{{--
    Shared linked-list timeline component.
    Usage:
        <x-file-timeline :movements="$file->movements" :current-user-id="$file->current_user_id" />
    or pass pre-sorted collection:
        <x-file-timeline :movements="$timeline" :current-user-id="$file->current_user_id" />
--}}
@props(['movements', 'currentUserId' => null])

@php
    $moves = $movements->sortBy('created_at')->values();
@endphp

@if($moves->isEmpty())
<div class="empty-state py-4">
    <i class="fa-solid fa-timeline fa-2x text-muted mb-2"></i>
    <p class="text-muted mb-0">No movement history recorded yet.</p>
</div>
@else

{{-- ── VERTICAL LINKED-LIST TIMELINE ─────────────────────────── --}}
<div class="ftl-wrap">
    @foreach($moves as $idx => $move)
    @php
        $isCreated  = $move->action === 'created';
        $isDeptMove = $move->fromDept && $move->toDept
                      && (int) $move->fromDept->id !== (int) $move->toDept->id;

        if ($isCreated) {
            $person    = $move->fromUser;
            $deptLabel = $move->fromDept?->name ?? '—';
            $cardType  = 'created';
        } else {
            $person    = $move->toUser;
            $deptLabel = $move->toDept?->name ?? '—';
            $cardType  = $isDeptMove ? 'dept' : 'transfer';
        }

        // Highlight current holder — last movement whose to_user matches current_user_id
        $isCurrent = $currentUserId
            && $move === $moves->last()
            && ($isCreated
                ? ($move->fromUser && (int)$move->fromUser->id === (int)$currentUserId)
                : ($move->toUser   && (int)$move->toUser->id   === (int)$currentUserId));
    @endphp

    <div class="ftl-item {{ $isCurrent ? 'ftl-current' : '' }}">

        {{-- Vertical connector (not on first) --}}
        @if($idx > 0)
        <div class="ftl-connector" aria-hidden="true"></div>
        @endif

        {{-- Card --}}
        <div class="ftl-card ftl-card-{{ $cardType }} {{ $isCurrent ? 'ftl-card-current' : '' }}">

            @if($isCurrent)
            <span class="ftl-badge-current">
                <i class="fa-solid fa-circle-dot me-1"></i>Current Holder
            </span>
            @endif

            <div class="ftl-card-inner">
                {{-- Avatar --}}
                <div class="ftl-avatar-wrap">
                    @if($person && $person->photo_url)
                    <img src="{{ $person->photo_url }}"
                         alt="{{ $person->name }}"
                         class="ftl-avatar"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="ftl-avatar-initials ftl-bg-{{ $cardType }}" style="display:none;">
                        {{ $person->initials }}
                    </div>
                    @elseif($person)
                    <div class="ftl-avatar-initials ftl-bg-{{ $cardType }}">
                        {{ $person->initials }}
                    </div>
                    @else
                    <div class="ftl-avatar-initials ftl-bg-dept">
                        <i class="fa-solid fa-building fa-xs"></i>
                    </div>
                    @endif
                </div>

                {{-- Details --}}
                <div class="ftl-details">
                    <div class="ftl-name">{{ $person?->name ?? $deptLabel }}</div>
                    <div class="ftl-dept">
                        <i class="fa-solid fa-building-columns fa-xs me-1"></i>{{ $deptLabel }}
                    </div>
                    <div class="ftl-meta">
                        <span><i class="fa-regular fa-calendar fa-xs me-1"></i>{{ $move->created_at->format('d M Y') }}</span>
                        <span class="ms-2"><i class="fa-regular fa-clock fa-xs me-1"></i>{{ $move->created_at->format('h:i A') }}</span>
                    </div>
                    @if($move->remarks)
                    <div class="ftl-remarks">
                        <i class="fa-solid fa-quote-left fa-xs me-1 text-muted"></i>{{ $move->remarks }}
                    </div>
                    @endif
                </div>

                {{-- Action badge --}}
                <div class="ftl-action">
                    @include('partials.action-badge', ['action' => $move->action])
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>

@endif

@once
@push('styles')
<style>
/* ═══════════════════════════════════════════════════════
   FILE TIMELINE — Linked List Vertical Style
═══════════════════════════════════════════════════════ */
.ftl-wrap {
    display: flex;
    flex-direction: column;
    padding: .5rem 0;
}

/* ── Connector line ─────────────────────────────────── */
.ftl-connector {
    width: 2px;
    min-height: 28px;
    background: linear-gradient(180deg, #c7d7e8 0%, #dce8f2 100%);
    margin-left: 21px; /* aligns with avatar centre (44px/2 - 1px) */
    flex-shrink: 0;
}
.ftl-current .ftl-connector,
.ftl-wrap .ftl-item:has(+ .ftl-current) .ftl-connector {
    background: linear-gradient(180deg, #22c55e 0%, #c7d7e8 100%);
}

/* ── Card ───────────────────────────────────────────── */
.ftl-card {
    background: #fff;
    border: 1.5px solid #dce3ea;
    border-radius: 14px;
    padding: 14px 16px;
    position: relative;
    transition: box-shadow .18s, border-color .18s, transform .18s;
    margin-bottom: 0;
}
.ftl-card:hover {
    box-shadow: 0 4px 18px rgba(23,64,107,.10);
    border-color: #93c5fd;
    transform: translateX(2px);
}
.ftl-card-created  { border-left: 4px solid #7c3aed; }
.ftl-card-transfer { border-left: 4px solid #2563eb; }
.ftl-card-dept     { border-left: 4px solid #059669; }
.ftl-card-current  { border-left: 4px solid #22c55e; border-color: #bbf7d0; background: #f0fdf4; }

/* Current holder badge */
.ftl-badge-current {
    display: inline-flex;
    align-items: center;
    background: #22c55e;
    color: #fff;
    font-size: .68rem;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 999px;
    margin-bottom: 8px;
    letter-spacing: .03em;
}

/* ── Inner layout ───────────────────────────────────── */
.ftl-card-inner {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

/* ── Avatar ─────────────────────────────────────────── */
.ftl-avatar-wrap { flex-shrink: 0; }
.ftl-avatar {
    width: 44px; height: 44px;
    border-radius: 50%; object-fit: cover;
    border: 2px solid #e2e8f0;
}
.ftl-avatar-initials {
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 800; color: #fff;
    border: 2px solid rgba(255,255,255,.3);
}
.ftl-bg-created  { background: #7c3aed; }
.ftl-bg-transfer { background: #2563eb; }
.ftl-bg-dept     { background: #059669; }

/* ── Details ────────────────────────────────────────── */
.ftl-details   { flex: 1; min-width: 0; }
.ftl-name      { font-weight: 700; font-size: .92rem; color: #1e293b; margin-bottom: 2px; }
.ftl-dept      { font-size: .78rem; color: #64748b; margin-bottom: 4px; }
.ftl-meta      { font-size: .74rem; color: #94a3b8; margin-bottom: 4px; }
.ftl-remarks {
    font-size: .78rem;
    color: #475569;
    background: #f8fafc;
    border-left: 3px solid #cbd5e1;
    padding: 4px 8px;
    border-radius: 0 6px 6px 0;
    margin-top: 4px;
    word-break: break-word;
}

/* ── Action badge ───────────────────────────────────── */
.ftl-action { flex-shrink: 0; padding-top: 2px; }

/* ── Responsive ─────────────────────────────────────── */
@media (max-width: 480px) {
    .ftl-card-inner { flex-wrap: wrap; gap: 10px; }
    .ftl-action { width: 100%; }
    .ftl-connector { margin-left: 19px; }
}
</style>
@endpush
@endonce
