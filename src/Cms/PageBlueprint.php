<?php

namespace Kirby\Cms;

class PageBlueprint extends Blueprint
{
    protected $num;

    public function num()
    {
        return $this->num;
    }

    public function options()
    {
        if (is_a($this->options, PageBlueprintOptions::class) === true) {
            return $this->options;
        }

        return $this->options = new PageBlueprintOptions($this->model, $this->options);
    }

    protected function setNum($num = null)
    {
        $aliases = [
            0          => 'zero',
            'date'     => '{{ page.date("Ymd") }}',
            'datetime' => '{{ page.date("YmdHi") }}',
            'sort'     => 'default',
        ];

        if (isset($aliases[$num])) {
            $num = $aliases[$num];
        }

        $this->num = $num ?? 'default';
        return $this;
    }
}
