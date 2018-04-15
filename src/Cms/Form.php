<?php

namespace Kirby\Cms;

use Kirby\Form\Form as BaseForm;

class Form extends BaseForm
{
    public static function for(Model $model, array $props = [])
    {

        // set a few defaults
        $props['values'] = array_merge($model->content()->toArray(), $props['values'] ?? []);
        $props['fields'] = $props['fields'] ?? [];

        // search for the blueprint
        if (method_exists($model, 'blueprint') === true && $blueprint = $model->blueprint()) {
            $props['fields'] = $blueprint->fields()->toArray();

            // add the title field for sites and pages
            if (isset($props['fields']['title']) === false) {
                if (is_a($model, Page::class) === true || is_a($model, Site::class)) {
                    $props['fields']['title'] = [
                        'type' => 'hidden'
                    ];
                }
            }
        }

        // create generic fields for each value
        if (empty($props['fields'])) {
            $props['fields'] = [];

            foreach ($props['values'] as $name => $value) {
                $props['fields'][$name] = [
                    'type' => 'hidden'
                ];
            }
        }

        return new static($props);
    }
}
