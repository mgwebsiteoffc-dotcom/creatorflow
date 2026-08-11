@props(['blocks' => []])
@foreach($blocks as $block)
    <script type="application/ld+json">
    {!! json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endforeach
