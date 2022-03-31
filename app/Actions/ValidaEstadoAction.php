<?php

namespace App\Actions;

use App\Models\Enums\Estado;
use Lorisleiva\Actions\Concerns\AsAction;

class ValidaEstadoAction
{
    use AsAction;

    public function handle(string $siglaEstado): bool
    {
        $estados = Estado::estados();

        if (!array_key_exists($siglaEstado, $estados)) {
            return false;
        }

        return true;
    }
}
