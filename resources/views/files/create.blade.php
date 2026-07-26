@extends('layouts.app')
@section('title', 'Create File')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('files.index') }}">Files</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create New File</h1>
        <div class="page-subtitle">Register a new official document in the system</div>
    </div>
    <a href="{{ route('files.index') }}" class="btn-portal-outline">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
</div>

<div class="portal-form-card">
    <form action="{{ route('files.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="portal-form"
          id="createFileForm"
          novalidate>
        @csrf

        {{-- Government File Number --}}
        <div class="mb-3">
            <label class="form-label">
                Government File Number <span class="required-star">*</span>
            </label>
            <input type="text"
                   name="file_number"
                   class="form-control @error('file_number') is-invalid @enderror"
                   value="{{ old('file_number') }}"
                   placeholder="e.g. HR/FIN/2026/234  or  FIN-12/456"
                   required
                   autocomplete="off">
            <div class="form-text text-muted">
                <i class="fa-solid fa-circle-info me-1"></i>
                Must be unique. Allowed: letters, numbers, hyphens, slashes, dots, spaces.
            </div>
            @error('file_number')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- File Name --}}
        <div class="mb-3">
            <label class="form-label">
                File Name / Subject <span class="required-star">*</span>
            </label>
            <input type="text"
                   name="file_name"
                   class="form-control @error('file_name') is-invalid @enderror"
                   value="{{ old('file_name') }}"
                   placeholder="Enter file name or subject"
                   required>
            @error('file_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Department — searchable input --}}
        <div class="mb-3">
            <label for="deptSearchField" class="form-label">
                Department <span class="required-star">*</span>
            </label>

            {{-- Hidden field — this is what actually gets submitted --}}
            <input type="hidden" name="department_id" id="deptIdHidden" value="{{ old('department_id', auth()->user()->department_id) }}">

            <div class="position-relative" id="deptSearchWrap">

                {{-- Text input the user types into --}}
                <input type="text"
                       id="deptSearchField"
                       class="form-control @error('department_id') is-invalid @enderror"
                       placeholder="Type to search department…"
                       autocomplete="off"
                       value="{{ old('_dept_label',
                           $departments->firstWhere('id', old('department_id', auth()->user()->department_id))?->name ?? ''
                       ) }}"
                       aria-autocomplete="list"
                       aria-controls="deptResultsList"
                       aria-expanded="false">

                {{-- Results dropdown --}}
                <div id="deptResultsList"
                     class="list-group shadow"
                     role="listbox"
                     style="display:none;
                            position:absolute;
                            z-index:1055;
                            width:100%;
                            top:calc(100% + 3px);
                            max-height:220px;
                            overflow-y:auto;
                            border-radius:8px;">
                </div>
            </div>

            {{-- Selected dept badge --}}
            <div id="deptSelectedBadge"
                 class="mt-2"
                 style="display:{{ (old('department_id', auth()->user()->department_id)) ? '' : 'none' }};">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-600 py-2 px-3"
                      style="font-size:.8rem;border-radius:8px;">
                    <i class="fa-solid fa-building-columns me-1"></i>
                    <span id="deptSelectedName">{{ $departments->firstWhere('id', old('department_id', auth()->user()->department_id))?->name ?? '' }}</span>
                    <button type="button"
                            id="deptClearBtn"
                            class="btn-close btn-close-sm ms-2"
                            style="font-size:.55rem;"
                            aria-label="Clear department selection"></button>
                </span>
            </div>

            {{-- "No department found" message --}}
            <div id="deptNoResult"
                 class="form-text text-danger mt-1"
                 style="display:none;">
                <i class="fa-solid fa-circle-xmark me-1"></i>No department found matching your search.
            </div>

            <div class="form-text text-muted mt-1">
                <i class="fa-solid fa-circle-info me-1"></i>
                Select the department this file belongs to.
            </div>

            @error('department_id')
            <div class="text-danger" style="font-size:.875rem;margin-top:.25rem;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remarks --}}
        <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="remarks"
                      class="form-control @error('remarks') is-invalid @enderror"
                      rows="3"
                      placeholder="Optional remarks or notes">{{ old('remarks') }}</textarea>
            @error('remarks')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Attachment --}}
        <div class="mb-4">
            <label class="form-label">Upload Document</label>
            <input type="file"
                   name="attachment"
                   class="form-control @error('attachment') is-invalid @enderror"
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
            <div class="form-text text-muted">Max 10 MB. Allowed: PDF, Word, Excel, PowerPoint, Images.</div>
            @error('attachment')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-portal-primary">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save File
            </button>
            <a href="{{ route('files.index') }}" class="btn-portal-outline">Cancel</a>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    /*
     * Department searchable input.
     * Departments are embedded from server — no AJAX needed (lightweight).
     * Hidden input #deptIdHidden holds the actual department_id for POST.
     */
    var departments = @json($departments->map(fn($d) => ['id' => $d->id, 'name' => $d->name])->values());

    var searchField   = document.getElementById('deptSearchField');
    var hiddenInput   = document.getElementById('deptIdHidden');
    var resultsList   = document.getElementById('deptResultsList');
    var selectedBadge = document.getElementById('deptSelectedBadge');
    var selectedName  = document.getElementById('deptSelectedName');
    var clearBtn      = document.getElementById('deptClearBtn');
    var noResult      = document.getElementById('deptNoResult');

    /* ── Render results list ───────────────────────────────── */
    function renderResults(items) {
        resultsList.innerHTML = '';

        if (!items.length) {
            noResult.style.display  = '';
            resultsList.style.display = 'none';
            return;
        }

        noResult.style.display = 'none';

        items.forEach(function (dept) {
            var btn = document.createElement('button');
            btn.type      = 'button';
            btn.className = 'list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 px-3';
            btn.style.fontSize = '.88rem';
            btn.innerHTML =
                '<i class="fa-solid fa-building-columns text-primary fa-sm"></i>' +
                '<span>' + esc(dept.name) + '</span>';
            btn.addEventListener('mousedown', function (e) {
                // mousedown fires before blur — prevent blur closing list first
                e.preventDefault();
                selectDept(dept.id, dept.name);
            });
            resultsList.appendChild(btn);
        });

        resultsList.style.display = '';
        searchField.setAttribute('aria-expanded', 'true');
    }

    /* ── Filter departments by query ───────────────────────── */
    function filterDepts(q) {
        if (!q.trim()) return departments;
        var lower = q.toLowerCase();
        return departments.filter(function (d) {
            return d.name.toLowerCase().indexOf(lower) !== -1;
        });
    }

    /* ── Confirm a department selection ────────────────────── */
    function selectDept(id, name) {
        hiddenInput.value       = id;
        searchField.value       = name;
        selectedName.textContent= name;
        selectedBadge.style.display = '';
        resultsList.style.display   = 'none';
        resultsList.innerHTML       = '';
        noResult.style.display      = 'none';
        searchField.setAttribute('aria-expanded', 'false');
        // Remove any validation error styling
        searchField.classList.remove('is-invalid');
    }

    /* ── Clear selection ────────────────────────────────────── */
    function clearSelection() {
        hiddenInput.value   = '';
        searchField.value   = '';
        selectedBadge.style.display = 'none';
        selectedName.textContent    = '';
        resultsList.style.display   = 'none';
        resultsList.innerHTML       = '';
        noResult.style.display      = 'none';
        searchField.focus();
    }

    clearBtn.addEventListener('click', clearSelection);

    /* ── Typing in the search field ─────────────────────────── */
    searchField.addEventListener('input', function () {
        // Clear the confirmed ID whenever the user modifies the text
        hiddenInput.value = '';
        selectedBadge.style.display = 'none';
        noResult.style.display      = 'none';

        var q = searchField.value;
        if (!q.trim()) {
            resultsList.style.display = 'none';
            resultsList.innerHTML     = '';
            return;
        }
        renderResults(filterDepts(q));
    });

    /* ── Show all results on focus if empty ─────────────────── */
    searchField.addEventListener('focus', function () {
        if (!searchField.value.trim() && !hiddenInput.value) {
            renderResults(departments);
        } else if (searchField.value.trim() && !hiddenInput.value) {
            renderResults(filterDepts(searchField.value));
        }
    });

    /* ── Hide results when clicking outside ─────────────────── */
    document.addEventListener('click', function (e) {
        var wrap = document.getElementById('deptSearchWrap');
        if (!wrap.contains(e.target)) {
            resultsList.style.display = 'none';
            searchField.setAttribute('aria-expanded', 'false');
            // If user left the field without selecting, show error
            if (searchField.value.trim() && !hiddenInput.value) {
                noResult.style.display  = '';
                searchField.classList.add('is-invalid');
            }
        }
    });

    /* ── Keyboard navigation ─────────────────────────────────── */
    searchField.addEventListener('keydown', function (e) {
        var items = resultsList.querySelectorAll('.list-group-item');
        if (!items.length) return;

        var focused = resultsList.querySelector('.list-group-item:focus');
        var idx     = Array.from(items).indexOf(focused);

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            var next = items[idx + 1] || items[0];
            next.focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            var prev = items[idx - 1] || items[items.length - 1];
            prev.focus();
        } else if (e.key === 'Escape') {
            resultsList.style.display = 'none';
            searchField.focus();
        }
    });

    /* ── Form submit validation ──────────────────────────────── */
    document.getElementById('createFileForm').addEventListener('submit', function (e) {
        if (!hiddenInput.value) {
            e.preventDefault();
            searchField.classList.add('is-invalid');
            noResult.style.display = '';
            noResult.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Please select a department from the list.';
            searchField.focus();
        }
    });

    /* ── Utility: HTML escape ────────────────────────────────── */
    function esc(str) {
        var d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

})();
</script>
@endpush
