<?php

namespace App\Modules\Core\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @OA\Schema(
 *     title="RPaginatedResourceCollection"
 * )
 */
class RPaginatedResourceCollection extends ResourceCollection
{
    public Collection $items;

    /**
     * @OA\Property()
     */
    public RMetaPagination $meta;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->meta = new RMetaPagination($resource);
        $this->items = $this->collection;
    }

    #[ArrayShape(['meta' => "\App\Modules\Core\Resources\RMetaPagination", 'items' => "\Illuminate\Support\Collection"])]
    public function toArray($request): array
    {
        return [
            'meta' => $this->meta,
            'items' => $this->items,
        ];
    }
}
