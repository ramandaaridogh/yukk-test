<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Gender;
use App\Models\Scopes\IsActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();
		static::addGlobalScope(new IsActiveScope);
	}

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'nik',
		'name',
		'username',
		'email',
		'birth_date',
		'phone_number',
		'gender',
		'address',
		'image',
		'password',
		'pin',
		'is_active',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'pin',
		'remember_token',
		'email_verified_at',
	];

	public function setPasswordAttribute($password)
	{
		if ($password !== null & $password !== "") {
			$this->attributes['password'] = Hash::make($password);
		}
	}

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
		'is_active' => 'boolean',
		'birth_date' => 'date',
		'gender' => Gender::class,
	];

	public function scopeCheck($query)
	{
		return $query->select('is_active');
	}

	public function scopeActive($query, $param)
	{
		return $query->where('is_active', $param);
	}

	public function scopeUsernameLike($query, $param)
	{
		return $query->where(DB::Raw('LOWER(username)'), 'LIKE', '%' . strtolower($param) . '%');
	}

    /**
     * Get all of the transactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
