<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

/**
 * Class Evaluation
 *
 * @property int $id
 * @property int $student_id
 * @property int $teacher_id
 * @property int $module_id
 * @property int $unit_id
 * @property float $score
 * @property string|null $comments
 * @property Carbon $evaluation_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Student $student
 * @property-read Teacher|null $teacher
 * @property-read Module $module
 * @property-read Unit|null $unit
 * @package App\Models
 */
class Evaluation extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'evaluations';

    protected $casts = [
        'student_id' => 'integer',
        'teacher_id' => 'integer',
        'module_id' => 'integer',
        'unit_id' => 'integer',
        'score' => 'float',
        'comments' => 'string',
        'evaluation_date' => 'date',
    ];

    protected $fillable = [
        'student_id',
        'teacher_id',
        'module_id',
        'unit_id',
        'score',
        'comments',
        'evaluation_date',
        'created_at',
        'updated_at'
    ];

    /* RELATIONS */

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
    
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
