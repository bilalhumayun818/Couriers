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

        $basePath = '/' . trim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/');
        $urlPath = parse_url($url, PHP_URL_PATH) ?? '';

        if ($basePath !== '/' && $urlPath !== $basePath && !str_starts_with($urlPath, $basePath . '/')) {
            if ($absolute) {
                $parts = parse_url($url);
                $path = $basePath . '/' . ltrim($parts['path'] ?? '', '/');
                $url = ($parts['scheme'] ?? '') . '://' . ($parts['host'] ?? '')
                    . (isset($parts['port']) ? ':' . $parts['port'] : '')
                    . $path
                    . (isset($parts['query']) ? '?' . $parts['query'] : '')
                    . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
            } else {
                $url = $basePath . '/' . ltrim($url, '/');
            }
        }

        return $url;
    }
}
