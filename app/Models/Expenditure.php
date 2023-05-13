<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expenditure extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'id' => 'string'
    ];
    
    protected $with = [
        'expenditure_items',
        'expenditure_items.stuffs'
    ];

    public $incrementing = false;

    public function created_by(){
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updated_by(){
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public function expenditure_items(){
        return $this->hasMany(ExpenditureItem::class);
    } 
}
