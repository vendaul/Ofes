@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Enter Verification Code</h3>

    <form method="POST" action="{{ route('student.verify.otp') }}">
        @csrf
        <input type="text" name="otp_code" class="form-control mb-3" placeholder="Enter Code" required>

        <button class="btn btn-success">Verify</button>
    </form>
</div>
@endsection
