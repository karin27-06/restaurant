<?php

namespace App\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterById
{
    public function __construct(private ?int $id) {}

    public function __invoke(Builder $builder, Closure $next)
    {
        if (!$this->id) {
            return $next($builder);
        }

        // Filtra por id si se proporciona
        $builder->where('id', $this->id);

        return $next($builder);
    }
}
