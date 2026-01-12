<?php

namespace App;

use App\Models\Quest;

trait Generator
{
    protected function questCode()
    {
        do {
            $questCode = bin2hex(random_bytes(8));
        } while (Quest::where('code', $questCode)->first());

        return $questCode;
    }
}
