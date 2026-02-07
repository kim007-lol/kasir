@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-truck"></i> Data Supplier
        </h2>
        <a href="{{ route('suppliers.create') }}" class="btn text-white fw-bold" style="background-color: #5b9dd9;">
            <i class="bi bi-plus-circle"></i> Tambah Supplier
        </a>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('suppliers.index') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label fw-semibold">Cari Supplier</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama, telepon, atau email...">
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if(isset($search) && $search)
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background-color: #ff6b6b; color: white;">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Supplier</th>
                        <th>No Telp</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $index => $supplier)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>{{ Str::limit($supplier->address ?? '-', 50) }}</td>
                        <td>
                            <div class="btn-group btn-group-sm px-2 py-1" role="group">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn" style="background-color: #ed8936; color: white; border-color: #ed8936;" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                    <span class="d-none d-md-inline">Edit</span>
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn" style="background-color: #f56565; color: white; border-color: #f56565;" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                        <span class="d-none d-md-inline">Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p>Tidak ada data supplier</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection