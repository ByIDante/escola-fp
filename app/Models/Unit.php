<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Unit
 *
 * @property int $id
 * @property string $title
 * @property int $module_id
 * @property int|null $teacher_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @mixin IdeHelperBook
 */
class Unit extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'units';

    protected $casts = [
        'title' => 'string',
        'module_id' => 'integer',
        'teacher_id' => 'integer',
    ];

    protected $fillable = [
        'title',
        'module_id',
        'teacher_id',
        'created_at',
        'updated_at'
    ];

    /* RELATIONS */

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
