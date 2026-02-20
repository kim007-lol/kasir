<nav class="navbar navbar-dark sticky-top">
    <div class="container-fluid">
        <button class="toggle-sidebar-btn" id="toggleSidebarBtn" type="button" title="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand mb-0 h1" style="color: white;">
            <i class="bi bi-cash-register"></i>
            @auth
            SMEGABIZ - {{ auth()->user()->name }}
            @else
            SMEGABIZ
            @endauth
        </span>
        @auth
        <div class="ms-auto d-flex align-items-center gap-3">

            <span class="d-none d-md-inline text-light small">
                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
            </span>
        </div>
        @endauth
    </div>
</nav>