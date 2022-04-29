<?php

namespace App\Modules\Core\Helpers\Imaginary\Actions;

use App\Modules\Core\Helpers\Imaginary\Enums\TypeEnum;
use Illuminate\Contracts\Support\Arrayable;

class Convert implements Arrayable
{
    public string $type;

    public int $quality;

    public int $compression;

    public string $file;

    public string $url;

    public bool $embed;

    public bool $force;

    public int $rotate;

    public bool $noRotation;

    public bool $noProfile;

    public bool $stripMeta;

    public bool $flip;

    public bool $flop;

    public string $extend;

    public string $background;

    public string $colorspace;

    public float $sigma;

    public float $minAmpl;

    public string $field;

    public bool $interlace;

    public string $aspectRatio;

    public bool $palette;

    public function typeWebp()
    {
        $this->type = TypeEnum::WEBP;
    }

    public function toArray(): array
    {
        $data = [];
        if ($this->type) {
            $data['type'] = $this->type;
        }
        if ($this->quality) {
            $data['quality'] = $this->quality;
        }
        if ($this->compression) {
            $data['compression'] = $this->compression;
        }
        if ($this->file) {
            $data['file'] = $this->file;
        }
        if ($this->url) {
            $data['url'] = $this->url;
        }
        if ($this->embed) {
            $data['embed'] = 1;
        }
        if ($this->force) {
            $data['force'] = 1;
        }
        if ($this->rotate) {
            $data['rotate'] = $this->rotate;
        }
        if ($this->noRotation) {
            $data['norotation'] = 1;
        }
        if ($this->noProfile) {
            $data['noprofile'] = 1;
        }
        if ($this->stripMeta) {
            $data['stripmeta'] = 1;
        }
        if ($this->flip) {
            $data['flip'] = 1;
        }
        if ($this->flop) {
            $data['flop'] = 1;
        }
        if ($this->extend) {
            $data['extend'] = $this->extend;
        }
        if ($this->background) {
            $data['background'] = $this->background;
        }
        if ($this->colorspace) {
            $data['colorspace'] = $this->colorspace;
        }
        if ($this->sigma) {
            $data['sigma'] = $this->sigma;
        }
        if ($this->minAmpl) {
            $data['minampl'] = $this->minAmpl;
        }
        if ($this->field) {
            $data['field'] = $this->field;
        }
        if ($this->interlace) {
            $data['interlace'] = 1;
        }
        if ($this->aspectRatio) {
            $data['aspectratio'] = $this->aspectRatio;
        }
        if ($this->palette) {
            $data['palette'] = 1;
        }
        return $data;
    }
}
