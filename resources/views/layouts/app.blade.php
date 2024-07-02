<x-laravel-ui-adminlte::adminlte-layout>

    <style>
        .dropdown-menu-lg {
        width: 300px;
        padding: 10px;
        }

        .user-header {
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .user-header p {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .user-header small {
            display: block;
            font-size: 0.875rem;
            margin-top: 5px;
            color: #f8f9fa;
        }

        .user-footer {
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-flat {
            border-radius: 0;
        }
    </style>

    <head>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <i class="nav-icon fas fa-user"></i>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <li class="user-header bg-primary text-center">
                                <img src="{{ asset('logo.png') }}" class="img-circle elevation-2 mb-3" alt="User Image" style="max-width: 100px;">
                                <p class="mb-0">
                                    {{ Auth::user()->name }}
                                </p>
                                <small>Member since {{ Auth::user()->created_at->format('M. Y') }}</small>
                            </li>
                            <li class="user-footer">
                                <a href="#" class="btn btn-danger btn-flat float-right" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Sign out
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>                              
                </ul>
            </nav>

            @include('layouts.sidebar')

            <div class="content-wrapper" style="background-color: #ffffff;">
                @yield('content')
            </div>

            <footer class="main-footer">
                <div class="float-right d-none d-sm-block">
                    <b>Version</b> 1.0.0
                </div>
                <strong>Copyright Tenaga Nasional Berhad &copy; 2021 <a href="https://www.tnb.com.my">TNB</a></strong> All rights
                reserved.
            </footer>
        </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">


        
    </body>
</x-laravel-ui-adminlte::adminlte-layout>
