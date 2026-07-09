@extends('layouts.app')
@section('title', 'Transfer File')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('files.index') }}">Files</a></li>
<li class="breadcrumb-item active">Transfer</li>
@endsection

@push('styles')
<style>
/* ── Transfer Receiver Dropdown ─────────────────────────── */
.trf-select-wrap { position: relative; }

.trf-dropdown {
    width: 100%;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(15,23,42,.10);
    overflow: hidden;
    display: none;
    position: absolute;
    z-index: 1060;
    top: calc(100% + 4px);
    left: 0;
    max-height: 280px;
    overflow-y: auto;
    animation: trfDrop .14s ease-out;
}
.trf-dropdown.open { display: block; }

@keyframes trfDrop {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.trf-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background .12s;
    font-size: .88rem;
    color: #1e293b;
}
.trf-option:last-child { border-bottom: none; }
.trf-option:hover { background: #f0f7ff; }
.trf-option.trf-opt-other {
    color: #7c3aed;
    font-weight: 700;
    border-top: 1.5px solid #e2e8f0;
    background: #faf5ff;
}
.trf-option.trf-opt-other:hover { background: #ede9fe; }

.trf-user-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: #dbeafe; color: #2563eb;
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; font-weight: 800; flex-shrink: 0;
}

.trf-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    background: #fff;
    transition: border-color .15s, box-shadow .15s;
    font-size: .88rem;
    color: #64748b;
    user-select: none;
    min-height: 46px;
}
.trf-trigger:hover, .trf-trigger.open {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
.trf-trigger .trf-selected-preview {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 0;
}
.trf-trigger .trf-chevron { transition: transform .18s; }
.trf-trigger.open .trf-chevron { transform: rotate(180deg); }

/* ── Dept search within dropdown ─────────────────────────── */
.trf-dept-search-wrap {
    padding: 10px 14px;
    border-bottom: 1.5px solid #e2e8f0;
    background: #faf5ff;
    display: none;
}
.trf-dept-search-wrap.visible { display: block; }
.trf-dept-search-input {
    width: 100%;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    padding: 7px 12px;
    font-size: .85rem;
    outline: none;
    transition: border-color .15s;
}
.trf-dept-search-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.1); }

