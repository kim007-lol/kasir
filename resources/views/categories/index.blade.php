@extends('layouts.app')

@section('title', 'Item Kategori')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-list"></i> Item Kategori
        </h2>
        <a href="{{ route('categories.create') }}" class="btn text-white fw-bold" style="background-color: #5b9dd9;">
            <i class="bi bi-plus-circle"></i> Tambah Kategori
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background-color: #ff6b6b; color: white;">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Kategori</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $index => $category)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            <div class="btn-group btn-group-sm px-2 py-1 " role="group">
                                <a href="{{ route('categories.edit', $category) }}" class="btn" style="background-color: #ed8936; color: white; border-color: #ed8936;" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                    <span class="d-none d-md-inline">Edit</span>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn" style="background-color: #f56565; color: white; border-color: #f56565;" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                        <span class="d-non
                                            e d-md-inline">Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p>Tidak ada data kategori</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .btn-group-sm .btn {
        padding: 0.35rem 0.5rem;
        font-size: 0.85rem;
    }

    @media (max-width: 576px) {
        .btn-group-sm .btn {
            padding: 0.4rem 0.35rem;
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