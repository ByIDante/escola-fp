<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Unit
 */
final class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        /** @var Unit $unit */
        $unit = $this->resource;

        return [
            'id' => $unit->id,
            'title' => $unit->title,
            'module_id' => $unit->module_id,
            'teacher_id' => $unit->teacher_id,
            'created_at' => $unit->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $unit->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