.trf-dept-results { }
.trf-dept-result-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 14px; cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    font-size: .85rem; color: #1e293b;
    transition: background .12s;
}
.trf-dept-result-item:hover { background: #ede9fe; color: #4f46e5; }
.trf-dept-result-item:last-child { border-bottom: none; }
.trf-dept-result-empty { padding: 10px 14px; font-size: .82rem; color: #9ca3af; }

/* ── Selected dept badge ─────────────────────────────────── */
.trf-dept-selected-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #ede9fe; color: #5b21b6;
    padding: 3px 10px; border-radius: 999px;
    font-size: .78rem; font-weight: 700;
    margin-top: 6px;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Transfer File</h1>
        <div class="page-subtitle">Route this file to another user or department</div>
    </div>
    <a href="{{ route('files.index') }}" class="btn-portal-outline">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
</div>

<div class="row g-3">

    {{-- File Summary Card --}}
    <div class="col-md-4">
        <div class="portal-card">
            <div class="card-header">
                <i class="fa-solid fa-file-lines me-2 text-primary"></i>File Details
            </div>
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
                <div class="mb-3">
                    <div class="text-muted fs-sm mb-1">Current Holder</div>
                    <div class="fw-700">{{ auth()->user()->name }}</div>
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
            <h5 class="fw-700 mb-1">
                <i class="fa-solid fa-right-left me-2 text-primary"></i>Select Recipient
            </h5>
            <p class="text-muted fs-sm mb-4">
                Choose a user from your department or select <strong>Other Department</strong> to transfer across departments.
            </p>

            <form action="{{ route('files.transfer.store') }}" method="POST"
                  id="transferForm" class="portal-form">
                @csrf
                <input type="hidden" name="file_record_uuid"  value="{{ $file->uuid }}">
                <input type="hidden" name="destination_type"  id="inp_dest_type"  value="">
                <input type="hidden" name="to_user_id"         id="inp_to_user"    value="">
                <input type="hidden" name="department_id"      id="inp_dept_id"    value="">

                {{-- ── Unified Receiver Dropdown ─────────────── --}}
                <div class="mb-4">
                    <label class="form-label fw-600">
                        Transfer To <span class="required-star">*</span>
                    </label>

                    <div class="trf-select-wrap" id="trfSelectWrap">

                        {{-- Trigger button --}}
                        <div class="trf-trigger" id="trfTrigger" tabindex="0" role="combobox"
                             aria-haspopup="listbox" aria-expanded="false">
                            <span class="trf-selected-preview" id="trfPreview">
                                <span class="text-muted">— Select recipient —</span>
                            </span>
                            <i class="fa-solid fa-chevron-down trf-chevron text-muted"></i>
                        </div>

                        {{-- Dropdown panel --}}
                        <div class="trf-dropdown" id="trfDropdown" role="listbox">

                            {{-- Same-dept user list --}}
                            <div id="trfUserList">
                                @forelse($sameDeptUsers as $u)
                                <div class="trf-option trf-opt-user"
                                     data-user-id="{{ $u->id }}"
                                     data-user-name="{{ $u->name }}"
                                     data-desig="{{ $u->designation->name ?? '' }}"
                                     role="option">
                                    <div class="trf-user-avatar">
                                        {{ strtoupper(substr($u->name,0,1)) }}{{ strtoupper(substr(explode(' ',$u->name)[1] ?? 'X',0,1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-600">{{ $u->name }}</div>
                                        @if($u->designation && $u->designation->name !== '—')
                                        <div class="text-muted" style="font-size:.75rem;">{{ $u->designation->name }}</div>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="trf-dept-result-empty">
                                    <i class="fa-solid fa-users-slash me-1"></i>No other users in your department.
                                </div>
                                @endforelse
                            </div>

                            {{-- Dept search section (shown when "Other Department" clicked) --}}
                            <div class="trf-dept-search-wrap" id="trfDeptSearchWrap">
                                <div class="fw-600 mb-2" style="font-size:.78rem;color:#7c3aed;letter-spacing:.04em;text-transform:uppercase;">
                                    <i class="fa-solid fa-building-columns me-1"></i>Search Department
                                </div>
                                <input type="text"
                                       id="trfDeptInput"
                                       class="trf-dept-search-input"
                                       placeholder="Type department name…"
                                       autocomplete="off">
                                <div class="trf-dept-results mt-1" id="trfDeptResults"></div>
                            </div>

                            {{-- Divider + "Other Department" option --}}
                            <div class="trf-option trf-opt-other" id="trfOptOtherDept" role="option">
                                <div class="trf-user-avatar" style="background:#ede9fe;color:#7c3aed;">
                                    <i class="fa-solid fa-building-columns fa-xs"></i>
                                </div>
                                <div>
                                    <div>Other Department</div>
                                    <div style="font-size:.72rem;font-weight:400;color:#9333ea;">
                                        Transfer to another department
                                    </div>
                                </div>
                            </div>

                        </div>{{-- /.trf-dropdown --}}
                    </div>{{-- /.trf-select-wrap --}}

                    {{-- Selected dept badge (shown after dept chosen) --}}
                    <div id="trfDeptBadge" style="display:none;">
                        <span class="trf-dept-selected-badge">
                            <i class="fa-solid fa-building-columns"></i>
                            <span id="trfDeptBadgeName"></span>
                            <button type="button" onclick="clearDeptSelection()"
                                    style="background:none;border:none;padding:0;color:#7c3aed;line-height:1;cursor:pointer;"
                                    title="Clear">
                                <i class="fa-solid fa-xmark fa-xs"></i>
                            </button>
                        </span>
                    </div>

                    @error('destination_type')
                    <div class="text-danger fs-sm mt-1">{{ $message }}</div>
                    @enderror
                    @error('to_user_id')
                    <div class="text-danger fs-sm mt-1">{{ $message }}</div>
                    @enderror
                    @error('department_id')
                    <div class="text-danger fs-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remarks --}}
                <div class="mb-4">
                    <label class="form-label fw-600">
                        <i class="fa-solid fa-comment-dots me-1 text-muted"></i>Remarks
                    </label>
                    <textarea name="remarks" class="form-control" rows="3"
                        placeholder="Enter notes or instructions for the recipient…">{{ old('remarks') }}</textarea>
                    <div class="form-text text-muted">
                        These remarks will appear in the file journey timeline.
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-portal-primary" id="submitBtn">
                        <i class="fa-solid fa-paper-plane me-1"></i>Transfer Now
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
    var trigger       = document.getElementById('trfTrigger');
    var dropdown      = document.getElementById('trfDropdown');
    var preview       = document.getElementById('trfPreview');
    var userOpts      = document.querySelectorAll('.trf-opt-user');
    var otherOpt      = document.getElementById('trfOptOtherDept');
    var deptSearchWrap = document.getElementById('trfDeptSearchWrap');
    var deptInput     = document.getElementById('trfDeptInput');
    var deptResults   = document.getElementById('trfDeptResults');
    var deptBadge     = document.getElementById('trfDeptBadge');
    var deptBadgeName = document.getElementById('trfDeptBadgeName');

    var inpDestType   = document.getElementById('inp_dest_type');
    var inpToUser     = document.getElementById('inp_to_user');
    var inpDeptId     = document.getElementById('inp_dept_id');

    var deptSearchTimer = null;
    var selectedDeptId  = null;
    var isOpen = false;

    /* ── Open / close ──────────────────────────────────────── */
    function openDropdown() {
        dropdown.classList.add('open');
        trigger.classList.add('open');
        trigger.setAttribute('aria-expanded', 'true');
        isOpen = true;
    }
    function closeDropdown() {
        dropdown.classList.remove('open');
        trigger.classList.remove('open');
        trigger.setAttribute('aria-expanded', 'false');
        isOpen = false;
    }

    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        isOpen ? closeDropdown() : openDropdown();
    });
    trigger.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); isOpen ? closeDropdown() : openDropdown(); }
        if (e.key === 'Escape') closeDropdown();
    });
    document.addEventListener('click', function (e) {
        if (!document.getElementById('trfSelectWrap').contains(e.target)) closeDropdown();
    });

    /* ── Select a same-dept user ────────────────────────────── */
    userOpts.forEach(function (opt) {
        opt.addEventListener('click', function () {
            var uid   = opt.dataset.userId;
            var uname = opt.dataset.userName;
            var udesig= opt.dataset.desig;

            inpDestType.value = 'same';
            inpToUser.value   = uid;
            inpDeptId.value   = '';
            selectedDeptId    = null;
            deptBadge.style.display = 'none';
            deptSearchWrap.classList.remove('visible');

            preview.innerHTML =
                '<div class="trf-user-avatar" style="width:28px;height:28px;font-size:.65rem;">' +
                escHtml(uname.split(' ').map(function(w){return w[0]||'';}).join('').substring(0,2).toUpperCase()) +
                '</div>' +
                '<div><span class="fw-700">' + escHtml(uname) + '</span>' +
                (udesig ? '<span class="text-muted ms-1" style="font-size:.75rem;">' + escHtml(udesig) + '</span>' : '') +
                '</div>';

            closeDropdown();
        });
    });

    /* ── Select "Other Department" ──────────────────────────── */
    otherOpt.addEventListener('click', function () {
        inpDestType.value = 'other';
        inpToUser.value   = '';
        inpDeptId.value   = '';
        selectedDeptId    = null;

        preview.innerHTML =
            '<div class="trf-user-avatar" style="width:28px;height:28px;font-size:.65rem;background:#ede9fe;color:#7c3aed;">' +
            '<i class="fa-solid fa-building-columns fa-xs"></i></div>' +
            '<span class="fw-700" style="color:#7c3aed;">Other Department</span>';

        deptSearchWrap.classList.add('visible');
        deptResults.innerHTML = '';
        deptInput.value = '';
        deptBadge.style.display = 'none';
        deptInput.focus();
    });

    /* ── Dept AJAX search ───────────────────────────────────── */
    deptInput.addEventListener('input', function () {
        var q = deptInput.value.trim();
        clearTimeout(deptSearchTimer);
        if (q.length < 2) { deptResults.innerHTML = ''; return; }
        deptSearchTimer = setTimeout(function () { fetchDepts(q); }, 250);
    });
    deptInput.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDropdown();
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
        .then(function(r){ return r.ok ? r.json() : []; })
        .then(function(data){ renderDeptResults(data); })
        .catch(function(){ deptResults.innerHTML = ''; });
    }

    function renderDeptResults(data) {
        deptResults.innerHTML = '';
        if (!data || !data.length) {
            deptResults.innerHTML = '<div class="trf-dept-result-empty"><i class="fa-solid fa-circle-xmark me-1"></i>No department found.</div>';
            return;
        }
        data.forEach(function (dept) {
            var item = document.createElement('div');
            item.className = 'trf-dept-result-item';
            item.innerHTML =
                '<div class="trf-user-avatar" style="background:#ede9fe;color:#7c3aed;width:28px;height:28px;font-size:.6rem;">' +
                '<i class="fa-solid fa-building-columns fa-xs"></i></div>' +
                '<span>' + escHtml(dept.name) + '</span>';
            item.addEventListener('click', function () { selectDept(dept.id, dept.name); });
            deptResults.appendChild(item);
        });
    }

    function selectDept(id, name) {
        selectedDeptId    = id;
        inpDeptId.value   = id;
        deptBadgeName.textContent = name;
        deptBadge.style.display   = '';
        deptInput.value   = name;
        deptResults.innerHTML = '';
        closeDropdown();
    }

    window.clearDeptSelection = function () {
        selectedDeptId  = null;
        inpDeptId.value = '';
        inpDestType.value = '';
        deptBadge.style.display = 'none';
        preview.innerHTML = '<span class="text-muted">— Select recipient —</span>';
        deptInput.value = '';
    };

    function escHtml(str) {
        var d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

    /* ── Form submit validation ─────────────────────────────── */
    document.getElementById('transferForm').addEventListener('submit', function (e) {
        var type = inpDestType.value;
        if (!type) {
            e.preventDefault();
            alert('Please select a recipient first.');
            return;
        }
        if (type === 'same' && !inpToUser.value) {
            e.preventDefault();
            alert('Please select a user to transfer to.');
            return;
        }
        if (type === 'other' && !inpDeptId.value) {
            e.preventDefault();
            alert('Please select a department from the search results.');
            return;
        }
        if (!confirm('Confirm file transfer?')) {
            e.preventDefault();
        }
    });

})();
</script>
@endpush
