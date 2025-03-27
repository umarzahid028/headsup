@props([
    'type' => 'line',
    'id' => null,
    'data' => [],
    'options' => [],
    'height' => '300px'
])

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <div style="height: {{ $height }}">
        <canvas 
            id="{{ $id }}" 
            data-chart="true"
            data-chart-type="{{ $type }}"
            data-chart-data='@json($data)'
            data-chart-options='@json($options)'
        ></canvas>
    </div>
</div> 