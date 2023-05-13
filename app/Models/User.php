<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes;

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    protected $with = [
        'user_levels',
        'user_data',
    ];
    
    public function user_data()
    {
        return $this->belongsTo(UserDatum::class, 'user_datum_id')->withTrashed();
    }
    
    public function user_levels()
    {
        return $this->belongsTo(UserLevel::class, 'user_level_id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
    
    public function transaction_items(){
        return $this->hasMany(TransactionItem::class);
    }
}
