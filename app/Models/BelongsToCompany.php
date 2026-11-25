<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

trait BelongsToCompany
{
    private static array $companyScopedTables = [];

    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function (Model $model): void {
            $currentUser = Auth::user();
            $currentCompanyId = $currentUser?->company_id;

            if ($model->getAttribute('company_id')) {
                if ($currentCompanyId && $model->getAttribute('company_id') !== $currentCompanyId) {
                    throw ValidationException::withMessages([
                        'company_id' => 'Data tidak dapat dibuat untuk company lain.',
                    ]);
                }

                static::validateCompanyRelations($model);
                return;
            }

            $companyId = $currentUser ? $currentCompanyId : Company::idFor('BIB');

            if (! $companyId) {
                throw new RuntimeException('Cannot create company-owned data before the BIB company exists.');
            }

            $model->setAttribute('company_id', $companyId);
            static::validateCompanyRelations($model);
        });

        static::updating(function (Model $model): void {
            if ($model->isDirty('company_id')) {
                throw ValidationException::withMessages([
                    'company_id' => 'Company data tidak dapat dipindahkan.',
                ]);
            }

            static::validateCompanyRelations($model);
        });
    }

    protected static function validateCompanyRelations(Model $model): void
    {
        foreach ((new \ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getDeclaringClass()->getName() !== $model::class || $method->getNumberOfRequiredParameters() !== 0) {
                continue;
            }

            $returnType = $method->getReturnType();
            if (! $returnType instanceof ReflectionNamedType || ! is_a($returnType->getName(), BelongsTo::class, true)) {
                continue;
            }

            $relation = $method->invoke($model);
            $related = $relation->getRelated();
            if (CompanyScope::isShared($related)
                || in_array($related->getTable(), static::crossCompanyRelationTables(), true)) {
                continue;
            }

            $table = $related->getTable();

            if (! array_key_exists($table, self::$companyScopedTables)) {
                self::$companyScopedTables[$table] = Schema::hasColumn($table, 'company_id');
            }

            $foreignKey = $relation->getForeignKeyName();
            $foreignId = $model->getAttribute($foreignKey);

            if (! self::$companyScopedTables[$table] || ! $foreignId) {
                continue;
            }

            $relatedCompanyId = DB::table($table)
                ->where($related->getKeyName(), $foreignId)
                ->value('company_id');

            if ($relatedCompanyId !== null && $relatedCompanyId !== $model->getAttribute('company_id')) {
                throw ValidationException::withMessages([
                    $foreignKey => 'Data yang dipilih berasal dari company lain.',
                ]);
            }
        }
    }

    protected static function crossCompanyRelationTables(): array
    {
        return [];
    }
}
