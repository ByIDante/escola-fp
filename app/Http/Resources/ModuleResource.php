<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Module
 */
final class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        /** @var Module $module */
        $module = $this->resource;

        return [
            'id' => $module->id,
            'name' => $module->name,
            'created_at' => $module->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $module->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
