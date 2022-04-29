<?php

namespace App\Modules\Core\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @OA\Schema(
 *     title="RMetaPagination"
 * )
 */
class RMetaPagination extends JsonResource
{
    /**
     * @OA\Property()
     */
    public int $total;

    /**
     * @OA\Property()
     */
    public int $currentPage;

    /**
     * @OA\Property()
     */
    public int $lastPage;

    /**
     * @OA\Property()
     */
    public int $perPage;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->total = (int)$resource->total();
        $this->currentPage = (int)$resource->currentPage();
        $this->perPage = (int)$resource->perPage();
        $this->lastPage = (int)$resource->lastPage();
    }

    #[ArrayShape(['total' => "int", 'currentPage' => "int", 'lastPage' => "int", 'perPage' => "int"])]
    public function toArray($request): array
    {
        return [
            'total' => $this->total,
            'currentPage' => $this->currentPage,
            'lastPage' => $this->lastPage,
            'perPage' => $this->perPage,
        ];
    }
}
