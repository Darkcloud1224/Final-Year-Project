<x-laravel-ui-adminlte::adminlte-layout>

    <style>
        .login-box {
            width: 360px;
            margin: 7% auto;
        }

        .login-logo a {
            color: #333;
            font-size: 2rem;
            text-decoration: none;
            font-weight: bold;
        }

        .login-card-body {
            padding: 2rem;
        }

        .input-group-text {
            background-color: #f4f4f4;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .footer {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #666;
        }
    </style>

    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="text-center mb-4">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="max-width: 100px;">
            </div>
            
            <div class="card">
                <div class="card-body login-card-body">
                    <div class="login-logo">
                        <a href="{{ url('/home') }}"><b>SWAM-TNB</b></a>
                    </div>
                    <p class="login-box-msg">Sign in to start your session</p>

                    <form method="post" action="{{ url('/login') }}">
                        @csrf

                        <div class="input-group mb-3">
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                                class="form-control @error('email') is-invalid @enderror">
                            <div class="input-group-append">
                                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                            </div>
                            @error('email')
                                <span class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" name="password" placeholder="Password"
                                class="form-control @error('password') is-invalid @enderror">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                            @error('password')
                                <span class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="remember">
                                    <label for="remember">Remember Me</label>
                                </div>
                            </div>

                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="footer">
            &copy; 2024 SWAM-TNB. All rights reserved.
        </div>
    </body>
</x-laravel-ui-adminlte::adminlte-layout>
