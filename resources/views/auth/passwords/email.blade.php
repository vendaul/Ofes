<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Faculty Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
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
            max-width: 430px;
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

        .btn-primary {
            border-radius: 10px;
            padding: 11px;
            font-weight: 600;
            background: linear-gradient(135deg, #3a7bd5 0%, #00b4d8 100%);
            border: 0;
        }

    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h1><i class="fas fa-key"></i> Forgot Password</h1>
            <p>Enter your email and we will send an OTP code.</p>
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

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-paper-plane"></i> Send OTP Code
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('choose.role') }}">Back to Role Selection</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
