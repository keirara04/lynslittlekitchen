<?php

namespace App\Http\Resources\Concerns;

use Illuminate\Http\Request;

trait HasLocalizedFields
{
    protected function localized(Request $request, string $field): mixed
    {
        $translated = $this->resource->{"{$field}_ms"} ?? null;

        return $request->header('X-Locale') === 'ms' && $translated
            ? $translated
            : $this->resource->{$field};
    }
}
