<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'OFES')); ?> - Admin Panel</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>">
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
            background-color: #f8f9fa;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            color: #fff;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.3rem;
            color: #fff;
        }

        .sidebar-header small {
            color: rgba(255, 255, 255, 0.7);
            display: block;
            margin-top: 5px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0 10px;
        }

        .sidebar-nav .nav-item {
            margin-bottom: 5px;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            padding-left: 20px;
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            text-align: center;
        }

        .sidebar-nav .nav-link.active {
            background-color: var(--secondary-color);
            color: #fff;
            font-weight: 600;
        }

        .sidebar-nav .nav-link.sub-link {
            margin-left: 28px;
            width: calc(100% - 28px);
            font-size: 0.95rem;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .sidebar-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
            margin: 15px 0;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
            border-bottom: 3px solid var(--secondary-color);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.3rem;
        }

        .navbar-text {
            color: var(--primary-color);
            font-weight: 500;
        }

        /* custom settings dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* SETTINGS ICON */
        .settings-icon {
            font-size: 22px;
            cursor: pointer;
            color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .settings-icon:hover {
            color: var(--secondary-color);
            transform: rotate(30deg);
        }

        /* DROPDOWN UI IMPROVEMENT */
        .custom-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background: #fff;
            min-width: 240px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            overflow: hidden;
            animation: fadeDropdown 0.2s ease;
        }

        /* HEADER */
        .custom-dropdown .dropdown-header {
            padding: 12px 16px;
            background: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
        }

        .custom-dropdown .dropdown-header small {
            display: block;
            color: #777;
            font-size: 0.75rem;
        }

        /* ITEMS */
        .custom-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            text-decoration: none;
            color: #333;
            transition: all 0.2s ease;
        }

        .custom-dropdown a i {
            width: 20px;
            text-align: center;
        }

        /* HOVER EFFECT */
        .custom-dropdown a:hover {
            background-color: #f4f6f8;
            padding-left: 20px;
        }

        /* DIVIDER */
        .custom-dropdown .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 5px 0;
        }

        /* ANIMATION */
        @keyframes fadeDropdown {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }


        .content {
            flex: 1;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--light-bg);
        }

        .page-header h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0;
        }

        .page-header p {
            color: #7f8c8d;
            margin: 5px 0 0 0;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .btn-danger {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--light-bg);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            color: var(--primary-color);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #000000;
            font-weight: 700;
            border-bottom: 2px solid var(--border-color);
            padding: 15px;
        }

        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            border-color: #e0e0e0;
            color: #333333;
        }

        .table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-buttons .btn {
            padding: 6px 12px;
            font-size: 0.875rem;
        }

        .badge {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 10px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .alert {
            border-radius: 5px;
            border: none;
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

        .dropdown-menu {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 5px;
        }

        .dropdown-item:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }

        .logout-btn {
            margin-top: auto;
            border-top: 2px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
        }

        /* basic slide effect for sidebar toggling */
        .sidebar {
            transition: transform 0.25s ease;
            z-index: 1050;
            /* above content when overlaying */
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        @media (max-width: 992px) {

            /* hide sidebar initially on small screens */
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                width: 250px;
                transform: translateX(-100%);
                background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content {
                padding: 15px;
            }

            .sidebar-nav {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }

            .sidebar-nav .nav-link {
                flex: 1;
                min-width: 150px;
                text-align: center;
            }
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-graduation-cap"></i> OFES</h4>
            <small>Admin Panel</small>
        </div>

        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('admin.area_college')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.area_college') ? 'active' : ''); ?>">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Areas & Colleges</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('instructors.index')); ?>" class="nav-link <?php echo e(request()->routeIs('instructors.*') ? 'active' : ''); ?>">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>Faculty</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('subjects.index')); ?>" class="nav-link <?php echo e(request()->routeIs('subjects.*') ? 'active' : ''); ?>">
                    <i class="fas fa-book"></i>
                    <span>Subjects</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo e(route('sections.index')); ?>" class="nav-link sub-link <?php echo e(request()->routeIs('sections.*') ? 'active' : ''); ?>">
                    <i class="fas fa-layer-group"></i>
                    <span>Sections</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo e(route('questions.index')); ?>" class="nav-link <?php echo e(request()->routeIs('questions.*') ? 'active' : ''); ?>">
                    <i class="fas fa-question-circle"></i>
                    <span>Evaluation</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-divider"></div>

        <div class="sidebar-nav logout-btn">
            <li class="nav-item">
                <a href="<?php echo e(route('logout')); ?>" class="nav-link logout-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            </li>
        </div>
    </div>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-lg-none me-2" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-text">
                    <i class="fas fa-home"></i> Dashboard
                </span>
                <div class="ms-auto d-flex align-items-center" style="gap:.75rem;">
                    <?php if(auth()->guard()->check()): ?>
                    <span class="navbar-text">
                        <i class="fas fa-user-circle"></i> <?php echo e(Auth::user()->name ?? 'Admin'); ?>

                    </span>
                    <?php endif; ?>

                    <!-- settings dropdown -->
                    <div class="dropdown">
                        <i class="fas fa-cog settings-icon" onclick="toggleDropdown()"></i>

                        <div id="myDropdown" class="dropdown-content custom-dropdown">

                            <!-- User Header -->
                            <div class="dropdown-header">
                                <strong><?php echo e(Auth::user()->name ?? 'Admin'); ?></strong>
                                <small>Administrator</small>
                            </div>

                            <!-- Menu Items -->
                            <a href="<?php echo e(route('settings.accounts')); ?>">
                                <i class="fas fa-users text-primary"></i>
                                <span>Manage Supervisors</span>
                            </a>

                            <a href="<?php echo e(route('settings.myAccount')); ?>">
                                <i class="fas fa-user-cog text-success"></i>
                                <span>Manage My Account</span>
                            </a>

                            <a href="<?php echo e(route('settings.semester')); ?>">
                                <i class="fas fa-calendar-alt text-warning"></i>
                                <span>Semester / School Year</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <a href="<?php echo e(route('logout')); ?>" class="logout-link text-danger">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </nav>

        <script>
            // sidebar toggling for mobile
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('show');
                });
                // clicking outside sidebar will close it
                document.addEventListener('click', e => {
                    if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                });
            }

        </script>

        <div class="content">
            <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-circle"></i> Errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>

            <!-- dropdown script -->
            <script>
                function toggleDropdown() {
                    var dd = document.getElementById('myDropdown');
                    if (dd.style.display === 'block') dd.style.display = 'none';
                    else dd.style.display = 'block';
                }

                window.onclick = function(event) {
                    if (!event.target.closest('.dropdown')) {
                        var dd = document.getElementById('myDropdown');
                        if (dd) dd.style.display = 'none';
                    }
                }

                // global confirmation for logout and delete actions
                document.addEventListener('DOMContentLoaded', function() {
                    // logout confirmation
                    document.querySelectorAll('a.logout-link').forEach(function(link) {
                        link.addEventListener('click', function(event) {
                            event.preventDefault();
                            var message = 'Are you sure you want to log out?';
                            if (confirm(message)) {
                                var logoutForm = document.getElementById('logout-form');
                                if (logoutForm) {
                                    logoutForm.submit();
                                } else {
                                    window.location.href = link.href;
                                }
                            }
                        });
                    });

                    // delete confirmation for forms using delete method
                    document.querySelectorAll('form').forEach(function(form) {
                        var deleteMethod = form.querySelector('input[name="_method"][value="DELETE"]');
                        if (deleteMethod) {
                            var onsubmitAttr = form.getAttribute('onsubmit') || '';
                            var hasInlineConfirm = /confirm\s*\(/i.test(onsubmitAttr);

                            // If a form already defines its own confirm dialog, avoid double prompts.
                            if (hasInlineConfirm) {
                                return;
                            }

                            form.addEventListener('submit', function(e) {
                                if (!confirm('Are you sure you want to delete this item?')) {
                                    e.preventDefault();
                                }
                            });
                        }
                    });
                });

            </script>
        </div>
        <?php echo $__env->yieldPushContent('scripts'); ?>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes/resources/views/layouts/admin.blade.php ENDPATH**/ ?>