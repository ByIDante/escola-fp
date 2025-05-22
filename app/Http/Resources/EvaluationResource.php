<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Evaluation
 */
final class EvaluationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        /** @var Evaluation $evaluation */
        $evaluation = $this->resource;

        return [
            'id' => $evaluation->id,
            'student_id' => $evaluation->student_id,
            'teacher_id' => $evaluation->teacher_id,
            'module_id' => $evaluation->module_id,
            'unit_id' => $evaluation->unit_id,
            'score' => $evaluation->score,
            'comments' => $evaluation->comments,
            'evaluation_date' => $evaluation->evaluation_date->format('Y-m-d'),
            'created_at' => $evaluation->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $evaluation->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
