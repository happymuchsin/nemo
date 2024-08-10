<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCounter extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function area()
    {
        return $this->belongsTo(MasterArea::class, 'master_area_id', 'id');
    }

    public function box()
    {
        return $this->hasMany(MasterBox::class, 'id', 'master_counter_id');
    }
}
