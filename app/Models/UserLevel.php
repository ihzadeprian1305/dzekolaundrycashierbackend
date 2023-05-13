<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLevel extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
