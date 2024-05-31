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
                @include('layouts.menu')
            </ul>
        </nav>
    </div>
</aside>

<style>
    .main-sidebar {
        background-color: #000 !important; /* Change sidebar background to black */
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link {
        color: #fff !important; /* Change link text color to white */
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link:hover {
        background-color: #333 !important; /* Change link hover background color to dark gray */
    }

    .main-sidebar .nav-sidebar > .nav-item > .nav-link.active {
        background-color: #555 !important; /* Change active link background color */
    }

    .main-sidebar .brand-link {
        border-bottom: 1px solid #4b4b4b; /* Optional: adds a bottom border to brand link */
    }

    .main-sidebar .brand-link .brand-text {
        color: #fff !important; /* Ensure the brand text is white */
    }

    .main-sidebar .brand-link img {
        opacity: 0.8; /* Optional: adjust the opacity of the logo image */
    }
</style>
