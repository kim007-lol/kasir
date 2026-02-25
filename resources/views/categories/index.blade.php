@extends('layouts.app')

@section('title', 'Item Kategori')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-list"></i> Item Kategori
        </h2>
        <a href="{{ route('categories.create') }}" class="btn btn-primary text-white fw-bold">
            <i class="bi bi-plus-circle"></i> Tambah Kategori
        </a>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('categories.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari kategori..." value="{{ $search ?? '' }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    @if(request('search'))
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
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
                            <th>Nama Kategori</th>
                            <th>Status</th>
                            <th style="width: 180px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                        <tr class="{{ $category->trashed() ? 'table-secondary text-muted' : '' }}">
                            <td>{{ $categories->firstItem() + $index }}</td>
                            <td>{{ $category->name }}</td>
                            <td>
                                @if($category->trashed())
                                <span class="badge bg-danger">Non-Aktif</span>
                                @else
                                <span class="badge bg-success">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center" role="group">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm text-white" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($category->trashed())
                                    <form action="{{ route('categories.restore', $category->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm text-white" title="Aktifkan">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="confirmDelete(event, 'Kategori ini akan dihapus!')">
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
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p>Belum ada kategori</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $categories->withQueryString()->links() }}
        </div>
    </div>
</div>

<style>
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }

    @media (max-width: 576px) {
        .btn-sm {
            padding: 0.15rem 0.3rem;
            font-size: 0.75rem;
        }

        table {
            font-size: 0.85rem;
        }

        th,
        td {
            padding: 0.6rem 0.4rem !important;
        }
    }

    .table-responsive {
        border-radius: 0.5rem;
    }

    .card {
        border-radius: 0.75rem;
    }
</style>
@endsection