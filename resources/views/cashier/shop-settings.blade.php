@extends('layouts.cashier')

@section('title', 'Pengaturan Toko')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-gear-fill"></i> Pengaturan Toko</h4>
            <a href="{{ route('cashier.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Current Status -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <h6 class="text-muted text-uppercase small fw-bold mb-2">Status Toko Saat Ini</h6>
                @if($isOpen)
                <span class="badge rounded-pill bg-success fs-5 px-4 py-2">
                    <i class="bi bi-shop"></i> BUKA
                </span>
                @else
                <span class="badge rounded-pill bg-danger fs-5 px-4 py-2">
                    <i class="bi bi-shop"></i> TUTUP
                </span>
                @endif

                @if($override)
                <div class="mt-2">
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-exclamation-triangle"></i> Mode Manual: {{ $override === 'open' ? 'Dipaksa Buka' : 'Dipaksa Tutup' }}
                    </span>
                </div>
                @else
                <p class="text-muted small mt-2 mb-0">Mengikuti jadwal otomatis</p>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <!-- Jam Operasional -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-clock"></i> Jam Operasional</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('cashier.settings.hours') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Jam Buka</label>
                                <input type="time" name="open_hour" value="{{ $openHour }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Jam Tutup</label>
                                <input type="time" name="close_hour" value="{{ $closeHour }}" class="form-control" required>
                            </div>

                            @if($errors->any())
                            <div class="alert alert-danger small py-2">
                                @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                                @endforeach
                            </div>
                            @endif

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Simpan Jadwal
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Toggle Manual -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-toggles"></i> Kontrol Manual</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">
                            Override jadwal otomatis. Berguna saat ada acara khusus atau tutup mendadak.
                        </p>

                        <div class="d-grid gap-2">
                            <form action="{{ route('cashier.settings.toggle') }}" method="POST">
                                @csrf
                                <input type="hidden" name="override" value="open">
                                <button type="submit" class="btn w-100 {{ $override === 'open' ? 'btn-success' : 'btn-outline-success' }}">
                                    <i class="bi bi-unlock-fill"></i> Paksa Buka
                                </button>
                            </form>

                            <form action="{{ route('cashier.settings.toggle') }}" method="POST">
                                @csrf
                                <input type="hidden" name="override" value="closed">
                                <button type="submit" class="btn w-100 {{ $override === 'closed' ? 'btn-danger' : 'btn-outline-danger' }}">
                                    <i class="bi bi-lock-fill"></i> Paksa Tutup
                                </button>
                            </form>

                            <form action="{{ route('cashier.settings.toggle') }}" method="POST">
                                @csrf
                                <input type="hidden" name="override" value="auto">
                                <button type="submit" class="btn w-100 {{ !$override ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                    <i class="bi bi-arrow-repeat"></i> Kembali ke Otomatis
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection