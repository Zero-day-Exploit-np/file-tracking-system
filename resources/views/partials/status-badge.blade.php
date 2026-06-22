@php
$map = [
    'active'           => ['class' => 'badge-active',      'label' => 'Active'],
    'pending_transfer' => ['class' => 'badge-pending',     'label' => 'Pending Transfer'],
    'pending'          => ['class' => 'badge-pending',     'label' => 'Pending'],
    'archived'         => ['class' => 'badge-archived',    'label' => 'Archived'],
    'draft'            => ['class' => 'badge-draft',       'label' => 'Draft'],
    'approved'         => ['class' => 'badge-approved',    'label' => 'Approved'],
    'rejected'         => ['class' => 'badge-rejected',    'label' => 'Rejected'],
];
$entry = $map[$status ?? ''] ?? ['class' => 'badge-draft', 'label' => ucfirst(str_replace('_',' ', $status ?? 'Unknown'))];
@endphp
<span class="badge-status {{ $entry['class'] }}">{{ $entry['label'] }}</span>
