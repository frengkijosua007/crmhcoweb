@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">
                <i class="bi bi-house-door"></i>
            </a>
        </li>
        @foreach($breadcrumbs as $breadcrumb)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $breadcrumb['label'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif