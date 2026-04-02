{{--
    Reusable sortable column header link.
    Variables: $label, $column, $sortBy, $sortDir
--}}
@php
    $isActive  = $sortBy === $column;
    $nextDir   = ($isActive && $sortDir === 'asc') ? 'desc' : 'asc';
    $iconClass = $isActive
        ? ($sortDir === 'asc' ? 'bi-sort-up' : 'bi-sort-down')
        : 'bi-arrow-down-up';
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort_by' => $column, 'sort_dir' => $nextDir]) }}"
   class="sort-link {{ $isActive ? 'text-primary fw-semibold' : '' }}">
    {{ $label }}
    <i class="bi {{ $iconClass }} ms-1"></i>
</a>
