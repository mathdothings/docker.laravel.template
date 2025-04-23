<?php

namespace App\DTOs\Graphics;

class BarGraphicChartDTO
{
    private $data = [
        'x' => [],
        'y' => [],
        'name' => '',
        'type' => 'bar',
        'marker' => [
            'color' => '',
            'cornerradius' => 10,
        ],
        'active' => true,
        'code' => null,
        'hovertemplate' => '',
    ];

    /**
     * Constructor
     *
     * @param  string  $name  The name of the chart
     * @param  string  $type  The chart type (default: 'bar')
     * @param  mixed  $code  The code identifier
     */
    public function __construct(string $name = '', string $type = 'bar', $code = null)
    {
        $this->data['name'] = $name;
        $this->data['type'] = $type;
        $this->data['code'] = $code;
    }

    /**
     * Add x-axis data
     *
     * @param  mixed  $value  The value to add to x-axis
     */
    public function addX($value): self
    {
        $this->data['x'][] = $value;

        return $this;
    }

    /**
     * Add y-axis data
     *
     * @param  mixed  $value  The value to add to y-axis
     */
    public function addY($value): self
    {
        $this->data['y'][] = $value;

        return $this;
    }

    /**
     * Set x-axis data
     *
     * @param  array  $values  Array of x-axis values
     */
    public function setX(array $values): self
    {
        $this->data['x'] = $values;

        return $this;
    }

    /**
     * Set y-axis data
     *
     * @param  array  $values  Array of y-axis values
     */
    public function setY(array $values): self
    {
        $this->data['y'] = $values;

        return $this;
    }

    /**
     * Set the name
     *
     * @param  string  $name  The name to set
     */
    public function setName(string $name): self
    {
        $firstAndLast = explode(' ', $name);
        $this->data['name'] = $firstAndLast[0].' '.$firstAndLast[count($firstAndLast) - 1];

        return $this;
    }

    /**
     * Set the chart type
     *
     * @param  string  $type  The chart type
     */
    public function setType(string $type): self
    {
        $this->data['type'] = $type;

        return $this;
    }

    /**
     * Set the marker color
     *
     * @param  string  $color  The color in hex format (e.g., '#444')
     */
    public function setMarkerColor(string $color): self
    {
        $this->data['marker']['color'] = $color;

        return $this;
    }

    public function setHoverTemplate(string $template): self
    {
        $this->data['hovertemplate'] = $template;

        return $this;
    }

    /**
     * Set the marker corner radius
     *
     * @param  int  $radius  The corner radius
     */
    public function setMarkerCornerRadius(int $radius): self
    {
        $this->data['marker']['cornerradius'] = $radius;

        return $this;
    }

    /**
     * Set the active status
     *
     * @param  bool  $active  The active status
     */
    public function setActive(bool $active = true): self
    {
        $this->data['active'] = $active;

        return $this;
    }

    /**
     * Set the code
     *
     * @param  mixed  $code  The code identifier
     */
    public function setCode($code): self
    {
        $this->data['code'] = $code;

        return $this;
    }

    /**
     * Get the array representation
     *
     * @return array The data array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Get JSON representation
     *
     * @return string JSON encoded string
     */
    public function toJson(): string
    {
        return json_encode($this->data);
    }

    /**
     * Reset the data arrays (x and y)
     */
    public function resetData(): self
    {
        $this->data['x'] = [];
        $this->data['y'] = [];

        return $this;
    }

    /**
     * Merge with additional data
     *
     * @param  array  $additionalData  Additional data to merge
     */
    public function merge(array $additionalData): self
    {
        $this->data = array_merge($this->data, $additionalData);

        return $this;
    }
}
