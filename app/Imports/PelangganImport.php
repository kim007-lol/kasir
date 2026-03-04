<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PelangganImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $linked = 0;
    public int $skipped = 0;
    public array $errors = [];

    /**
     * Minimum username/password length (must match validation in UserController/RegisterController)
     */
    private const MIN_USERNAME_LENGTH = 5;
    private const MIN_PASSWORD_LENGTH = 5;

    public function collection(Collection $rows)
    {
        // Track usernames within this import batch to prevent duplicates
        $batchUsernames = [];
        $batchPhones = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because row 1 is header

            $nama  = trim($row['nama'] ?? '');
            $phone = trim($row['no_telepon'] ?? $row['no telepon'] ?? '');

            // ═══════════════════════════════════════════
            // VALIDASI 1: Data tidak boleh kosong
            // ═══════════════════════════════════════════
            if (!$nama || !$phone) {
                $this->errors[] = "Baris {$rowNum}: Data tidak lengkap (nama dan no_telepon wajib diisi).";
                $this->skipped++;
                continue;
            }

            // ═══════════════════════════════════════════
            // VALIDASI 2: Nama minimal 2 karakter
            // ═══════════════════════════════════════════
            if (mb_strlen($nama) < 2) {
                $this->errors[] = "Baris {$rowNum}: Nama '{$nama}' terlalu pendek (minimal 2 karakter).";
                $this->skipped++;
                continue;
            }

            // ═══════════════════════════════════════════
            // VALIDASI 3: Nama maksimal 255 karakter
            // ═══════════════════════════════════════════
            if (mb_strlen($nama) > 255) {
                $this->errors[] = "Baris {$rowNum}: Nama terlalu panjang (maksimal 255 karakter).";
                $this->skipped++;
                continue;
            }

            // ═══════════════════════════════════════════
            // VALIDASI 4: Format nomor telepon
            // ═══════════════════════════════════════════
            // Bersihkan karakter non-digit kecuali + di awal
            $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
            // Pastikan hanya angka (dan optional + di awal)
            if (!preg_match('/^\+?[0-9]{8,15}$/', $cleanPhone)) {
                $this->errors[] = "Baris {$rowNum}: Nomor telepon '{$phone}' tidak valid (harus 8-15 digit angka).";
                $this->skipped++;
                continue;
            }
            $phone = $cleanPhone;

            // ═══════════════════════════════════════════
            // VALIDASI 5: Cek duplikasi telepon dalam batch
            // ═══════════════════════════════════════════
            if (in_array($phone, $batchPhones)) {
                $this->errors[] = "Baris {$rowNum}: Nomor telepon '{$phone}' duplikat dalam file import.";
                $this->skipped++;
                continue;
            }
            $batchPhones[] = $phone;

            // ═══════════════════════════════════════════
            // GENERATE USERNAME (aman & konsisten)
            // ═══════════════════════════════════════════
            $username = $this->generateUsername($nama, $batchUsernames);

            // ═══════════════════════════════════════════
            // VALIDASI 6: Username harus valid setelah generate
            // ═══════════════════════════════════════════
            if (!$username || strlen($username) < self::MIN_USERNAME_LENGTH) {
                $this->errors[] = "Baris {$rowNum}: Gagal membuat username dari nama '{$nama}'. Username hasil generate terlalu pendek.";
                $this->skipped++;
                continue;
            }

            // Track username in batch
            $batchUsernames[] = $username;

            // ═══════════════════════════════════════════
            // GENERATE PASSWORD (aman & konsisten)
            // ═══════════════════════════════════════════
            $password = $this->generatePassword($username);

            // ═══════════════════════════════════════════
            // GENERATE EMAIL (unik)
            // ═══════════════════════════════════════════
            $email = $this->generateUniqueEmail($username);

            // ═══════════════════════════════════════════
            // PROSES INSERT (dalam transaction per-row)
            // ═══════════════════════════════════════════
            try {
                DB::transaction(function () use ($nama, $phone, $username, $password, $email, $rowNum) {
                    // Buat user
                    $user = User::create([
                        'name'     => $nama,
                        'username' => $username,
                        'email'    => $email,
                        'password' => Hash::make($password),
                        'role'     => 'pelanggan',
                    ]);

                    // Cek member yang sudah ada (nama + phone sama)
                    $existingMember = Member::withTrashed()
                        ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($nama))])
                        ->where('phone', $phone)
                        ->first();

                    if ($existingMember) {
                        if ($existingMember->user_id && $existingMember->user_id !== $user->id) {
                            // Member sudah punya akun lain → tetap buat user tapi tanpa link member
                            $this->errors[] = "Baris {$rowNum}: Member '{$nama}' ({$phone}) sudah punya akun lain. User dibuat tanpa link member.";
                            $this->imported++;
                            return;
                        }
                        $existingMember->restore();
                        $existingMember->update(['user_id' => $user->id]);
                        $this->linked++;
                    } else {
                        Member::create([
                            'name'    => $nama,
                            'phone'   => $phone,
                            'address' => '-',
                            'user_id' => $user->id,
                        ]);
                    }

                    $this->imported++;
                });
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNum}: Gagal menyimpan data - " . $e->getMessage();
                $this->skipped++;
            }
        }
    }

    /**
     * Generate username yang aman dari nama pelanggan.
     *
     * Rules:
     * - Ambil kata pertama dari nama, lowercase, hapus karakter non-alfanumerik
     * - Jika terlalu pendek (< MIN_USERNAME_LENGTH), ambil lebih banyak kata dari nama
     * - Jika masih pendek, pad dengan angka
     * - Pastikan unique di database DAN dalam batch import saat ini
     */
    private function generateUsername(string $nama, array $batchUsernames): string
    {
        // Langkah 1: Bersihkan dan ambil kata-kata dari nama
        $words = preg_split('/\s+/', mb_strtolower(trim($nama)));
        $cleanWords = [];
        foreach ($words as $word) {
            $clean = preg_replace('/[^a-z0-9]/', '', $word);
            if (!empty($clean)) {
                $cleanWords[] = $clean;
            }
        }

        // Jika tidak ada karakter valid sama sekali, gunakan fallback
        if (empty($cleanWords)) {
            $base = 'user';
        } else {
            // Langkah 2: Mulai dari kata pertama, tambah kata berikutnya jika masih pendek
            $base = '';
            foreach ($cleanWords as $word) {
                $base .= $word;
                if (strlen($base) >= self::MIN_USERNAME_LENGTH) {
                    break;
                }
            }
        }

        // Langkah 3: Pad dengan angka jika masih pendek
        if (strlen($base) < self::MIN_USERNAME_LENGTH) {
            $base = str_pad($base, self::MIN_USERNAME_LENGTH, '0', STR_PAD_RIGHT);
        }

        // Langkah 4: Pastikan username unik (cek DB + batch saat ini)
        $username = $base;
        $counter = 1;
        while (
            User::withTrashed()->where('username', $username)->exists() ||
            in_array($username, $batchUsernames)
        ) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Generate password dari username.
     *
     * Rules:
     * - Format: username + '123'
     * - Minimum panjang: MIN_PASSWORD_LENGTH karakter
     * - Jika masih pendek setelah tambah '123', pad dengan angka tambahan
     */
    private function generatePassword(string $username): string
    {
        $password = $username . '123';

        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $password = str_pad($password, self::MIN_PASSWORD_LENGTH, '45', STR_PAD_RIGHT);
        }

        return $password;
    }

    /**
     * Generate email unik untuk user baru.
     */
    private function generateUniqueEmail(string $username): string
    {
        $baseEmail = $username . '@smegabiz.local';
        $email = $baseEmail;
        $counter = 1;

        while (User::withTrashed()->where('email', $email)->exists()) {
            $email = $username . $counter . '@smegabiz.local';
            $counter++;
        }

        return $email;
    }
}
