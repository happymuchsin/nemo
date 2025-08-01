<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMorningStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function master_needle()
    {
        return $this->belongsTo(MasterNeedle::class, 'master_needle_id', 'id');
    }
}
