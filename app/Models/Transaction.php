<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ammount' => 'double',
    ];

    protected $fillable = [
        'code',
        'ammount',
        'note',
        'image',
        'transaction_type_id',
        'user_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the transation_type that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transation_type(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id', 'id');
    }

    /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
