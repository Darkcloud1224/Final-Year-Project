<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('home') }}" class="brand-link">
        <img src="https://assets.infyom.com/logo/blue_logo_150x150.png"
             alt="AdminLTE Logo"
             class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">SWAM-TNB</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if(Auth::user()->isAdmin())
                    @include('layouts.menu')
                @else
                    @include('layouts.staff-menu')
                @endif
            </ul>
        </nav>
    </div>
</aside>

<style>
    .main-sidebar {
        background-color: #000 !important; 
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link {
        color: #fff !important; 
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link:hover {
        background-color: #333 !important; 
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link.active {
        background-color: #555 !important; 
    }

    .main-sidebar .brand-link {
        border-bottom: 1px solid #4b4b4b; 
    }

    .main-sidebar .brand-link .brand-text {
        color: #fff !important; 
    }

    .main-sidebar .brand-link img {
        opacity: 0.8; 
    }
</style>
