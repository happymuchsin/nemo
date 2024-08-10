<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterStyle extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function buyer()
    {
        return $this->belongsTo(MasterBuyer::class, 'master_buyer_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(MasterCategory::class, 'master_category_id', 'id');
    }
    public function sample()
    {
        return $this->belongsTo(MasterSample::class, 'master_sample_id', 'id');
    }
    public function fabric()
    {
        return $this->belongsTo(MasterFabric::class, 'master_fabric_id', 'id');
    }
}
