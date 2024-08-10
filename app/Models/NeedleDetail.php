<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NeedleDetail extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    public function needle()
    {
        return $this->belongsTo(Needle::class, 'needle_id', 'id');
    }

    public function status()
    {
        return $this->belongsTo(MasterStatus::class, 'master_status_id', 'id');
    }
}
