<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Faculty Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="container" style="max-width: 500px;">
        <div class="card shadow-lg mt-5">
            <div class="card-body p-4">
                <h4 class="card-title text-center mb-4"><i class="fas fa-user-plus"></i> Student Registration</h4>

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('student.register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="student_number" class="form-label">Student Number</label>
                        <input type="text" id="student_number" name="sid" class="form-control" value="{{ old('sid', optional($student)->sid) }}" required readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Student Name</label>
                        <input type="text" class="form-control" value="{{ optional($student)->first_name }} {{ optional($student)->last_name }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', optional($student)->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-user-check"></i> Register</button>
                    <a href="{{ route('student.login') }}" class="btn btn-secondary w-100 mt-2"><i class="fas fa-arrow-left"></i> Back to login</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
