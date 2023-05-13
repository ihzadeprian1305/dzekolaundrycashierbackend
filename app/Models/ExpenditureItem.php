<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenditureItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    protected $with = [
        'stuffs'
    ];

    public $incrementing = false;

    public function expenditures(){
        return $this->belongsTo(Expenditure::class)->withTrashed();
    }
    
    public function stuffs(){
        return $this->belongsTo(Stuff::class, 'stuff_id')->withTrashed();
    }
    
    public function users(){
        return $this->belongsTo(User::class)->withTrashed();
    }
}
