<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password with OTP - Faculty Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .reset-container {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.2);
            padding: 36px;
            max-width: 460px;
            width: 100%;
        }

        .reset-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .reset-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .reset-header p {
            color: #6b7280;
            margin-bottom: 0;
        }

        .form-control {
            border-radius: 10px;
            padding: 11px 14px;
        }

        .btn-success {
            border-radius: 10px;
            padding: 11px;
            font-weight: 600;
            border: 0;
            background: linear-gradient(135deg, #00a86b 0%, #4caf50 100%);
        }

        .btn-cancel {
            border-radius: 10px;
            padding: 11px;
            font-weight: 600;
        }

    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h1><i class="fas fa-shield-halved"></i> Reset Password</h1>
            <p>Enter the OTP sent to your email and set a new password.</p>
        </div>

        @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $email ?? request('email')) }}" required>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="otp" class="form-label">OTP Code</label>
                <input type="text" id="otp" name="otp" maxlength="6" inputmode="numeric" class="form-control @error('otp') is-invalid @enderror" value="{{ old('otp') }}" required>
                @error('otp')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-unlock-alt"></i> Update Password
            </button>

            <a href="{{ route('choose.role') }}" class="btn btn-outline-secondary btn-cancel w-100 mt-2">
                <i class="fas fa-arrow-left"></i> Cancel and Go Back
            </a>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}">Resend OTP</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
