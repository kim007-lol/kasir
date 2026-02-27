@php
$routePrefix = (auth()->check() && auth()->user()->role === 'kasir') ? 'cashier.' : '';
$layout = $routePrefix ? 'layouts.cashier' : 'layouts.app';
@endphp
@extends($layout)

@section('title', 'Supplier')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-truck"></i> Data Supplier
        </h2>
        <a href="{{ route($routePrefix . 'suppliers.create') }}" class="btn btn-primary text-white fw-bold">
            <i class="bi bi-plus-circle"></i> Tambah Supplier
        </a>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($routePrefix . 'suppliers.index') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label fw-semibold">Cari Supplier</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Cari berdasarkan nama, telepon, email, atau alamat...">
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if(isset($search) && $search)
                        <a href="{{ route($routePrefix . 'suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="data-container">
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #ff0000; color: white;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Supplier</th>
                            <th>No Telp</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>Tanggal Kontrak</th>
                            <th style="width: 180px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $index => $supplier)
                        <tr class="{{ $supplier->trashed() ? 'table-secondary text-muted' : '' }}">
                            <td>{{ $suppliers->firstItem() + $index }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                            <td>{{ $supplier->email ?? '-' }}</td>
                            <td>{{ Str::limit($supplier->address ?? '-', 50) }}</td>
                            <td>
                                @if($supplier->trashed())
                                <span class="badge bg-danger">Off Kontrak</span>
                                @else
                                {{ $supplier->contract_date ? $supplier->contract_date->format('d/m/Y') : '-' }}
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center" role="group">
                                    <a href="{{ route($routePrefix . 'suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm text-white" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($supplier->trashed())
                                    <form action="{{ route($routePrefix . 'suppliers.restore', $supplier->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm text-white" title="Aktifkan">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route($routePrefix . 'suppliers.destroy', $supplier) }}" method="POST" style="display:inline;" onsubmit="confirmDelete(event, 'Data supplier ini akan dihapus permanently!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm text-white" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
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
        <div class="mt-3">
            {{ $suppliers->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection