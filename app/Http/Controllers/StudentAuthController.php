<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;


class StudentAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Prefer a user account that is linked to a student record for this email.
        $users = User::where('email', $request->email)->get();
        $user = null;

        foreach ($users as $candidate) {
            if (Student::where('account_id', $candidate->id)->exists()) {
                $user = $candidate;
                break;
            }
        }

        // If no student-linked user, fall back to first matching user.
        if (!$user && $users->isNotEmpty()) {
            $user = $users->first();
        }

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        // Ensure role student if a student record links to this account
        $studentLinked = Student::where('account_id', $user->id)->exists();
        if ($studentLinked && $user->user_role !== '3') {
            $user->user_role = '3';
            $user->save();
        }

        if (!$studentLinked && $user->user_role !== '3') {
            return back()->withErrors(['email' => 'Not authorized as student.']);
        }

        $passwordMatches = false;
        if (Hash::check($request->password, $user->password)) {
            $passwordMatches = true;
        } elseif ($request->password === $user->password) {
            // fallback for legacy plaintext passwords (not recommended)
            $passwordMatches = true;
        }

        if (!$passwordMatches) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        Auth::login($user);
        $user->last_login = now();
        $user->save();

        return redirect()->route('student.dashboard');
    }

    /**
     * Register a new student user account
     */
    public function registerStudent(Request $request)
    {
        $validated = $request->validate([
            'sid' => 'required|exists:db_students,sid',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $student = Student::where('sid', $validated['sid'])->firstOrFail();

        // If this student has a linked account, ask them to login instead of re-registering.
        if ($student->account_id) {
            $registeredEmail = optional(User::find($student->account_id))->email;
            $message = 'This student is already registered.';
            if ($registeredEmail) {
                $message .= ' Please login with ' . $registeredEmail . '.';
            }
            return back()->withErrors(['sid' => $message]);
        }

        // If the submitted email is already in use by any user, show friendly guidance.
        if (User::where('email', $validated['email'])->exists()) {
            return back()->withErrors(['email' => 'Email is already taken. Use Forgot Password or login if this is your account.']);
        }

        $user = User::create([
            'name' => $student->first_name . ' ' . $student->last_name,
            'email' => $validated['email'],
            'user_role' => '3', // student role
            'password' => Hash::make($validated['password']),
        ]);

        $student->account_id = $user->id;
        $student->save();

        Auth::login($user);

        return redirect('/student/dashboard')->with('success', 'Student account created successfully!');
    }

    /**
     * Show student registration form with prefilled student number if provided
     */
    public function showRegister(Request $request)
    {
        $student = null;
        if ($request->filled('sid')) {
            $student = Student::where('sid', $request->sid)->first();
        }

        return view('auth.student-register', compact('student'));
    }

    /**
     * Show SID-based password reset form.
     */
    public function showResetBySid()
    {
        return view('auth.student-reset-by-sid');
    }

    /**
     * Handle SID-based password reset request.
     */
    public function sendResetBySid(Request $request)
    {
        $request->validate([
            'sid' => 'required|string',
        ]);

        $student = Student::where('sid', $request->sid)->first();
        if (!$student) {
            return back()->withErrors(['sid' => 'Student ID not found.']);
        }

        if (!$student->account_id) {
            return back()->withErrors(['sid' => 'This student has no linked account yet. Please complete registration first.']);
        }

        $user = User::find($student->account_id);
        if (!$user) {
            return back()->withErrors(['sid' => 'Linked user account not found. Please contact administrator.']);
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Password reset link sent to ' . $user->email . '.');
        }

        return back()->withErrors(['sid' => 'Unable to send reset link. Please try again later.']);
    }

    /**
     * Check student by student number (AJAX)
     */
    public function checkStudent($sid)
    {
        // Lookup in db_students by sid (this is the current unique student identifier)
        $student = Student::where('sid', $sid)->first();

        if (!$student) {
            return response()->json(['found' => false]);
        }

        // Find associated user email (if account already created)
        $email = null;
        if ($student->account_id) {
            $user = User::find($student->account_id);
            $email = $user ? $user->email : null;
        }

        return response()->json([
            'found' => true,
            'sid' => $student->sid,
            'name' => $student->first_name . ' ' . $student->last_name,
            'has_account' => $student->account_id ? true : false,
            'email' => $email,
        ]);
    }
}
