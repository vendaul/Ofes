@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-users-cog"></i> Manage Supervisors & Evaluators</h1>
    <p>Assign dean/program chair per college and choose evaluators</p>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('settings.accounts') }}" method="POST">
    @csrf

    <div class="mb-4">
        <label for="college_filter" class="form-label">Select College/Department</label>
        <select id="college_filter" class="form-select">
            <option value="">All Colleges</option>
            @foreach($colleges as $college)
            <option value="{{ $college->id }}">{{ $college->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- dean/chair assignments -->
    <div class="card mb-4">
        <div class="card-header text-white bg-primary">
            <i class="fas fa-user-tie"></i> Dean / Program Chair by College
        </div>
        <div class="card-body">
            @foreach($colleges as $college)
            <div class="mb-3 college-group" data-college-id="{{ $college->id }}">
                <h5>{{ $college->name }}</h5>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Supervisor</label>
                        <select name="supervisor[{{ $college->id }}]" class="form-select">
                            <option value="">-- none --</option>
                            @foreach($instructors->where('college', $college->id) as $i)
                            <option value="{{ $i->instructor_id }}" {{ $i->supervisor_role === 'supervisor' ? 'selected' : '' }}>
                                {{ $i->first_name ?? $i->fname }} {{ $i->last_name ?? $i->lname }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dean</label>
                        <select name="dean[{{ $college->id }}]" class="form-select">
                            <option value="">-- none --</option>
                            @foreach($instructors->where('college', $college->id) as $i)
                            <option value="{{ $i->instructor_id }}" {{ $i->supervisor_role === 'dean' ? 'selected' : '' }}>
                                {{ $i->first_name ?? $i->fname }} {{ $i->last_name ?? $i->lname }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Program Chair</label>
                        <select name="chair[{{ $college->id }}]" class="form-select">
                            <option value="">-- none --</option>
                            @foreach($instructors->where('college', $college->id) as $i)
                            <option value="{{ $i->instructor_id }}" {{ $i->supervisor_role === 'program_chair' ? 'selected' : '' }}>
                                {{ $i->first_name ?? $i->fname }} {{ $i->last_name ?? $i->lname }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- evaluator selection -->
    <div class="card mb-4">
        <div class="card-header text-white bg-primary">
            <i class="fas fa-check-circle"></i> Supervisor Evaluators
        </div>
        <div class="card-body">
            <p>Choose which supervisors (deans or program chairs) may serve as evaluators.</p>
            @php
            $supervisors = $instructors->filter(function($i){
            return in_array($i->supervisor_role, ['supervisor','dean','program_chair']);
            });
            @endphp
            @if($supervisors->isEmpty())
            <p class="text-muted">No supervisors have been assigned yet.</p>
            @else
            <div class="row">
                @foreach($supervisors as $sup)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="evaluators[]" value="{{ $sup->instructor_id }}" id="eval{{ $sup->instructor_id }}" {{ $sup->evaluator ? 'checked' : '' }}>
                        <label class="form-check-label" for="eval{{ $sup->instructor_id }}">
                            {{ $sup->first_name }} {{ $sup->last_name }}
                            ({{ ucfirst($sup->supervisor_role) }})
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
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

@endsection
