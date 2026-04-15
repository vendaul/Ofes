<?php
$layout = 'layouts.student';

if (Auth::user()->display_role === 'admin') {
$layout = 'layouts.admin';
} elseif (Auth::user()->display_role === 'instructor') {
$layout = 'layouts.faculty';
}
?>



<?php $__env->startSection('content'); ?>
<?php
$nameValue = old('name', $displayName ?? (Auth::user()->name ?? 'Student'));
$emailValue = old('email', $displayEmail ?? (Auth::user()->email ?? ''));
?>
<div class="container mt-4">

    <div class="row">

        <!-- LEFT SIDE PROFILE -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body text-center">

                    <!-- Profile Image -->

                    <h4><?php echo e($nameValue); ?></h4>
                    <p class="text-muted"><?php echo e($emailValue !== '' ? $emailValue : 'No email'); ?></p>

                    <hr>

                    <p><strong>Role:</strong> <?php echo e(Auth::user()->display_role); ?></p>


                </div>
            </div>
        </div>


        <!-- RIGHT SIDE SETTINGS -->
        <div class="col-md-8">

            <!-- PROFILE INFORMATION -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Profile Information</h5>
                </div>

                <div class="card-body">

                    <form method="POST" action="<?php echo e(route('profile.update')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="row mb-3">
                            <div class="col">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo e($nameValue); ?>">
                            </div>

                            <div class="col">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo e($emailValue); ?>">
                            </div>
                        </div>

                        

            <button class="btn btn-success">
                Update Profile
            </button>
            <button type="button" class="btn btn-warning ml-2" onclick="togglePasswordForm()">Change Password</button>

            </form>

        </div>
    </div>

    <!-- CHANGE PASSWORD -->
    <div class="card shadow mb-4" id="password-card" style="display: none;">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">Change Password</h5>
        </div>

        <div class="card-body">

            <form method="POST" action="<?php echo e(route('settings.password.update')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-3 position-relative">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control" id="current_password">
                    <i class="fas fa-eye position-absolute" style="right: 10px; top: 70%; transform: translateY(-50%); cursor: pointer;" onclick="togglePassword('current_password', this)"></i>
                </div>

                <div class="mb-3 position-relative">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" id="new_password">
                    <i class="fas fa-eye position-absolute" style="right: 10px; top: 70%; transform: translateY(-50%); cursor: pointer;" onclick="togglePassword('new_password', this)"></i>
                </div>

                <div class="mb-3 position-relative">
                    <label>Confirm Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" id="confirm_password">
                    <i class="fas fa-eye position-absolute" style="right: 10px; top: 70%; transform: translateY(-50%); cursor: pointer;" onclick="togglePassword('confirm_password', this)"></i>
                </div>

                <button class="btn btn-warning">
                    Change Password
                </button>

            </form>

        </div>
    </div>

    <!-- SECURITY SETTINGS -->
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Security Settings</h5>
        </div>

        <div class="card-body">

            <div class="mb-3">
                <label>Last Login</label>
                <p><?php echo e(Auth::user()->last_login_at ? Auth::user()->last_login_at->format('M d, Y H:i') : 'Not available'); ?></p>
            </div>

            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-danger">
                    Logout
                </button>
            </form>

        </div>
    </div>

</div>

</div>

</div>

<script>
    function togglePassword(id, icon) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function togglePasswordForm() {
        const card = document.getElementById('password-card');
        if (card.style.display === 'none') {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    }

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/settings/myAccount.blade.php ENDPATH**/ ?>