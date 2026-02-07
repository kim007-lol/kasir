@extends('layouts.app')

@section('title', 'Detail Member')

@section('content')
<div class="mb-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2 class="fw-bold mb-0">
            <i class="bi bi-person-vcard"></i> Detail Member
        </h2>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted">Nama Member</label>
                </div>
                <div class="col-md-9">
                    <p class="form-control-plaintext fw-bold">{{ $member->name }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted">No Telepon</label>
                </div>
                <div class="col-md-9">
                    <p class="form-control-plaintext">{{ $member->phone ?? '-' }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted">Alamat</label>
                </div>
                <div class="col-md-9">
                    <p class="form-control-plaintext">{{ $member->address ?? '-' }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted">Terdaftar Sejak</label>
                </div>
                <div class="col-md-9">
                    <p class="form-control-plaintext">{{ $member->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <hr>

            <div class="d-flex gap-2">
                <a href="{{ route('members.edit', $member) }}" class="btn fw-bold text-white" style="background-color: #5b9dd9;">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('members.destroy', $member) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus member ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 0.75rem;
    }

    .form-control-plaintext {
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
    }

    @media (max-width: 576px) {
        h2 {
            font-size: 1.25rem;
        }

        .btn {
            padding: 0.35rem 0.75rem;
            font-size: 0.9rem;
        }

        .row {
            flex-direction: column;
        }

        .col-md-3 {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endsection
