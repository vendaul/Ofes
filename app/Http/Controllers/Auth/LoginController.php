<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

protected function credentials(\Illuminate\Http\Request $request)
{
    $roleMap = [
        'admin' => '1',
        'instructor' => '2',
        'student' => '3',
        'super_admin' => '4'  // Added for admin user
    ];
    return [
        'email' => $request->email,
        'password' => $request->password,
        'user_role' => $roleMap[$request->role] ?? $request->role
    ];
}
public function adminLogin(Request $request)
{
    \Log::info('Admin login attempt', ['email' => $request->email]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        \Log::info('Auth attempt success', ['user' => $user]);

        if ($user->user_role == null || !in_array($user->user_role, ['1', '4'])) {
            $user->user_role = '4';  // Set to admin role
            $user->save();
        }

        if ($user->display_role !== 'admin') {
            Auth::logout();
            \Log::warning('Auth role mismatch', ['role' => $user->display_role]);
            return back()->with('error', 'You are not an admin.');
        }

        $user->last_login = now();
        $user->save();

        return redirect()->route('admin.dashboard');
    }

    \Log::warning('Auth attempt failed', ['credentials' => $credentials]);
    return back()->with('error', 'Invalid credentials.');
}

protected function authenticated($request, $user)
{
    if ($user->display_role === 'admin') {
        return redirect('/admin/dashboard');
    }

    if ($user->display_role === 'instructor') {
        return redirect('/instructor/dashboard');
    }

    if ($user->display_role === 'student') {
        return redirect('/student/dashboard');
    }

    return redirect('/');
}
public function instructorLogin(Request $request)
{
    \Log::info('Instructor login attempt', ['email' => $request->email]);

    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        \Log::info('Auth attempt success', ['user' => $user]);

        if ($user->user_role == null) {
            $user->user_role = '2';
            $user->save();
        }

        if ($user->display_role !== 'instructor') {
            Auth::logout();
            \Log::warning('Auth role mismatch', ['role' => $user->display_role]);
            return back()->withErrors(['email' => 'Not authorized as instructor']);
        }

        return redirect()->route('instructor.dashboard');
    }

    \Log::warning('Auth attempt failed', ['credentials' => $credentials]);
    return back()->withErrors(['email' => 'Invalid credentials']);
}

public function checkInstructor($empid)
{
    $instructor = User::where('empid', $empid)
        ->whereRaw("TRIM(COALESCE(user_role, '')) = ?", ['2'])
        ->first();

    if (!$instructor) {
        return response()->json(['found' => false]);
    }

    return response()->json([
        'found' => true,
        'empid' => $instructor->empid,
        'name' => $instructor->name,
        'email' => $instructor->email,
        'has_password' => !empty($instructor->password),
    ]);
}

public function showInstructorRegister(Request $request)
{
    $instructor = null;

    if ($request->filled('empid')) {
        $instructor = User::where('empid', $request->empid)
            ->whereRaw("TRIM(COALESCE(user_role, '')) = ?", ['2'])
            ->first();

        if (!$instructor) {
            return redirect()->route('instructor.login')->withErrors(['empid' => 'Instructor ID not found.']);
        }

        if (!empty($instructor->email) && !empty($instructor->password)) {
            return redirect()->route('instructor.login')->with('status', 'Instructor account already completed. Please login.');
        }
    }

    return view('auth.instructor-register', compact('instructor'));
}

public function registerInstructor(Request $request)
{
    $validated = $request->validate([
        'empid' => 'required|string|exists:users,empid',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    $instructor = User::where('empid', $validated['empid'])
        ->whereRaw("TRIM(COALESCE(user_role, '')) = ?", ['2'])
        ->first();

    if (!$instructor) {
        return back()->withErrors(['empid' => 'Instructor ID not found.'])->withInput();
    }

    if (!empty($instructor->email) && !empty($instructor->password)) {
        return back()->withErrors(['empid' => 'Instructor account is already completed. Please login.']);
    }

    $instructor->email = $validated['email'];
    $instructor->password = Hash::make($validated['password']);
    $instructor->save();

    return redirect()->route('instructor.login')->with('status', 'Account completed successfully. You can now login.');
}
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('selected_area');
        $request->session()->forget('selected_college');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
