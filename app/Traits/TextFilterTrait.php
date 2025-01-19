<?php

namespace App\Traits;

use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

trait TextFilterTrait
{
    protected static function makeTextFilter(string $fieldName, ?string $label = null): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make($fieldName)
            ->form([
                Forms\Components\TextInput::make($fieldName)
                    ->label($label ?? ucfirst($fieldName)),
            ])
            ->query(function (Builder $query, array $data) use ($fieldName) {
                return $query->when($data[$fieldName], function (Builder $q, $value) use ($fieldName) {
                    return $q->where($fieldName, 'like', "%{$value}%");
                });
            });
    }
}
