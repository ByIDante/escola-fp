<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Module
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @mixin IdeHelperBook
 */
class Module extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'modules';

    protected $casts = [
        'name' => 'string'
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at'
    ];

    /* RELATIONS */

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
