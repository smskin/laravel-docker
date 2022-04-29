<?php

namespace App\Modules\Core\Helpers\Imaginary;

use App\Modules\Core\Helpers\Imaginary\Actions\Convert;

class Imaginary
{
    protected string $endpoint;

    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function url(string $url, string $type = 'webp'): string
    {
        $params = [
            'url' => $url,
            'type' => $type
        ];
        return $this->endpoint . '/convert?' . http_build_query($params);
    }

    public function convert(Convert $convert): string
    {
        return $this->endpoint . '/convert?' . http_build_query($convert->toArray());
    }
}
