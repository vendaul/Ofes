<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'OFES') }} - Faculty Evaluation System</title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-bg: #ecf0f1;
            --border-color: #bdc3c7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            font-size: 1.6rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-2px);
        }

        .container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .card {
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            color: #fff;
            padding: 25px;
            border: none;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body {
            padding: 30px;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn {
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #2980b9 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
            color: #fff;
        }

        .btn-secondary {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }

        .btn-secondary:hover {
            background-color: #d5dbdb;
            color: var(--primary-color);
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #7f8c8d !important;
        }

        a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .footer {
            background-color: rgba(0, 0, 0, 0.05);
            padding: 20px;
            text-align: center;
            color: var(--primary-color);
            font-size: 0.9rem;
            margin-top: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .invalid-feedback {
            display: block;
            color: var(--accent-color);
            font-size: 0.875rem;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .card {
                margin: 0 10px;
            }

            .card-body {
                padding: 20px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-graduation-cap"></i>
                {{ config('app.name', 'OFES') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('choose.role') }}">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-circle"></i> Errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'OFES') }} - Faculty Evaluation System. All rights reserved.</p>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
