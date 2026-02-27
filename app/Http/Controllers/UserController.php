<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): View|string
    {
        $search = $request->get('search');
        $query = User::select('id', 'name', 'username', 'email', 'role', 'created_at', 'deleted_at')
            ->withTrashed()
            ->when($search, function ($query) use ($search) {
                $searchLower = '%' . mb_strtolower($search) . '%';
                $query->where(function ($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', [$searchLower])
                        ->orWhereRaw('LOWER(username) LIKE ?', [$searchLower])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$searchLower]);
                });
            })
            ->orderBy('created_at', 'desc');

        $users = $query->paginate(15);

        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = view('users.index', compact('users', 'search'));
            return $view->fragment('data-container');
        }

        return view('users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|min:5|max:255|unique:users,username',
            'password' => 'required|string|min:5|confirmed',
            'role'     => 'required|in:admin,kasir,pelanggan',
        ];
        if ($request->input('role') === 'pelanggan') {
            $rules['phone'] = 'required|string|max:20';
        }

        $validated = $request->validate($rules, [
            'name.required'      => 'Nama harus diisi',
            'username.required'  => 'Username harus diisi',
            'username.min'       => 'Username minimal 5 karakter',
            'username.unique'    => 'Username sudah digunakan',
            'password.required'  => 'Password harus diisi',
            'password.min'       => 'Password minimal 5 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required'      => 'Role harus dipilih',
            'role.in'            => 'Role tidak valid',
            'phone.required'     => 'Nomor telepon wajib diisi untuk akun pelanggan',
        ]);

        // Auto-generate a unique email based on username
        $baseEmail = $validated['username'] . '@smegabiz.local';
        $email = $baseEmail;
        $counter = 1;
        while (User::withTrashed()->where('email', $email)->exists()) {
            $email = $validated['username'] . $counter . '@smegabiz.local';
            $counter++;
        }

        try {
            DB::transaction(function () use ($validated, $email) {
                $user = User::create([
                    'name'     => $validated['name'],
                    'username' => $validated['username'],
                    'email'    => $email,
                    'password' => Hash::make($validated['password']),
                    'role'     => $validated['role'],
                ]);

                if ($validated['role'] === 'pelanggan') {
                    $phone = $validated['phone'];
                    $name  = $validated['name'];

                    // Cari member berdasarkan nama (case-insensitive) DAN nomor telepon
                    $existingMember = Member::withTrashed()
                        ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($name))])
                        ->where('phone', $phone)
                        ->first();

                    if ($existingMember) {
                        // Member sudah punya akun lain → tolak
                        if ($existingMember->user_id && $existingMember->user_id !== $user->id) {
                            throw new \Exception(
                                "Member dengan nama '{$name}' dan nomor '{$phone}' sudah memiliki akun login. Gunakan nama atau nomor yang berbeda."
                            );
                        }
                        // Tautkan user ke member yang sudah ada
                        $existingMember->restore();
                        $existingMember->update(['user_id' => $user->id]);
                    } else {
                        // Tidak ada cocok → buat member baru
                        Member::create([
                            'name'    => $name,
                            'phone'   => $phone,
                            'address' => '-',
                            'user_id' => $user->id,
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['phone' => $e->getMessage()]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }


    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:5|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:5|confirmed',
            'role' => 'required|in:admin,kasir,pelanggan',
        ], [
            'name.required' => 'Nama harus diisi',
            'username.required' => 'Username harus diisi',
            'username.min' => 'Username minimal 5 karakter',
            'username.unique' => 'Username sudah digunakan',
            'password.min' => 'Password minimal 5 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role harus dipilih',
            'role.in' => 'Role tidak valid',
        ]);

        $oldRole = $user->role;
        $newRole = $validated['role'];

        $updateData = [
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'role'     => $newRole,
        ];

        // Hanya update password jika diisi
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        DB::transaction(function () use ($user, $updateData, $oldRole, $newRole, $validated) {
            $user->update($updateData);

            // Jika role baru pelanggan dan belum punya member → buat member
            if ($newRole === 'pelanggan' && !$user->member()->withTrashed()->exists()) {
                Member::create([
                    'name'    => $validated['name'],
                    'phone'   => '-',
                    'address' => '-',
                    'user_id' => $user->id,
                ]);
            }

            // Jika role lama pelanggan dan diganti bukan pelanggan → hapus member
            if ($oldRole === 'pelanggan' && $newRole !== 'pelanggan') {
                $user->member()->delete();
            }

            // Sinkronkan nama member jika masih pelanggan
            if ($newRole === 'pelanggan' && $user->member) {
                $user->member->update(['name' => $validated['name']]);
            }
        });

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Cegah admin menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus (Nonaktif)');
    }

    public function restore($id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')->with('success', 'User berhasil dipulihkan (Aktif)');
    }

    public function exportTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PelangganTemplateExport(),
            'template-import-pelanggan.xlsx'
        );
    }

    public function importPelanggan(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File Excel wajib dipilih',
            'file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv',
            'file.max'      => 'Ukuran file maksimal 2 MB',
        ]);

        $import = new \App\Imports\PelangganImport();

        try {
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }

        $message = "Import selesai! {$import->imported} user dibuat";
        if ($import->linked > 0) {
            $message .= ", {$import->linked} ditautkan ke member yang sudah ada";
        }
        if ($import->skipped > 0) {
            $message .= ", {$import->skipped} dilewati";
        }
        $message .= '.';

        if (!empty($import->errors)) {
            $message .= ' Detail: ' . implode(' | ', array_slice($import->errors, 0, 5));
            if (count($import->errors) > 5) {
                $message .= ' ... dan ' . (count($import->errors) - 5) . ' error lainnya.';
            }
        }

        return redirect()->route('users.index')->with($import->skipped > 0 ? 'warning' : 'success', $message);
    }
}
