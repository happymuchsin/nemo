<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    public function area()
    {
        return $this->belongsTo(MasterArea::class, 'master_area_id');
    }

    public function needle()
    {
        return $this->belongsTo(MasterNeedle::class, 'master_needle_id');
    }
}
