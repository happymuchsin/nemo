<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterBox extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function counter()
    {
        return $this->belongsTo(MasterCounter::class, 'master_counter_id', 'id');
    }
}
