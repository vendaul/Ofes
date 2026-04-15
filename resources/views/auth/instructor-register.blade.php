<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Registration Completion - Faculty Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 430px;
            width: 100%;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .register-header p {
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
            border-color: #f5576c;
            box-shadow: 0 0 0 0.2rem rgba(245, 87, 108, 0.25);
        }

        .btn-submit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            color: white;
        }

        .btn-submit:hover {
            color: white;
            opacity: 0.95;
        }

    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1><i class="fas fa-user-plus"></i> Complete Instructor Registration</h1>
            <p>Set your login email and password</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('instructor.register') }}">
            @csrf

            <div class="mb-3">
                <label for="empid" class="form-label">Employee ID</label>
                <input type="text" id="empid" name="empid" class="form-control" value="{{ old('empid', optional($instructor)->empid) }}" readonly required>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Instructor Name</label>
                <input type="text" id="name" class="form-control" value="{{ optional($instructor)->name }}" readonly>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', optional($instructor)->email) }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" minlength="6" required>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" minlength="6" required>
            </div>

            <button type="submit" class="btn btn-submit w-100">
                <i class="fas fa-check"></i> Complete Registration
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('instructor.login') }}">Back to Instructor Login</a>
        </div>
    </div>
</body>
</html>
