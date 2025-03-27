<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <div style="height: {{ $height }}">
        <canvas 
            id="{{ $id }}" 
            data-chart="true"
            data-chart-type="{{ $type }}"
            data-chart-data="{{ isset($data) ? json_encode($data) : '{"labels":[],"datasets":[]}' }}"
            data-chart-options="{{ isset($options) ? json_encode($options) : '{}' }}"
        ></canvas>
    </div>
</div> 