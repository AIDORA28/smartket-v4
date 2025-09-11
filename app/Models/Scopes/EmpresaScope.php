<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class EmpresaScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Solo aplicar si el usuario está autenticado y tiene empresa_actual_id
        if (Auth::check() && Auth::user()->empresa_actual_id) {
            $builder->where('empresa_id', Auth::user()->empresa_actual_id);
        }
    }
}
