<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\EvaluationQuestionController;
use App\Http\Controllers\EvaluationAnswerController;
use App\Http\Controllers\TeachingAssignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentEvaluationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController; // for admin/instructor login
use App\Http\Controllers\QuestionTemplateController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AreaCollegeController;

// ==============================
// ROLE SELECTION PAGE
// ==============================

Route::get('/', function () {
    return view('auth.choose-role');
})->name('choose.role');


// ==============================
// ADMIN LOGIN
// ==============================

Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login');

Route::post('/admin/login', [LoginController::class, 'adminLogin'])
    ->name('admin.login.submit');

// Password reset (shared)
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/otp', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset/otp', [ResetPasswordController::class, 'reset'])->name('password.update');

// Student routes (temporarily without auth for testing)
Route::resource('students', StudentController::class);
Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
Route::post('students/add-existing', [StudentController::class, 'addExistingStudent'])->name('students.addExisting');

// Question Template Management (CRUD)
Route::middleware(['auth'])->group(function () {
    Route::resource('question_templates', QuestionTemplateController::class);
    Route::post('question_templates/store-from-questions', [QuestionTemplateController::class, 'storeFromQuestions'])->name('question_templates.storeFromQuestions');
       // Use Template as Default Evaluation Form
       Route::post('questions/use-template/{template}', [App\Http\Controllers\EvaluationQuestionController::class, 'useTemplate'])->name('questions.useTemplate');
    Route::post('questions/reschedule-period', [App\Http\Controllers\EvaluationQuestionController::class, 'reschedulePeriod'])->name('questions.reschedulePeriod');
    Route::post('questions/stop-evaluation', [App\Http\Controllers\EvaluationQuestionController::class, 'stopEvaluation'])->name('questions.stopEvaluation');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('admin.dashboard');
});

// ==============================
// INSTRUCTOR LOGIN
// ==============================

Route::get('/instructor/login', function () {
    return view('auth.instructor-login');
})->name('instructor.login');

Route::post('/instructor/login', [LoginController::class, 'instructorLogin'])
    ->name('instructor.login.submit');

Route::get('/instructor/check/{empid}', [LoginController::class, 'checkInstructor'])
    ->name('instructor.check');

Route::get('/instructor/register', [LoginController::class, 'showInstructorRegister'])
    ->name('instructor.register.form');

Route::post('/instructor/register', [LoginController::class, 'registerInstructor'])
    ->name('instructor.register');

Route::middleware(['auth'])->group(function () {
    Route::get('/instructor/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        \Log::info('Accessing instructor dashboard', ['user' => $user, 'session_id' => session()->getId()]);
        if ($user->display_role !== 'instructor') {
            abort(403, 'Unauthorized (role=' . ($user->display_role ?? 'null') . ')');
        }
        return app(\App\Http\Controllers\InstructorController::class)->dashboard();
    })->name('instructor.dashboard');

    Route::get('/instructor/workload', [InstructorController::class, 'workload'])
        ->name('instructor.workload');

    Route::get('/instructor/reports', [InstructorController::class, 'reports'])
        ->name('instructor.reports');
});

// ==============================
// STUDENT LOGIN (OUTSIDE AUTH)
// ==============================

Route::get('/student/login', function () {
    return view('auth.student-login');
})->name('student.login');

Route::post('/student/login', [StudentAuthController::class, 'login'])
    ->name('student.login.submit');

Route::get('/student/check/{student_number}', [StudentAuthController::class, 'checkStudent'])
    ->name('student.check');

Route::get('/student/register', [StudentAuthController::class, 'showRegister'])
    ->name('student.register.form');

Route::post('/student/register', [StudentAuthController::class, 'registerStudent'])
    ->name('student.register');

Route::get('/student/password/reset-by-sid', [StudentAuthController::class, 'showResetBySid'])
    ->name('student.password.reset.sid');

Route::post('/student/password/reset-by-sid', [StudentAuthController::class, 'sendResetBySid'])
    ->name('student.password.reset.sid.send');

// ==============================
// AUTHENTICATED STUDENT ROUTES
// ==============================

