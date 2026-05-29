<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class SpaceInsensitiveSearch
{
    public static function whereColumn(Builder $query, string $column, string $search): Builder
    {
        return self::whereExpression($query, $column, $search);
    }

    public static function whereExpression(Builder $query, string $expression, string $search): Builder
    {
        $normalizedSearch = self::normalize($search);

        return $query->whereRaw(
            self::sqlCompactExpression($expression) . ' LIKE ?',
            ["%{$normalizedSearch}%"]
        );
    }

    public static function normalize(string $value): string
    {
        return preg_replace('/[\s\/_-]+/u', '', mb_strtolower(trim($value))) ?? '';
    }

    public static function sqlCompactExpression(string $expression): string
    {
        $compact = "LOWER(COALESCE({$expression}, ''))";

        foreach ([' ', '/', '-', '_'] as $char) {
            $compact = "REPLACE({$compact}, '{$char}', '')";
        }

        return $compact;
    }
}