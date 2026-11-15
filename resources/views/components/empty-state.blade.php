@props(['icon' => 'bi-inbox', 'title' => 'Sin registros', 'text' => null])

<div class="text-center py-5">
    <i class="bi {{ $icon }} empty-icon"></i>
    <div class="fw-semibold">{{ $title }}</div>
    @if($text)
        <div class="text-muted small mt-1">{{ $text }}</div>
    @endif
</div>
