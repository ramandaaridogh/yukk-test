<?php

namespace App\Models;

use App\Enums\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionType extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_image' => 'boolean',
		'type' => Type::class,
    ];

    protected $fillable = [
        'code',
        'name',
        'type',
        'has_image',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get all of the transactions for the TransactionType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
