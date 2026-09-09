<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasDemoToken
{
    public static function bootHasDemoToken(): void
    {
        static::addGlobalScope('demo_token', function (Builder $builder) {
            // Check if request is running in web context and is_demo is active
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }

            if (request() && request()->attributes->get('is_demo')) {
                $token = request()->attributes->get('demo_token');
                if ($token) {
                    $builder->where($builder->getModel()->getTable() . '.demo_token', $token);
                }
            } else {
                $builder->whereNull($builder->getModel()->getTable() . '.demo_token');
            }
        });

        static::creating(function ($model) {
            if (request() && request()->attributes->get('is_demo')) {
                $token = request()->attributes->get('demo_token');
                if ($token && empty($model->demo_token)) {
                    $model->demo_token = $token;
                }
            }
        });
    }

    public function scopeForDemo(Builder $query, ?string $token = null): Builder
    {
        $token = $token ?? (request() ? request()->attributes->get('demo_token') : null);
        if ($token) {
            return $query->withoutGlobalScope('demo_token')->where($this->getTable() . '.demo_token', $token);
        }
        return $query;
    }
}
