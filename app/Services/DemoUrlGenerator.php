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
            request()->is(['demo*', 'courier/demo*'])
        ) {
            if ($this->routes->getByName('demo.' . $name)) {
                $name = 'demo.' . $name;
            }
        }

        $url = parent::route($name, $parameters, $absolute);

        if (!str_contains($url, '/courier')) {
            if ($absolute) {
                $parts = parse_url($url);
                $path = '/courier/' . ltrim($parts['path'] ?? '', '/');
                $url = ($parts['scheme'] ?? '') . '://' . ($parts['host'] ?? '')
                    . (isset($parts['port']) ? ':' . $parts['port'] : '')
                    . $path
                    . (isset($parts['query']) ? '?' . $parts['query'] : '')
                    . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
            } else {
                $url = '/courier/' . ltrim($url, '/');
            }
        }

        return $url;
    }
}