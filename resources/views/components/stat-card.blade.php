@props([
    'label',
    'value',
    'icon' => 'bi-graph-up',
    'tone' => 'primary',
    'hint' => null,
])

<div class="col-6 col-xl-3">
    <div class="card stat-card h-100" style="--stat-tone: var(--bs-{{ $tone }})">
        <div class="card-body">
            <div class="stat-label">{{ $label }}</div>
            <div class="stat-value">{{ $value }}</div>
            @if($hint)
                <div class="stat-hint text-truncate">{{ $hint }}</div>
            @endif
            <i class="bi {{ $icon }} stat-icon"></i>
        </div>
    </div>
</div>
