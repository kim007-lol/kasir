@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-person-gear"></i> Kelola User
        </h2>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('users.pelanggan.template') }}" class="btn btn-outline-success btn-sm fw-bold">
                <i class="bi bi-download"></i> Template Excel
            </a>
            <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload"></i> Import Pelanggan
            </button>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm fw-bold text-white">
                <i class="bi bi-plus-circle"></i> Tambah User
            </a>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('users.pelanggan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel"><i class="bi bi-file-earmark-excel"></i> Import Pelanggan dari Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 mb-3" style="font-size: 0.85rem;">
                            <i class="bi bi-lightbulb-fill me-1"></i>
                            Download <strong>Template Excel</strong> terlebih dahulu, isi data pelanggan, lalu upload di sini.
                            Format kolom: <code>nama</code>, <code>no_telepon</code>. <br>
                            <em>(Username dan kata sandi otomatis dibuat dari nama depan pelanggan)</em>
                        </div>
                        <div class="mb-3">
                            <label for="importFile" class="form-label fw-bold">Pilih File Excel</label>
                            <input type="file" class="form-control" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Format: .xlsx, .xls, atau .csv (maks 2 MB)</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, username, atau email..." value="{{ $search ?? '' }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    @if(request('search'))
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div id="data-container">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #ff0000; color: white;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th class="d-none d-md-table-cell">Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="d-none d-md-table-cell">Terdaftar</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>
                                <code>{{ $user->username }}</code>
                                @if($user->role === 'pelanggan')
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle"></i> Default Pass: <code>{{ $user->username }}123</code> (min 8 char)
                                </small>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                {{ $user->email }}
                            </td>
                            <td>
                                @if($user->role === 'admin')
                                <span class="badge bg-danger">Admin</span>
                                @elseif($user->role === 'kasir')
                                <span class="badge bg-primary">Kasir</span>
                                @elseif($user->role === 'pelanggan')
                                <span class="badge bg-success">Pelanggan</span>
                                @else
                                <span class="badge bg-secondary">{{ $user->role ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($user->deleted_at)
                                <span class="badge bg-danger">Nonaktif</span>
                                @else
                                <span class="badge bg-success">Aktif</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($user->deleted_at)
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Restore (Aktifkan Kembali)">
                                            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                        </button>
                                    </form>
                                    @else
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="confirmDelete(event, 'Yakin ingin menonaktifkan pengguna {{ $user->name }} ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Nonaktifkan">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p>Belum ada user</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>

<style>
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        table {
            font-size: 0.8rem;
        }

        th,
        td {
            padding: 0.6rem 0.4rem !important;
        }

        .btn-sm {
            padding: 0.15rem 0.3rem;
            font-size: 0.75rem;
        }
    }

    .card {
        border-radius: 0.75rem;
    }
</style>
@endsection