<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Evaluation System - Select Login Type</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .role-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 50px 30px;
            max-width: 600px;
            width: 100%;
        }

        .role-header {
            margin-bottom: 40px;
            text-align: center;
        }

        .role-header h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .role-header p {
            color: #666;
            font-size: 16px;
        }

        .role-buttons {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        @media (min-width: 576px) {
            .role-buttons {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        .role-btn {
            padding: 20px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            color: white;
        }

        .role-btn i {
            font-size: 32px;
        }

        .role-btn-student {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .role-btn-student:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .role-btn-instructor {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .role-btn-instructor:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(245, 87, 108, 0.4);
            color: white;
        }

        .role-btn-admin {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .role-btn-admin:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(79, 172, 254, 0.4);
            color: white;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 14px;
        }

    </style>
</head>
<body>
    <div class="role-container">
        <div class="role-header">
            <h1><i class="fas fa-graduation-cap"></i> Faculty Evaluation System</h1>
            <p>Select your role to proceed</p>
        </div>

        <div class="role-buttons">
            <a href="<?php echo e(route('student.login')); ?>" class="role-btn role-btn-student">
                <i class="fas fa-user-graduate"></i>
                <span>Student</span>
            </a>
            <a href="<?php echo e(route('instructor.login')); ?>" class="role-btn role-btn-instructor">
                <i class="fas fa-chalkboard-user"></i>
                <span>Instructor</span>
            </a>
            <a href="<?php echo e(route('admin.login')); ?>" class="role-btn role-btn-admin">
                <i class="fas fa-shield-halved"></i>
                <span>Admin</span>
            </a>
        </div>

        <div class="footer-text">
            <p>&copy; 2025 Faculty Evaluation System. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/auth/choose-role.blade.php ENDPATH**/ ?>