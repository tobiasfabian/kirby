<?php

namespace Kirby\Cms;

class BlueprintOptions
{
    protected $model;
    protected $aliases = [];
    protected $options = [];

    public function __construct(Model $model, array $options = null)
    {
        $this->model = $model;

        if ($options !== null) {
            foreach ($options as $key => $value) {
                if (isset($this->aliases[$key]) === true) {
                    $options[$this->aliases[$key]] = $value;
                    unset($options[$key]);
                }
            }

            foreach ($this->options as $key => $default) {
                if (isset($options[$key]) === true) {
                    $this->options[$key] = (bool)$options[$key];
                }
            }
        }

    }

    protected function isAllowed(string $category, string $action): bool
    {
        $user = $this->user();

        if (empty($user) === true || $user->role()->id() === 'nobody') {
            return false;
        }

        if ($user->role()->id() === 'admin') {
            return true;
        }

        if (is_bool($this->options[$action]) === true) {
            return $this->options[$action];
        }

        return $user->permissions($category, $action);
    }

    protected function kirby()
    {
        return $this->model()->kirby();
    }

    protected function model()
    {
        return $this->model;
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->options as $key => $value) {
            $result[$key] = $this->$key();
        }

        return $result;
    }

    protected function user()
    {
        return $this->kirby()->user();
    }

}
