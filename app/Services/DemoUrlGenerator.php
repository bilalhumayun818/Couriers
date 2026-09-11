<?php

namespace App\Services;

use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;

class DemoUrlGenerator extends BaseUrlGenerator
{
    public function route($name, $parameters = [], $absolute = true)
    {
        if (
            is_string($name) &&
            !str_starts_with($name, 'demo.') &&
            request() &&
            request()->is('demo*')
        ) {
            if ($this->routes->getByName('demo.' . $name)) {
                $name = 'demo.' . $name;
            }
        }

        $url = parent::route($name, $parameters, $absolute);

        return $url;
    }
}