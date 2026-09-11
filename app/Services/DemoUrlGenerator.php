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

        if (request() && request()->is('courier*')) {
            if ($absolute) {
                $url = str_replace(
                    'http://187.127.204.45/',
                    'http://187.127.204.45/courier/',
                    $url
                );
            } else {
                $url = '/courier' . $url;
            }
        }

        return $url;
    }
}