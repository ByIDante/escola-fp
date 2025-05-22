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
 * Class Teacher
 *
 * @property int $id
 * @property string $user_id
 * @property string $first_name
 * @property string $last_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @mixin IdeHelperBook
 */
class Teacher extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'teachers';

    protected $casts = [
        'user_id' => 'string',
        'first_name' => 'string',
        'last_name' => 'string'
    ];

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'created_at',
        'updated_at'
    ];

    /* RELATIONS */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
