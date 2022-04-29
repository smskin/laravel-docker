<?php

namespace App\Modules\Core\Helpers;

use App\Modules\Core\Helpers\Imaginary\Imaginary;

class Helper
{
    public function imaginary(): Imaginary
    {
        return app(Imaginary::class);
    }
}
