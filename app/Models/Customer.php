<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public $incrementing = false;

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}
