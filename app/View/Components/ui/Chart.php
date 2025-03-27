<?php

namespace App\View\Components\ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Chart extends Component
{
    /**
     * The chart type (line, bar, pie, etc.)
     */
    public string $type;

    /**
     * Unique ID for the chart canvas
     */
    public string $id;

    /**
     * Chart data (labels and datasets)
     */
    public array $data;

    /**
     * Chart options
     */
    public array $options;

    /**
     * Chart height
     */
    public string $height;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $type = 'line',
        $id = null,
        $data = [],
        $options = [],
        $height = '300px'
    ) {
        $this->type = $type;
        $this->id = $id ?? 'chart-' . uniqid();
        $this->data = $data;
        $this->options = $options;
        $this->height = $height;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.chart');
    }
}
