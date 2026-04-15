<?php $__env->startSection('content'); ?>

<style>
    .section-sticky-wrap {
        max-height: 68vh;
        overflow: auto;
    }

    .section-sticky-wrap thead th {
        position: sticky;
        top: 0;
        z-index: 3;
        background-color: #f8f9fa;
    }

</style>

<div class="container-fluid">
    <div class="page-header mb-4 d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="fas fa-layer-group"></i> Section and Student Management</h1>
            <p class="text-muted">Manage sections and students in one place</p>
        </div>
        <form id="sectionStudentFilterForm" method="GET" class="d-flex gap-2 align-items-end">
            <input type="hidden" name="tab" id="activeTabInput" value="<?php echo e($activeTab); ?>">
            <div class="form-group mb-0">
                <label for="area_code" class="form-label small mb-1">Area</label>
                <select id="area_code" name="area_code" class="form-select" onchange="submitSectionStudentFilter(this.form)">
                    <option value="">Select Area</option>
                    <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($code); ?>" <?php echo e((string) $code === (string) $selectedArea ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group mb-0">
                <label for="college_id" class="form-label small mb-1">College</label>
                <select id="college_id" name="college_id" class="form-select" onchange="submitSectionStudentFilter(this.form)">
                    <option value="">Select College</option>
                    <?php $__currentLoopData = $collegeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>" <?php echo e((string) $id === (string) $selectedCollege ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="button" class="btn btn-outline-primary align-self-end" onclick="submitSectionStudentFilter(document.getElementById('sectionStudentFilterForm'))">Apply</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="sectionStudentTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'sections' ? 'active' : ''); ?>" id="sections-tab" data-bs-toggle="tab" data-bs-target="#sections" type="button">
                        <i class="fas fa-layer-group"></i> Sections
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'students' ? 'active' : ''); ?>" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button">
                        <i class="fas fa-user-graduate"></i> Students
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade <?php echo e($activeTab === 'sections' ? 'show active' : ''); ?>" id="sections">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="<?php echo e(route('sections.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Section
                        </a>
                        <form id="sectionSearchForm" method="GET" action="<?php echo e(route('sections.index')); ?>" class="input-group w-50">
                            <input type="hidden" name="tab" value="sections">
                            <input type="hidden" name="area_code" value="<?php echo e($selectedArea); ?>">
                            <input type="hidden" name="college_id" value="<?php echo e($selectedCollege); ?>">
                            <input id="sectionSearch" type="text" name="section_search" class="form-control" placeholder="Search all sections by name or year..." value="<?php echo e($sectionSearchTerm ?? ''); ?>">
                            <button id="sectionSearchBtn" type="button" class="btn btn-outline-secondary"><i class="fas fa-search"></i> Search</button>
                            <?php if(!empty($sectionSearchTerm)): ?>
                            <a href="<?php echo e(route('sections.index', ['tab' => 'sections', 'area_code' => $selectedArea, 'college_id' => $selectedCollege])); ?>" class="btn btn-outline-danger" title="Clear search"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <?php if(!empty($sectionSearchTerm)): ?>
                    <div class="alert alert-info py-2 mb-2">
                        <i class="fas fa-search"></i> Showing all sections matching <strong>"<?php echo e($sectionSearchTerm); ?>"</strong> across all areas and colleges.
                        <a href="<?php echo e(route('sections.index', ['tab' => 'sections', 'area_code' => $selectedArea, 'college_id' => $selectedCollege])); ?>" class="ms-2 alert-link">Clear search</a>
                    </div>
                    <?php endif; ?>

                    <?php if($sections->count() > 0): ?>
                    <div class="table-responsive section-sticky-wrap">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Section ID</th>
                                    <th>Section Name</th>
                                    <th>Year</th>
                                    <th>Total Students</th>
                                    <th style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><span class="badge bg-primary text-white"><?php echo e($section->id); ?></span></td>
                                    <td><strong><?php echo e($section->name); ?></strong></td>
                                    <td><?php echo e($section->year ?: '-'); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('sections.students', ['id' => $section->id])); ?>" class="badge bg-secondary text-decoration-none" title="View students in this section">
                                            <i class="fas fa-users"></i> <?php echo e($sectionStudentCounts[$section->id] ?? 0); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="<?php echo e(route('sections.show', ['id' => $section->id])); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('sections.edit', ['id' => $section->id])); ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo e(route('sections.destroy', ['id' => $section->id])); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this section?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No sections found.
                        <a href="<?php echo e(route('sections.create')); ?>">Create one now</a>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade <?php echo e($activeTab === 'students' ? 'show active' : ''); ?>" id="students">
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <a href="<?php echo e(route('students.create')); ?>" class="btn btn-primary me-2">
                            <i class="fas fa-plus"></i> Add New Student
                        </a>
                        <form id="studentSearchForm" method="GET" action="<?php echo e(route('sections.index')); ?>" class="input-group w-50">
                            <input type="hidden" name="tab" value="students">
                            <input type="hidden" name="area_code" value="<?php echo e($selectedArea); ?>">
                            <input type="hidden" name="college_id" value="<?php echo e($selectedCollege); ?>">
                            <input id="studentSearch" type="text" name="student_search" class="form-control" placeholder="Search all students by number or name..." value="<?php echo e($studentSearchTerm ?? ''); ?>">
                            <button id="studentSearchBtn" type="button" class="btn btn-outline-secondary"><i class="fas fa-search"></i> Search</button>
                            <?php if(!empty($studentSearchTerm)): ?>
                            <a href="<?php echo e(route('sections.index', ['tab' => 'students', 'area_code' => $selectedArea, 'college_id' => $selectedCollege])); ?>#students" class="btn btn-outline-danger" title="Clear search"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                        </form>
                        <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#importStudentsModal">
                            <i class="fas fa-upload"></i> Import Students
                        </button>
                    </div>

                    <?php if(!empty($studentSearchTerm)): ?>
                    <div class="alert alert-info py-2 mb-2">
                        <i class="fas fa-search"></i> Showing all students matching <strong>"<?php echo e($studentSearchTerm); ?>"</strong> across all areas and colleges.
                        <a href="<?php echo e(route('sections.index', ['tab' => 'students', 'area_code' => $selectedArea, 'college_id' => $selectedCollege])); ?>#students" class="ms-2 alert-link">Clear search</a>
                    </div>
                    <?php endif; ?>

                    <?php if($students->count() > 0): ?>
                    <div class="table-responsive section-sticky-wrap">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Number</th>
                                    <th>Name</th>
                                    <th style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><span class="badge bg-primary text-white"><?php echo e($student->id); ?></span></td>
                                    <td><strong><?php echo e($student->sid); ?></strong></td>
                                    <td><?php echo e($student->fname); ?> <?php echo e($student->lname); ?></td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="<?php echo e(route('students.show', $student->id)); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('students.edit', $student->id)); ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo e(route('students.destroy', $student->id)); ?>" method="POST" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No students found.
                        <a href="<?php echo e(route('students.create')); ?>">Create one now</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importStudentsModal" tabindex="-1" aria-labelledby="importStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importStudentsModalLabel">
                    <i class="fas fa-upload"></i> Import Students
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('students.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Select CSV or Excel File</label>
                        <input type="file" class="form-control" id="importFile" name="file" accept=".csv,.xlsx,.xls,.txt" required>
                        <div class="form-text">Supported formats: CSV, Excel (.xlsx, .xls), Text (.txt)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Import Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activeTabInput = document.getElementById('activeTabInput');
        const filterForm = document.getElementById('sectionStudentFilterForm');
        const sectionSearchForm = document.getElementById('sectionSearchForm');
        const sectionSearchInput = document.getElementById('sectionSearch');
        const sectionSearchBtn = document.getElementById('sectionSearchBtn');
        const studentSearchForm = document.getElementById('studentSearchForm');
        const studentSearchInput = document.getElementById('studentSearch');
        const studentSearchBtn = document.getElementById('studentSearchBtn');
        const allowedTabs = ['sections', 'students'];

        function filterRows(containerSelector, inputValue) {
            const filter = (inputValue || '').trim().toLowerCase();
            document.querySelectorAll(`${containerSelector} table tbody tr`).forEach(function(row) {
                const rowText = Array.from(row.querySelectorAll('td')).map(function(td) {
                    return td.textContent.trim().toLowerCase();
                }).join(' ');
                const matches = filter === '' || rowText.indexOf(filter) !== -1;
                row.style.display = matches ? '' : 'none';
            });
        }

        function debounce(fn, delay) {
            let timer;
            return function() {
                const args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function() {
                    fn.apply(null, args);
                }, delay);
            };
        }

        function getCurrentTabState() {
            const activeButton = document.querySelector('#sectionStudentTabs .nav-link.active');
            if (activeButton) {
                return activeButton.getAttribute('data-bs-target').replace('#', '');
            }

            const activePane = document.querySelector('.tab-content .tab-pane.show.active');
            if (activePane) {
                return activePane.id;
            }

            return '<?php echo e($activeTab); ?>';
        }

        function setTabState(tabName) {
            if (activeTabInput) {
                activeTabInput.value = tabName;
            }

            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            url.hash = tabName;
            window.history.replaceState({}, '', url.toString());
        }

        window.submitSectionStudentFilter = function(form) {
            const tabName = getCurrentTabState();
            setTabState(tabName);
            form.submit();
        };

        if (filterForm) {
            filterForm.addEventListener('submit', function() {
                setTabState(getCurrentTabState());
            });
        }

        if (sectionSearchForm && sectionSearchInput) {
            sectionSearchForm.addEventListener('submit', function(event) {
                event.preventDefault();
            });

            const debouncedSectionSearch = debounce(function() {
                if (getCurrentTabState() !== 'sections') {
                    return;
                }

                filterRows('#sections', sectionSearchInput.value);
            }, 350);

            if (sectionSearchBtn) {
                sectionSearchBtn.addEventListener('click', function() {
                    filterRows('#sections', sectionSearchInput.value);
                });
            }

            sectionSearchInput.addEventListener('input', function() {
                debouncedSectionSearch();
            });

            sectionSearchInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    filterRows('#sections', sectionSearchInput.value);
                }
            });
        }

        if (studentSearchForm && studentSearchInput) {
            studentSearchForm.addEventListener('submit', function(event) {
                event.preventDefault();
            });

            const debouncedStudentSearch = debounce(function() {
                if (getCurrentTabState() !== 'students') {
                    return;
                }

                filterRows('#students', studentSearchInput.value);
            }, 350);

            if (studentSearchBtn) {
                studentSearchBtn.addEventListener('click', function() {
                    filterRows('#students', studentSearchInput.value);
                });
            }

            studentSearchInput.addEventListener('input', function() {
                debouncedStudentSearch();
            });

            studentSearchInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    filterRows('#students', studentSearchInput.value);
                }
            });
        }

        const hash = window.location.hash.replace('#', '');
        const queryTab = new URLSearchParams(window.location.search).get('tab');
        const initialTab = allowedTabs.includes(queryTab) ? queryTab : (allowedTabs.includes(hash) ? hash : getCurrentTabState());

        const initialTabTrigger = document.getElementById(initialTab + '-tab');
        if (initialTabTrigger && window.bootstrap && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(initialTabTrigger).show();
            setTabState(initialTab);
        } else {
            setTabState(getCurrentTabState());
        }

        document.querySelectorAll('#sectionStudentTabs button[data-bs-toggle="tab"]').forEach(function(tabButton) {
            tabButton.addEventListener('shown.bs.tab', function(event) {
                const target = event.target.getAttribute('data-bs-target') || '';
                const tabName = target.replace('#', '');
                const currentQueryTab = new URLSearchParams(window.location.search).get('tab');
                if (currentQueryTab === tabName) {
                    setTabState(tabName);
                    return;
                }

                const url = new URL(window.location.href);
                url.searchParams.set('tab', tabName);
                url.hash = tabName;
                window.location.assign(url.toString());
            });
        });
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/sections/index.blade.php ENDPATH**/ ?>