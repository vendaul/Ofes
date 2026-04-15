<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-users-cog"></i> Manage Supervisors & Evaluators</h1>
    <p>Assign dean/program chair per college and choose evaluators</p>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<form action="<?php echo e(route('settings.accounts')); ?>" method="POST">
    <?php echo csrf_field(); ?>

    <div class="mb-4">
        <label for="college_filter" class="form-label">Select College/Department</label>
        <select id="college_filter" class="form-select">
            <option value="">All Colleges</option>
            <?php $__currentLoopData = $colleges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $college): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($college->id); ?>"><?php echo e($college->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <!-- dean/chair assignments -->
    <div class="card mb-4">
        <div class="card-header text-white bg-primary">
            <i class="fas fa-user-tie"></i> Dean / Program Chair by College
        </div>
        <div class="card-body">
            <?php $__currentLoopData = $colleges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $college): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mb-3 college-group" data-college-id="<?php echo e($college->id); ?>">
                <h5><?php echo e($college->name); ?></h5>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Supervisor</label>
                        <select name="supervisor[<?php echo e($college->id); ?>]" class="form-select">
                            <option value="">-- none --</option>
                            <?php $__currentLoopData = $instructors->where('college', $college->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($i->instructor_id); ?>" <?php echo e($i->supervisor_role === 'supervisor' ? 'selected' : ''); ?>>
                                <?php echo e($i->first_name ?? $i->fname); ?> <?php echo e($i->last_name ?? $i->lname); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dean</label>
                        <select name="dean[<?php echo e($college->id); ?>]" class="form-select">
                            <option value="">-- none --</option>
                            <?php $__currentLoopData = $instructors->where('college', $college->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($i->instructor_id); ?>" <?php echo e($i->supervisor_role === 'dean' ? 'selected' : ''); ?>>
                                <?php echo e($i->first_name ?? $i->fname); ?> <?php echo e($i->last_name ?? $i->lname); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Program Chair</label>
                        <select name="chair[<?php echo e($college->id); ?>]" class="form-select">
                            <option value="">-- none --</option>
                            <?php $__currentLoopData = $instructors->where('college', $college->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($i->instructor_id); ?>" <?php echo e($i->supervisor_role === 'program_chair' ? 'selected' : ''); ?>>
                                <?php echo e($i->first_name ?? $i->fname); ?> <?php echo e($i->last_name ?? $i->lname); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- evaluator selection -->
    <div class="card mb-4">
        <div class="card-header text-white bg-primary">
            <i class="fas fa-check-circle"></i> Supervisor Evaluators
        </div>
        <div class="card-body">
            <p>Choose which supervisors (deans or program chairs) may serve as evaluators.</p>
            <?php
            $supervisors = $instructors->filter(function($i){
            return in_array($i->supervisor_role, ['supervisor','dean','program_chair']);
            });
            ?>
            <?php if($supervisors->isEmpty()): ?>
            <p class="text-muted">No supervisors have been assigned yet.</p>
            <?php else: ?>
            <div class="row">
                <?php $__currentLoopData = $supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="evaluators[]" value="<?php echo e($sup->instructor_id); ?>" id="eval<?php echo e($sup->instructor_id); ?>" <?php echo e($sup->evaluator ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="eval<?php echo e($sup->instructor_id); ?>">
                            <?php echo e($sup->first_name); ?> <?php echo e($sup->last_name); ?>

                            (<?php echo e(ucfirst($sup->supervisor_role)); ?>)
                        </label>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Save Changes
    </button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filter = document.getElementById('college_filter');
        const groups = document.querySelectorAll('.college-group');

        const updateVisibility = () => {
            const selected = filter.value;
            groups.forEach((group) => {
                if (!selected || group.dataset.collegeId === selected) {
                    group.style.display = '';
                } else {
                    group.style.display = 'none';
                }
            });
        };

        filter.addEventListener('change', updateVisibility);
        updateVisibility();
    });

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/settings/accounts.blade.php ENDPATH**/ ?>