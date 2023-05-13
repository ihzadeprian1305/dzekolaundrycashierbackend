<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    protected $with = [
        'packages'
    ];

    public $incrementing = false;

    public function transactions(){
        return $this->belongsTo(Transaction::class)->withTrashed();
    }
    
    public function packages(){
        return $this->belongsTo(Package::class, 'package_id')->withTrashed();
    }
    
    public function users(){
        return $this->belongsTo(User::class)->withTrashed();
    }
}
