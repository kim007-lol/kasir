<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PelangganImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $linked = 0;
    public int $skipped = 0;
    public array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because row 1 is header

            $nama     = trim($row['nama'] ?? '');
            $phone    = trim($row['no_telepon'] ?? $row['no telepon'] ?? '');

            // Validasi dasar
            if (!$nama || !$phone) {
                $this->errors[] = "Baris {$rowNum}: Data tidak lengkap (nama/no_telepon wajib diisi).";
                $this->skipped++;
                continue;
            }

            // Generate username based on first name
            $firstName = strtolower(explode(' ', $nama)[0]);
            // Remove non-alphanumeric chars
            $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);
            if (empty($firstName)) {
                $firstName = 'user';
            }

            $username = $firstName;
            $counter = 1;
            // Ensure unique username
            while (User::withTrashed()->where('username', $username)->exists()) {
                $username = $firstName . $counter;
                $counter++;
            }

            // Generate password (e.g. username123), pastikan minimal 8 karakter
            $password = $username . '123';
            if (strlen($password) < 8) {
                $password = str_pad($password, 8, '456789', STR_PAD_RIGHT);
            }

            // Auto-generate email
            $baseEmail = $username . '@smegabiz.local';
            $email = $baseEmail;
            $counter = 1;
            while (User::withTrashed()->where('email', $email)->exists()) {
                $email = $username . $counter . '@smegabiz.local';
                $counter++;
            }

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
                    continue;
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
        }
    }
}
