<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h5><i class="bi bi-cash-register"></i> SMEGABIZ</h5>
    </div>
    <nav>
        <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) active @endif">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('warehouse.index') }}" class="@if(request()->routeIs('warehouse.*')) active @endif">
            <i class="bi bi-building"></i>
            <span>Gudang</span>
        </a>
        <a href="{{ route('suppliers.index') }}" class="@if(request()->routeIs('suppliers.*')) active @endif">
            <i class="bi bi-truck"></i>
            <span>Supplier</span>
        </a>
        <a href="{{ route('categories.index') }}" class="@if(request()->routeIs('categories.*')) active @endif">
            <i class="bi bi-list"></i>
            <span>Kategori</span>
        </a>
        <a href="{{ route('cashier-items.index') }}" class="@if(request()->routeIs('cashier-items.*')) active @endif">
            <i class="bi bi-cart-check"></i>
            <span>Stok Item Kasir</span>
        </a>

        <a href="{{ route('history.index') }}" class="@if(request()->routeIs('history.*')) active @endif">
            <i class="bi bi-clock-history"></i>
            <span>Histori Transaksi</span>
        </a>
        <a href="{{ route('members.index') }}" class="@if(request()->routeIs('members.*')) active @endif">
            <i class="bi bi-people"></i>
            <span>Member</span>
        </a>
        <a href="{{ route('reports.index') }}" class="@if(request()->routeIs('reports.*')) active @endif">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Laporan</span>
        </a>
    </nav>
    <hr class="bg-secondary my-3">
    <div class="logout-btn">
        <form action="{{ route('logout') }}" method="POST" class="w-100">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-md-inline">Logout</span>
            </button>
        </form>
    </div>
</div>