Route::middleware(['auth'])->group(function () {

    Route::get('/student/dashboard', [StudentDashboardController::class, 'dashboard'])
        ->name('student.dashboard');

    Route::get('/student/schedules', [StudentDashboardController::class, 'schedules'])
        ->name('student.schedules');

    Route::get('/student/evaluations', [StudentDashboardController::class, 'evaluations'])
        ->name('student.evaluations');

    Route::get('/student/evaluate/{class_schedule_id}', [StudentController::class, 'showEvaluation'])
        ->name('student.evaluate');

    Route::get('/evaluate/{class_schedule_id}', [StudentEvaluationController::class, 'show'])
        ->name('evaluate.show');

    Route::post('/evaluate/store', [StudentEvaluationController::class, 'store'])
        ->name('evaluate.store');

    Route::post('/student/evaluate/store', [StudentController::class, 'storeEvaluation'])
        ->name('student.evaluate.store');

    Route::post('/student/submit', [StudentController::class, 'submitEvaluation'])
        ->name('student.submit');
});

// ==============================
// ADMIN / GENERAL AUTH ROUTES
// ==============================

Route::middleware(['auth'])->group(function () {

    //resource routes temporarily disabled to avoid missing controller errors
    // Route::resource('students', StudentController::class); // Temporarily moved outside auth
    Route::resource('instructors', InstructorController::class);
    Route::post('instructors/import-with-workload', [InstructorController::class, 'importWithWorkload'])->name('instructors.importWithWorkload');
    // workload popup/overview per instructor
   // Admin routes
    Route::get('/admin/instructors/{id}/workload', [TeachingAssignmentController::class, 'instructorWorkload'])
    ->name('instructors.workload');
    Route::post('/admin/instructors/{id}/workload/import', [TeachingAssignmentController::class, 'importWorkload'])
    ->name('instructors.workload.import');
    Route::get('/admin/sections/{id}/students', [SectionController::class, 'showStudents'])->name('sections.students');
    Route::resource('subjects', SubjectController::class);
    Route::delete('subjects/bulk/destroy', [SubjectController::class, 'destroyMany'])->name('subjects.destroyMany');
    Route::post('subjects/import', [SubjectController::class, 'import'])->name('subjects.import');
    Route::post('subjects/programs', [SubjectController::class, 'storeProgram'])->name('subjects.programs.store');
    Route::put('subjects/programs/{id}', [SubjectController::class, 'updateProgram'])->name('subjects.programs.update');
    Route::delete('subjects/programs/{id}', [SubjectController::class, 'destroyProgram'])->name('subjects.programs.destroy');
    Route::post('subjects/curriculums', [SubjectController::class, 'storeCurriculum'])->name('subjects.curriculums.store');
    Route::post('subjects/curriculums/{id}/set-active', [SubjectController::class, 'setActiveCurriculum'])->name('subjects.curriculums.set-active');
    Route::put('subjects/curriculums/{id}', [SubjectController::class, 'updateCurriculum'])->name('subjects.curriculums.update');
    Route::delete('subjects/curriculums/{id}', [SubjectController::class, 'destroyCurriculum'])->name('subjects.curriculums.destroy');
    Route::post('curriculums/ajax-create', [SubjectController::class, 'ajaxCreateCurriculum'])->name('curriculums.ajaxCreate');
    Route::post('programs/ajax-create', [SubjectController::class, 'ajaxCreateProgram'])->name('programs.ajaxCreate');
    Route::get('sections', [SectionController::class, 'index'])->name('sections.index');
    Route::get('sections/create', [SectionController::class, 'create'])->name('sections.create');
    Route::post('sections', [SectionController::class, 'store'])->name('sections.store');
    Route::get('sections/{id}', [SectionController::class, 'show'])->name('sections.show');
    Route::get('sections/{id}/edit', [SectionController::class, 'edit'])->name('sections.edit');
    Route::put('sections/{id}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('sections/{id}', [SectionController::class, 'destroy'])->name('sections.destroy');
    Route::resource('questions', EvaluationQuestionController::class);
    Route::resource('evaluation-answers', EvaluationAnswerController::class);
    Route::resource('assignments', TeachingAssignmentController::class);

    // Class Schedule Student Management routes
    Route::get('/class-schedules/{id}/students', [App\Http\Controllers\ClassScheduleStudentController::class, 'index'])->name('class-schedule-students.index');
    Route::get('/class-schedules/{id}/students/create', [App\Http\Controllers\ClassScheduleStudentController::class, 'create'])->name('class-schedule-students.create');
    Route::post('/class-schedules/{id}/students', [App\Http\Controllers\ClassScheduleStudentController::class, 'store'])->name('class-schedule-students.store');
    Route::post('/class-schedules/{id}/students/bulk', [App\Http\Controllers\ClassScheduleStudentController::class, 'bulkStore'])->name('class-schedule-students.bulk');
    Route::get('/class-schedules/{id}/students/{studentId}/edit', [App\Http\Controllers\ClassScheduleStudentController::class, 'edit'])->name('class-schedule-students.edit');
    Route::put('/class-schedules/{id}/students/{studentId}', [App\Http\Controllers\ClassScheduleStudentController::class, 'update'])->name('class-schedule-students.update');
    Route::delete('/class-schedules/{id}/students/{studentId}', [App\Http\Controllers\ClassScheduleStudentController::class, 'destroy'])->name('class-schedule-students.destroy');

    // Class Schedule CRUD routes
    Route::resource('class-schedules', App\Http\Controllers\ClassScheduleController::class);

    // settings routes
    Route::get('settings/accounts', [\App\Http\Controllers\SettingsController::class, 'manageAccounts'])
        ->name('settings.accounts');
    Route::post('settings/accounts', [\App\Http\Controllers\SettingsController::class, 'updateAccounts']);

    Route::get('/instructor/results/{assign_id}', [ReportController::class, 'instructorResult'])
        ->name('instructor.results');

   //Manage myAccount
   Route::get('settings/myAccount', [\App\Http\Controllers\SettingsController::class, 'myAccount'])
        ->name('settings.myAccount');
    Route::post('settings/myAccount', [\App\Http\Controllers\SettingsController::class, 'updateAccounts']);

    // Manage semester and school year
    Route::get('settings/semester', [\App\Http\Controllers\SettingsController::class, 'manageSemesterSettings'])
        ->name('settings.semester');
    Route::post('settings/semester', [\App\Http\Controllers\SettingsController::class, 'updateSemesterSettings'])
        ->name('settings.semester.update');

    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');

    Route::post('/settings/logout-other-devices', [SettingsController::class, 'logoutOtherDevices'])->name('logout.other.devices');

    // Areas & Colleges
    Route::get('/admin/area-college', [AreaCollegeController::class, 'index'])->name('admin.area_college');
    Route::post('/admin/areas', [AreaCollegeController::class, 'storeArea'])->name('areas.store');
    Route::put('/admin/areas/{area}', [AreaCollegeController::class, 'updateArea'])->name('areas.update');
    Route::delete('/admin/areas/{area}', [AreaCollegeController::class, 'destroyArea'])->name('areas.destroy');
    Route::post('/admin/colleges', [AreaCollegeController::class, 'storeCollege'])->name('colleges.store');
    Route::post('/admin/colleges/{college}/set-active', [AreaCollegeController::class, 'setActiveCollege'])->name('colleges.set-active');
    Route::post('/admin/dashboard/notify-pending-students', [DashboardController::class, 'notifyPendingStudents'])->name('admin.dashboard.notify-pending');
    Route::put('/admin/colleges/{college}', [AreaCollegeController::class, 'updateCollege'])->name('colleges.update');
    Route::delete('/admin/colleges/{college}', [AreaCollegeController::class, 'destroyCollege'])->name('colleges.destroy');
});


Route::get('/debug/test', function () {
    return 'Test route works!';
});

Route::get('/debug/auth', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user) {
        return 'Authenticated! Email: ' . $user->email . ', Role: ' . $user->display_role;
    } else {
        return 'Not authenticated';
    }
});
Route::get('/test-mail', function () {
    Mail::raw('Test Email', function ($message) {
        $message->to('yourgmail@gmail.com')
                ->subject('Test Email');
    });

    return 'Mail sent!';
});

// ==============================
// DEFAULT AUTH ROUTES (optional for fallback)
// ==============================

// We have custom role-based login routes.  Disable the default `/login` route
// to avoid users accidentally signing in through the generic form and being
// redirected to the wrong dashboard.
// Auth::routes();

// Provide only the necessary logout route since it was removed above.
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->name('logout');
// also allow GET for quick testing (change to POST in production)
Route::get('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);

// Optional: redirect any hit to /login back to role selection
Route::get('/login', function () {
    return redirect()->route('choose.role');
});
