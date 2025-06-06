<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPlacement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    public function line()
    {
        return $this->belongsTo(MasterLine::class, 'location_id', 'id');
    }

    public function counter()
    {
        return $this->belongsTo(MasterCounter::class, 'counter_id', 'id');
    }
}
