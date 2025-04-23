<?php

namespace App\DTOs\Graphics;

class PieGraphicChartDTO
{
    private $data = [
        'labels' => [],
        'values' => [],
        'dealer_codes' => [],
        'type' => 'pie',
        'marker' => [
            'colors' => [],
        ],
        'textinfo' => 'percent',
        'insidetextfont' => [
            'family' => 'Roboto Mono',
            'size' => 10,
            'color' => '#6b7280',
        ],
        'outsidetextfont' => [
            'family' => 'Roboto Mono',
            'size' => 14,
            'color' => '#6b7280',
        ],
        'automargin' => true,
        'textposition' => 'outside',
        'hoverinfo' => 'label+percent+value',
        'hovertemplate' => '%{label}: %{percent:.1%}<extra></extra>',
        'hole' => 0,
        'pull' => 0,
        'rotation' => 0,
        'active' => true,
    ];

    /**
     * Constructor
     *
     * @param  string  $name  The name of the chart
     * @param  string  $type  The chart type (default: 'pie')
     */
    public function __construct(string $name = '', string $type = 'pie')
    {
        $this->data['name'] = $name;
        $this->data['type'] = $type;
    }

    /**
     * Add label
     *
     * @param  string  $label  The label to add
     */
    public function addLabel(string $label): self
    {
        $this->data['labels'][] = $label;

        return $this;
    }

    /**
     * Add value
     *
     * @param  float  $value  The value to add
     */
    public function addValue(float $value): self
    {
        $this->data['values'][] = $value;

        return $this;
    }

    /**
     * Set labels
     *
     * @param  array  $labels  Array of labels
     */
    public function setLabels(array $labels): self
    {
        $firstLastNames = array_map(
            function ($labels) {
                $parts = array_filter(explode(' ', trim($labels)));

                if (count($parts) === 1) {
                    return $labels;
                }

                if (count($parts) > 1) {
                    return $parts[0].' '.$parts[count($parts) - 1];
                }
            },
            $labels
        );

        $this->data['labels'] = $firstLastNames;

        return $this;
    }

    /**
     * Set values
     *
     * @param  array  $values  Array of values
     */
    public function setValues(array $values): self
    {
        $this->data['values'] = $values;

        return $this;
    }

    public function setDealersCodes(array $codes): self
    {
        $this->data['dealer_codes'] = $codes;

        return $this;
    }

    /**
     * Set the name
     *
     * @param  string  $name  The name to set
     */
    public function setFilialName(string $name): self
    {
        $this->data['filial_label'] = $name;

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
     * Set marker colors
     *
     * @param  array  $colors  Array of colors in hex format
     */
    public function setMarkerColors(array $colors): self
    {
        $this->data['marker']['colors'] = $colors;

        return $this;
    }

    /**
     * Add a single marker color
     *
     * @param  string  $color  The color in hex format
     */
    public function addMarkerColor(string $color): self
    {
        $this->data['marker']['colors'][] = $color;

        return $this;
    }

    /**
     * Set marker line properties
     *
     * @param  string  $color  Line color
     * @param  int  $width  Line width
     */
    public function setMarkerLine(string $color, int $width = 1): self
    {
        $this->data['marker']['line']['color'] = $color;
        $this->data['marker']['line']['width'] = $width;

        return $this;
    }

    /**
     * Set text display info
     *
     * @param  string  $textinfo  What text to display (e.g., 'label', 'percent', 'value', 'none')
     */
    public function setTextInfo(string $textinfo): self
    {
        $this->data['textinfo'] = $textinfo;

        return $this;
    }

    /**
     * Set text position
     *
     * @param  string  $textposition  Where to position text ('inside', 'outside', 'auto', 'none')
     */
    public function setTextPosition(string $textposition): self
    {
        $this->data['textposition'] = $textposition;

        return $this;
    }

    /**
     * Set hover info
     *
     * @param  string  $hoverinfo  What to show on hover (e.g., 'label', 'percent', 'value', 'all')
     */
    public function setHoverInfo(string $hoverinfo): self
    {
        $this->data['hoverinfo'] = $hoverinfo;

        return $this;
    }

    /**
     * Set hover template
     *
     * @param  string  $template  The hover template string
     */
    public function setHoverTemplate(string $template): self
    {
        $this->data['hovertemplate'] = $template;

        return $this;
    }

    /**
     * Set hole size (for donut charts)
     *
     * @param  float  $size  Size of hole (0-1)
     */
    public function setHole(float $size): self
    {
        $this->data['hole'] = $size;

        return $this;
    }

    /**
     * Set slice pull amount
     *
     * @param  float  $pull  How much to pull slices out (0-1)
     */
    public function setPull(float $pull): self
    {
        $this->data['pull'] = $pull;

        return $this;
    }

    /**
     * Set initial rotation angle
     *
     * @param  float  $degrees  Rotation in degrees
     */
    public function setRotation(float $degrees): self
    {
        $this->data['rotation'] = $degrees;

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
    public function setFilialCode($code): self
    {
        $this->data['filial_code'] = $code;

        return $this;
    }

    /**
     * Set start date
     *
     * @param  mixed  $date  The start date
     */
    public function setStartDate($date): self
    {
        $this->data['date_start'] = $date;

        return $this;
    }

    /**
     * Set end date
     *
     * @param  mixed  $date  The end date
     */
    public function setEndDate($date): self
    {
        $this->data['date_end'] = $date;

        return $this;
    }

    /**
     * Set dealer's code
     *
     * @param  mixed  $dealerCode  The dealer's code
     */
    public function addDealerCode($dealerCode): self
    {
        $this->data['dealer_code'][] = $dealerCode;

        return $this;
    }

    /**
     * Set filial data
     *
     * @param  mixed  $filialData  The filial data
     */
    public function setFilialData(array $filialData): self
    {
        $this->data['data']['filials'][] = $filialData;

        return $this;
    }

    /**
     * Set dealer's data
     *
     * @param  mixed  $dealerData  The dealer's data
     */
    public function setDealerData(array $dealerData): self
    {
        $this->data['data']['dealers'][] = $dealerData;

        return $this;
    }

    /**
     * Set dealer's label
     *
     * @param  mixed  $dealerLabel  The dealer's code
     */
    public function setDealerLabel($dealerLabel): self
    {
        $this->data['dealer_label'] = $dealerLabel;
        $this->data['data']['dealer_label'][] = $dealerLabel;

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
     * Reset the data arrays (labels and values)
     */
    public function resetData(): self
    {
        $this->data['labels'] = [];
        $this->data['values'] = [];

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
