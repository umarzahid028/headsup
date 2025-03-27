<?php

namespace App\View\Components\Ui;

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
        string $id,
        string $type = 'line',
        string $height = '300px',
        array $data = [],
        array $options = []
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->height = $height;
        $this->data = $data;
        $this->options = $options;
    }

    /**
     * Get the encoded chart data.
     */
    public function getEncodedData(): string
    {
        return json_encode($this->data ?: ['labels' => [], 'datasets' => []]);
    }

    /**
     * Get the encoded chart options.
     */
    public function getEncodedOptions(): string
    {
        return json_encode($this->options ?: new \stdClass());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.ui.chart');
    }
}
