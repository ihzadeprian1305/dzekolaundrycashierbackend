<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'id' => 'string'
    ];
    
    protected $with = [
        'transaction_items',
        'transaction_items.packages'
    ];

    public $incrementing = false;

    public function created_by(){
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
    
    public function cb(){
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function updated_by(){
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public function customers(){
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
    }

    public function transaction_items(){
        return $this->hasMany(TransactionItem::class);
    } 
}
