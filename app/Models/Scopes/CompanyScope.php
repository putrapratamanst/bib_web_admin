<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
{
    // Shared master data keeps company_id for ownership, but is visible to both companies.
    private const SHARED_TABLES = [
        'contacts',
        'contact_types',
        'contact_groups',
        'billing_addresses',
    ];

    public static function isShared(Model $model): bool
    {
        return in_array($model->getTable(), self::SHARED_TABLES, true);
    }

    public function apply(Builder $builder, Model $model): void
    {
        if (self::isShared($model)) {
            return;
        }

        if (! Auth::check()) {
            return;
        }

        $companyId = Auth::user()->company_id;

        if ($companyId) {
            $builder->where($model->qualifyColumn('company_id'), $companyId);
        } else {
            $builder->whereRaw('1 = 0');
        }
    }
}
