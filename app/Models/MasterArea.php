<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterArea extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function line()
    {
        return $this->hasMany(MasterLine::class, 'id', 'master_area_id');
    }

    public function counter()
    {
        return $this->hasMany(MasterCounter::class, 'id', 'master_area_id');
    }
}
