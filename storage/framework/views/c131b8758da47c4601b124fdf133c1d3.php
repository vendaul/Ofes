<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Faculty Evaluation System</title>
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

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-user-graduate"></i> Student Login</h1>
            <p>Faculty Evaluation System</p>
        </div>

        <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <strong>Login Failed!</strong>
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('student.login.submit')); ?>" id="student-login-form">
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label for="sid" class="form-label">Student ID</label>
                <div class="input-group">
                    <input type="text" id="sid" name="sid" class="form-control" placeholder="Enter student id" value="<?php echo e(old('sid')); ?>" required>
                    <button type="button" id="check-student" class="btn btn-secondary">Check</button>
                </div>
                <small id="student-status" class="form-text text-muted"></small>
            </div>

            <div class="mb-3" id="email-field" style="display:none;">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Email will appear after SID verification" readonly>
                <div class="invalid-feedback" id="email-feedback"></div>
            </div>

            <div class="mb-4" id="password-field" style="display:none;">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password">
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <button type="submit" class="btn btn-login w-100" id="student-login-btn" disabled>
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div class="mt-3" style="text-align:center;">
            <a href="<?php echo e(route('password.request')); ?>">Forgot your password?</a>
        </div>
        <div class="back-link">
            <a href="<?php echo e(route('choose.role')); ?>">
                <i class="fas fa-arrow-left"></i> Back to Role Selection
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('check-student').addEventListener('click', function() {
            const sidInput = document.getElementById('sid');
            const sid = sidInput.value.trim();
            const statusText = document.getElementById('student-status');
            const emailField = document.getElementById('email-field');
            const passwordField = document.getElementById('password-field');
            const loginBtn = document.getElementById('student-login-btn');
            const emailInput = document.getElementById('email');

            if (!sid) {
                statusText.innerText = 'Please enter a student ID first.';
                statusText.className = 'form-text text-danger';
                return;
            }

            statusText.innerText = 'Checking student...';
            statusText.className = 'form-text text-muted';

            fetch(`<?php echo e(url('/student/check')); ?>/${encodeURIComponent(sid)}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.found) {
                        statusText.innerText = 'Student ID not found. Please register first.';
                        statusText.className = 'form-text text-danger';
                        emailField.style.display = 'none';
                        passwordField.style.display = 'none';
                        loginBtn.disabled = true;
                        return;
                    }

                    if (!data.email) {
                        const registerUrl = '<?php echo e(route("student.register.form")); ?>?sid=' + encodeURIComponent(sid);
                        statusText.innerHTML = 'Student found. Email not assigned yet. <a href="' + registerUrl + '">Complete registration</a> or use <a href="<?php echo e(route("student.login")); ?>">Student Login</a> after registration.';
                        emailField.style.display = 'none';
                        passwordField.style.display = 'none';
                        loginBtn.disabled = true;

                        // Add explicit action button for users who are blocked on no email yet.
                        let directAction = document.getElementById('student-register-action');
                        if (!directAction) {
                            directAction = document.createElement('a');
                            directAction.id = 'student-register-action';
                            directAction.href = registerUrl;
                            directAction.className = 'btn btn-warning w-100 mt-2';
                            directAction.innerHTML = '<i class="fas fa-user-plus"></i> Complete Registration';
                            document.querySelector('form').appendChild(directAction);
                        } else {
                            directAction.href = registerUrl;
                        }
                        return;
                    }

                    statusText.innerText = 'Student verified. Continue with password.';
                    statusText.className = 'form-text text-success';
                    emailField.style.display = 'block';
                    passwordField.style.display = 'block';
                    emailInput.value = data.email;
                    loginBtn.disabled = false;
                })
                .catch(() => {
                    statusText.innerText = 'Unable to verify student right now. Try again later.';
                    statusText.className = 'form-text text-danger';
                });
        });

        // Prevent submitting without SID check
        document.getElementById('student-login-form').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('student-login-btn');
            if (loginBtn.disabled) {
                e.preventDefault();
            }
        });

    </script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/auth/student-login.blade.php ENDPATH**/ ?>