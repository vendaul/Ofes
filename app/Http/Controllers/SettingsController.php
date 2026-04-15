<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\College;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SettingsController extends Controller
{

    // UPDATE PROFILE
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'contact' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
        ]);

        $fullName = trim($request->name);
        $nameParts = preg_split('/\s+/', $fullName, 2);

        $user->fullname = $fullName;

        if (!empty($nameParts[0])) {
            $user->fname = $nameParts[0];
        }

        if (!empty($nameParts[1])) {
            $user->lname = $nameParts[1];
        }

        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->department = $request->department;

        $user->save();

        // Keep student profile table aligned when logged-in account is a student.
        if ((string) $user->user_role === '3') {
            Student::query()
                ->where('account_id', $user->id)
                ->update([
                    'fname' => $user->fname,
                    'lname' => $user->lname,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                ]);
        }

        return back()->with('success','Profile updated successfully.');
    }



    // CHANGE PASSWORD
    public function updatePassword(Request $request)
    {

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error','Current password is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success','Password changed successfully.');
    }

    /**
     * Logout other devices.
     */
    public function logoutOtherDevices(Request $request)
    {
        Auth::logoutOtherDevices();

        return back()->with('success', 'Logged out from other devices successfully.');
    }


    /**
     * Show semester/school year management page.
     */
    public function manageSemesterSettings(Request $request)
    {
        $periods = DB::table('db_periods')
            ->select('id', 'year', 'term', 'name', 'code', 'is_external')
            ->orderBy('year', 'desc')
            ->orderBy('term')
            ->get();

        $activePeriodId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
        $activePeriod = $activePeriodId ? DB::table('db_periods')->find($activePeriodId) : null;

        $editPeriod = null;
        if ($request->filled('edit_id')) {
            $editPeriod = DB::table('db_periods')->find((int) $request->query('edit_id'));
        }

        return view('settings.semester', compact('periods', 'activePeriod', 'editPeriod'));
    }

    /**
     * Handle semester/school year form post (create/update/delete/active).
     */
    public function updateSemesterSettings(Request $request)
    {
        if ($request->filled('bulk_delete_period_ids')) {
            $validated = $request->validate([
                'bulk_delete_period_ids' => 'required|array|min:1',
                'bulk_delete_period_ids.*' => 'integer|exists:db_periods,id',
            ]);

            $periodIds = collect($validated['bulk_delete_period_ids'])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($periodIds->isEmpty()) {
                return redirect()->route('settings.semester')->with('error', 'Please select at least one period to delete.');
            }

            $deleted = DB::table('db_periods')->whereIn('id', $periodIds->all())->delete();

            if ($deleted > 0) {
                $activeId = (int) DB::table('system_settings')->where('key', 'active_period_id')->value('value');
                if ($activeId && $periodIds->contains($activeId)) {
                    DB::table('system_settings')->where('key', 'active_period_id')->delete();
                }
            }

            return redirect()->route('settings.semester')->with('success', $deleted . ' period(s) deleted successfully.');
        }

        if ($request->filled('delete_period_id')) {
            $deleteId = (int) $request->input('delete_period_id');
            $deleted = DB::table('db_periods')->where('id', $deleteId)->delete();

            if ($deleted) {
                $activeId = DB::table('system_settings')->where('key', 'active_period_id')->value('value');
                if ($activeId && (int)$activeId === $deleteId) {
                    DB::table('system_settings')->where('key', 'active_period_id')->delete();
                }
            }

            return redirect()->route('settings.semester')->with('success', 'Period deleted successfully.');
        }

        if ($request->filled('set_active_id')) {
            $activeId = (int) $request->input('set_active_id');
            DB::table('system_settings')->updateOrInsert(
                ['key' => 'active_period_id'],
                ['value' => $activeId]
            );
            return redirect()->route('settings.semester')->with('success', 'Active period updated successfully.');
        }

        $request->validate([
            'year' => 'required|string|max:20',
            'term' => 'required|string|max:50',
            'name' => 'nullable|string|max:150',
        ]);

        $year = trim($request->input('year'));
        $term = trim($request->input('term'));
        $name = trim($request->input('name', "$term $year"));

        $updateId = $request->input('edit_period_id');

        if ($updateId) {
            $exists = DB::table('db_periods')
                ->where('year', $year)
                ->where('term', $term)
                ->where('id', '<>', $updateId)
                ->exists();
        } else {
            $exists = DB::table('db_periods')->where('year', $year)->where('term', $term)->exists();
        }

        if ($exists) {
            return redirect()->route('settings.semester')->with('error', 'This year/term already exists.');
        }

        $code = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($year . '-' . $term)));
        $code = Str::limit($code, 11, ''); // db_periods.code is varchar(11)

        if (DB::table('db_periods')->where('code', $code)->when($updateId, fn($q) => $q->where('id', '<>', $updateId))->exists()) {
            $code = $code . '-' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
            $code = Str::limit($code, 11, '');
        }

        if ($updateId) {
            DB::table('db_periods')->where('id', $updateId)->update([
                'code' => $code,
                'name' => $name,
                'year' => $year,
                'term' => $term,
                'is_external' => 'N',
                'updated_at' => now(),
            ]);

            return redirect()->route('settings.semester')->with('success', 'Period updated successfully.');
        }

        DB::table('db_periods')->insert([
            'code' => $code,
            'name' => $name,
            'year' => $year,
            'term' => $term,
            'is_external' => 'N',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('settings.semester')->with('success', 'Semester and school year entry saved successfully.');
    }

    /**
     * Show manage accounts page.
     */
    public function manageAccounts()
    {
        $colleges = College::orderBy('name')->get();
        $instructors = Instructor::orderBy('college')->orderBy('lname')->get();
        return view('settings.accounts', compact('instructors', 'colleges'));
    }

    /**
     * Show my account page.
     */
    public function myAccount()
    {
        $user = Auth::user();
        $studentProfile = null;

        if ($user && (string) $user->user_role === '3') {
            $studentProfile = Student::query()->where('account_id', $user->id)->first();
        }

        $displayName = trim((string) ($user->name ?? ''));
        if (($displayName === '' || strcasecmp($displayName, 'n/a') === 0) && $studentProfile) {
            $displayName = trim(((string) ($studentProfile->fname ?? '')) . ' ' . ((string) ($studentProfile->lname ?? '')));
        }
        if ($displayName === '') {
            $displayName = 'Student';
        }

        $displayEmail = trim((string) ($user->email ?? ''));
        if ($displayEmail === '' && $studentProfile) {
            $displayEmail = trim((string) ($studentProfile->email ?? ''));
        }

        return view('settings.myAccount', compact('displayName', 'displayEmail'));
    }

    /**
     * Save supervisor/dean/chair/evaluator assignments.
     */
    public function updateAccounts(Request $request)
    {
        $supervisor = $request->input('supervisor', []);
        $dean = $request->input('dean', []);
        $chair = $request->input('chair', []);
        $evaluators = $request->input('evaluators', []);

        // clear existing roles for involved colleges
        $colleges = array_unique(array_merge(array_keys($supervisor), array_keys($dean), array_keys($chair)));
        foreach ($colleges as $college) {
            Instructor::where('college', $college)
                ->whereIn('supervisor_role', ['supervisor', 'dean', 'program_chair'])
                ->update(['supervisor_role' => null]);

            if (!empty($supervisor[$college])) {
                Instructor::where('instructor_id', $supervisor[$college])
                    ->update(['supervisor_role' => 'supervisor']);
            }
            if (!empty($dean[$college])) {
                Instructor::where('instructor_id', $dean[$college])
                    ->update(['supervisor_role' => 'dean']);
            }
            if (!empty($chair[$college])) {
                Instructor::where('instructor_id', $chair[$college])
                    ->update(['supervisor_role' => 'program_chair']);
            }
        }

        // reset all evaluators then mark selected
        Instructor::where('evaluator', true)->update(['evaluator' => false]);
        if (is_array($evaluators) && count($evaluators) > 0) {
            Instructor::whereIn('instructor_id', $evaluators)->update(['evaluator' => true]);
        }

        return redirect()->route('settings.accounts')
                         ->with('success', 'Account management settings saved.');
    }
}
