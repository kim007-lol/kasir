@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-speedometer2"></i> Dashboard
    </h2>

    <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card shadow-sm border-0 h-100" style="border-top: 4px solid #ff6b6b;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total Produk</p>
                            <h2 class="mb-0" style="color: #ff6b6b;">{{ $totalItems }}</h2>
                        </div>
                        <i class="bi bi-box" style="font-size: 2.5rem; color: #ff6b6b; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card shadow-sm border-0 h-100" style="border-top: 4px solid #ff8a80;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total Transaksi</p>
                            <h2 class="mb-0" style="color: #ff8a80;">{{ $totalTransactions }}</h2>
                        </div>
                        <i class="bi bi-receipt" style="font-size: 2.5rem; color: #ff8a80; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Metrics -->
    <div class="row g-3 mt-4">
        <div class="col-12">
        </div>
        <div class="col-12 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-top: 4px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Produk Terjual Hari Ini</p>
                            <h2 class="mb-0" style="color: #28a745;">{{ $totalProductsSoldToday }}</h2>
                        </div>
                        <i class="bi bi-cart-check" style="font-size: 2.5rem; color: #28a745; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="card shadow-sm border-0 h-100" style="border-top: 4px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Pendapatan Hari Ini</p>
                            <h2 class="mb-0" style="color: #ffc107;">Rp. {{ number_format($totalRevenueToday, 0, ',', '.') }}</h2>
                        </div>
                        <i class="bi bi-cash-stack" style="font-size: 2.5rem; color: #ffc107; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mt-4">
        <div class="col-12">
            <h5 class="fw-bold mb-3">Quick Access</h5>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('transactions.index') }}" class="btn btn-primary w-100 py-3 fw-bold text-white">
                <i class="bi bi-plus-circle"></i> Buat Transaksi
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('warehouse.index') }}" class="btn btn-info w-100 py-3 fw-bold text-white">
                <i class="bi bi-box"></i> Lihat Produk
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="{{ route('history.index') }}" class="btn btn-secondary w-100 py-3 fw-bold text-white">
                <i class="bi bi-clock-history"></i> History
            </a>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="row g-3 mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-graph-up"></i> Grafik Penjualan 7 Hari Terakhir</h5>
                    <canvas id="salesChart" height="80" data-sales="{{ json_encode($salesChart) }}"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const salesChartCanvas = document.getElementById('salesChart');
    const salesData = JSON.parse(salesChartCanvas.dataset.sales);

    // Prepare data for chart
    const dates = salesData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short'
        });
    });
    const counts = salesData.map(item => item.count);
    const totals = salesData.map(item => item.total);

    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: counts,
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255, 107, 107, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y'
            }, {
                label: 'Total Pendapatan (Rp)',
                data: totals,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.datasetIndex === 1) {
                                label += 'Rp. ' + context.parsed.y.toLocaleString('id-ID');
                            } else {
                                label += context.parsed.y;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Jumlah Transaksi'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Pendapatan (Rp)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
</script>


<style>
    .card {
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .btn {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 576px) {
        h2 {
            font-size: 1.5rem;
        }

        .card-body {
            padding: 1rem;
        }

        h2.mb-0 {
            font-size: 1.75rem;
        }
    }
</style>
@endsection