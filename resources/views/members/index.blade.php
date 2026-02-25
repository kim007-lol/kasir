@extends('layouts.app')

@section('title', 'Kelola Member')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-people"></i> Kelola Member
        </h2>
        <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm fw-bold text-white">
            <i class="bi bi-plus-circle"></i> Tambah Member
        </a>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('members.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, telepon, atau alamat..." value="{{ $search ?? '' }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    @if(request('search'))
                    <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Reset</a>
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

    <div id="data-container">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #ff0000; color: white;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Member</th>
                            <th class="d-none d-md-table-cell">No Telepon</th>
                            <th class="d-none d-lg-table-cell">Alamat</th>
                            <th class="d-none d-md-table-cell">Bergabung</th>
                            <th class="d-none d-sm-table-cell">Total Transaksi</th>
                            <th class="d-none d-lg-table-cell">Akun Login</th>
                            <th class="d-none d-md-table-cell">Status</th>
                            <th style="width: 150px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $index => $member)
                        <tr>
                            <td>{{ $members->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $member->name }}</strong>
                            </td>
                            <td class="d-none d-md-table-cell">
                                {{ $member->phone ?? '-' }}
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small>{{ Str::limit($member->address ?? '-', 40) }}</small>
                            </td>
                            <td class="d-none d-md-table-cell">
                                {{ $member->created_at->format('d/m/Y') }}
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <strong class="text-primary">Rp. {{ number_format($member->transactions_sum_total ?? 0, 0, ',', '.') }}</strong>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($member->hasLoginAccount())
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Ada</span>
                                <br><small class="text-muted">{{ $member->user->username ?? '' }}</small>
                                @else
                                <span class="badge bg-secondary">Tidak Ada</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($member->deleted_at)
                                <span class="badge bg-danger">Nonaktif</span>
                                @else
                                <span class="badge bg-success">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center" role="group">
                                    @if($member->deleted_at)
                                    <form action="{{ route('members.restore', $member->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm text-white" title="Restore (Aktifkan Kembali)">
                                            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                        </button>
                                    </form>
                                    @else
                                    <a href="{{ route('members.edit', $member) }}" class="btn btn-warning btn-sm text-white" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('members.destroy', $member) }}" method="POST" style="display: inline;" onsubmit="confirmDelete(event, 'Data member ini akan dinonaktifkan!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm text-white" title="Nonaktifkan">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p>Belum ada member</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $members->withQueryString()->links() }}
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

        small {
            font-size: 0.75rem;
        }
    }

    .card {
        border-radius: 0.75rem;
    }

    .btn-group-sm .btn {
        border-radius: 0.375rem;
    }
</style>
@endsection