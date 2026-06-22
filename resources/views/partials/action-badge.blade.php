@php
$map = [
    'created'     => 'badge-created',
    'requested'   => 'badge-pending',
    'approved'    => 'badge-approved',
    'rejected'    => 'badge-rejected',
    'transferred' => 'badge-transferred',
    'delivered'   => 'badge-delivered',
];
$cls = $map[$action ?? ''] ?? 'badge-draft';
@endphp
<span class="badge-status {{ $cls }}">{{ ucfirst($action ?? 'Unknown') }}</span>
