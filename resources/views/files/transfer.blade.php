@extends('layouts.app')
@section('title', 'Transfer File')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('files.index') }}">Files</a></li>
<li class="breadcrumb-item active">Transfer</li>
@endsection

@push('styles')
<style>
.transfer-type-card {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 1.1rem 1.25rem;
    cursor: pointer;
    transition: border-color .18s, background .18s;
    user-select: none;
}
.transfer-type-card:hover { border-color: #6366f1; background: #f5f3ff; }
.transfer-type-card.selected { border-color: #6366f1; background: #ede9fe; }
.transfer-type-card input[type=radio] { accent-color: #6366f1; }

#dept-suggestions {
    position: absolute;
    z-index: 1050;
    width: 100%;
    background: #fff;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,.10);
    max-height: 220px;
    overflow-y: auto;
    margin-top: 2px;
}
#dept-suggestions .suggestion-item {
    padding: .6rem 1rem;
    cursor: pointer;
    font-size: .9rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background .12s;
}
#dept-suggestions .suggestion-item:last-child { border-bottom: none; }
#dept-suggestions .suggestion-item:hover,
#dept-suggestions .suggestion-item.active { background: #ede9fe; color: #4f46e5; }
#dept-suggestions .suggestion-empty {
    padding: .6rem 1rem;
    color: #9ca3af;
    font-size: .875rem;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Transfer File</h1>
        <div class="page-subtitle">Route this file to another user or department</div>
    </div>
    <a href="{{ route('files.index') }}" class="btn-portal-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="row g-3">

    {{-- File Details Card --}}
    <div class="col-md-4">
        <div class="portal-card">
            <div class="card-header"><i class="fa-solid fa-file-lines me-2 text-primary"></i>File Details</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted fs-sm mb-1">File Name</div>
                    <div class="fw-700">{{ $file->file_name }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted fs-sm mb-1">File Number</div>
                    <div class="fw-700 text-portal-primary">{{ $file->file_number }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted fs-sm mb-1">Current Department</div>
                    <div>{{ $file->department->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-muted fs-sm mb-1">Status</div>
                    @include('partials.status-badge', ['status' => $file->status])
                </div>
            </div>
        </div>
    </div>

    {{-- Transfer Form --}}
    <div class="col-md-8">
        <div class="portal-form-card" style="max-width:100%">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-right-left me-2 text-primary"></i>Transfer Details</h5>

            <form action="{{ route('files.transfer.store') }}" method="POST" id="transferForm" class="portal-form">
                @csrf
                <input type="hidden" name="file_record_uuid" value="{{ $file->uuid }}">
                <input type="hidden" name="destination_type" id="destination_type_input" value="">
                <input type="hidden" name="department_id"    id="department_id_input"    value="">

                {{-- Step 1: Destination type --}}
                <div class="mb-4">
                    <label class="form-label fw-600 mb-2">Destination Type <span class="required-star">*</span></label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="transfer-type-card d-flex align-items-center gap-3 w-100" id="card-same">
                                <input type="radio" name="_dest_type" value="same" class="form-check-input mt-0">
                                <div>
                                    <div class="fw-600" style="font-size:.9rem;">Same Department</div>
                                    <div class="text-muted fs-sm">{{ auth()->user()->department->name ?? '' }}</div>
                                </div>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="transfer-type-card d-flex align-items-center gap-3 w-100" id="card-other">
                                <input type="radio" name="_dest_type" value="other" class="form-check-input mt-0">
                                <div>
                                    <div class="fw-600" style="font-size:.9rem;">Other Department</div>
                                    <div class="text-muted fs-sm">Cross-department transfer</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    @error('destination_type')
                    <div class="text-danger fs-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Step 2a: Same dept — select user --}}
                <div id="section-same" style="display:none;" class="mb-3">
                    <label class="form-label">Select User <span class="required-star">*</span></label>
                    <select name="to_user_id" id="to_user_id" class="form-select @error('to_user_id') is-invalid @enderror">
                        <option value="">— Select a user —</option>
                        @foreach($sameDeptUsers as $u)
                        <option value="{{ $u->id }}" {{ old('to_user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('to_user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($sameDeptUsers->isEmpty())
                    <div class="alert alert-warning mt-2 py-2 px-3 fs-sm">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        No other active users in your department.
                    </div>
                    @endif
                </div>

                {{-- Step 2b: Other dept — AJAX search --}}
                <div id="section-other" style="display:none;" class="mb-3">
                    <label class="form-label">Department Name <span class="required-star">*</span></label>
                    <div class="position-relative">
                        <input
                            type="text"
                            id="dept_search_input"
                            class="form-control @error('department_id') is-invalid @enderror"
                            placeholder="Start typing department name…"
                            autocomplete="off"
                            value="{{ old('_dept_name', '') }}">
                        <div id="dept-suggestions" style="display:none;"></div>
                    </div>
                    <div id="dept-selected-info" class="mt-2 fs-sm" style="display:none;">
                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i><span id="dept-selected-name"></span></span>
                    </div>
                    <div id="dept-not-found" class="mt-2 fs-sm text-danger" style="display:none;">
                        <i class="fa-solid fa-circle-xmark me-1"></i>No department found. Please select from the suggestions.
                    </div>
                    @error('department_id')
                    <div class="text-danger fs-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remarks --}}
                <div class="mb-4" id="section-remarks" style="display:none;">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3"
                        placeholder="Optional notes about this transfer">{{ old('remarks') }}</textarea>
                </div>

                {{-- Submit --}}
                <div class="d-flex gap-2" id="section-submit" style="display:none;">
                    <button type="submit" class="btn-portal-primary" id="submitBtn">
                        <i class="fa-solid fa-paper-plane"></i> Transfer Now
                    </button>
                    <a href="{{ route('files.index') }}" class="btn-portal-outline">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // ── DOM refs ──────────────────────────────────────────────────
    var radios      = document.querySelectorAll('input[name="_dest_type"]');
    var destInput   = document.getElementById('destination_type_input');
    var deptIdInput = document.getElementById('department_id_input');

    var cardSame  = document.getElementById('card-same');
    var cardOther = document.getElementById('card-other');

    var secSame    = document.getElementById('section-same');
    var secOther   = document.getElementById('section-other');
    var secRemarks = document.getElementById('section-remarks');
    var secSubmit  = document.getElementById('section-submit');

    // Dept search
    var deptSearch    = document.getElementById('dept_search_input');
    var suggestions   = document.getElementById('dept-suggestions');
    var deptInfo      = document.getElementById('dept-selected-info');
    var deptName      = document.getElementById('dept-selected-name');
    var deptNotFound  = document.getElementById('dept-not-found');

    var searchTimer = null;
    var selectedDeptId = null;

    // ── Type selection ────────────────────────────────────────────
    radios.forEach(function (r) {
        r.addEventListener('change', function () {
            onTypeChange(r.value);
        });
    });

    function onTypeChange(type) {
        destInput.value = type;

        // Update card styling
        cardSame.classList.toggle('selected', type === 'same');
        cardOther.classList.toggle('selected', type === 'other');

        // Show/hide sections
        secSame.style.display    = type === 'same'  ? '' : 'none';
        secOther.style.display   = type === 'other' ? '' : 'none';
        secRemarks.style.display = '';
        secSubmit.style.display  = '';

        // Reset dept state when switching
        if (type === 'same') {
            clearDeptSelection();
            deptIdInput.value = '';
        } else {
            document.getElementById('to_user_id').value = '';
        }
    }

    // ── Dept AJAX search ──────────────────────────────────────────
    if (deptSearch) {
        deptSearch.addEventListener('input', function () {
            clearDeptSelection();
            var q = deptSearch.value.trim();

            if (q.length < 2) {
                hideSuggestions();
                return;
            }

            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () { fetchDepts(q); }, 280);
        });

        deptSearch.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') hideSuggestions();
        });
    }

    document.addEventListener('click', function (e) {
        if (deptSearch && !deptSearch.contains(e.target) && !suggestions.contains(e.target)) {
            hideSuggestions();
            // If nothing was selected, show error
            if (!selectedDeptId && deptSearch.value.trim().length > 0) {
                deptNotFound.style.display = '';
            }
        }
    });

    function fetchDepts(q) {
        fetch('{{ route("ajax.departments.search") }}?q=' + encodeURIComponent(q), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        })
        .then(function (r) { return r.ok ? r.json() : []; })
        .then(function (data) { renderSuggestions(data); })
        .catch(function () { hideSuggestions(); });
    }

    function renderSuggestions(data) {
        suggestions.innerHTML = '';
        if (!data || data.length === 0) {
            suggestions.innerHTML = '<div class="suggestion-empty"><i class="fa-solid fa-circle-xmark me-1"></i>No department found.</div>';
            deptNotFound.style.display = '';
            deptInfo.style.display = 'none';
        } else {
            deptNotFound.style.display = 'none';
            data.forEach(function (dept) {
                var item = document.createElement('div');
                item.className = 'suggestion-item';
                item.textContent = dept.name;
                item.dataset.id = dept.id;
                item.addEventListener('click', function () {
                    selectDept(dept.id, dept.name);
                });
                suggestions.appendChild(item);
            });
        }
        suggestions.style.display = '';
    }

    function selectDept(id, name) {
        selectedDeptId    = id;
        deptIdInput.value = id;
        deptSearch.value  = name;
        deptName.textContent = name;
        deptInfo.style.display    = '';
        deptNotFound.style.display = 'none';
        hideSuggestions();
    }

    function clearDeptSelection() {
        selectedDeptId    = null;
        deptIdInput.value = '';
        deptInfo.style.display     = 'none';
        deptNotFound.style.display = 'none';
    }

    function hideSuggestions() {
        suggestions.style.display = 'none';
    }

    // ── Form submit validation ────────────────────────────────────
    document.getElementById('transferForm').addEventListener('submit', function (e) {
        var type = destInput.value;

        if (!type) {
            e.preventDefault();
            alert('Please select a destination type.');
            return;
        }

        if (type === 'same') {
            var sel = document.getElementById('to_user_id');
            if (!sel.value) {
                e.preventDefault();
                alert('Please select a user to transfer to.');
                return;
            }
        }

        if (type === 'other') {
            if (!selectedDeptId) {
                e.preventDefault();
                deptNotFound.style.display = '';
                deptSearch.focus();
                return;
            }
        }

        if (!confirm('Confirm immediate file transfer?')) {
            e.preventDefault();
        }
    });

    // ── Pre-fill on old() validation failure ─────────────────────
    @if(old('destination_type'))
        onTypeChange('{{ old("destination_type") }}');
        @if(old('destination_type') === 'other' && old('_dept_name'))
            // Mark as unselected — user must re-pick
            deptSearch.value = '{{ old("_dept_name") }}';
        @endif
    @endif

})();
</script>
@endpush
