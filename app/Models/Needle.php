<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Needle extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    public function line()
    {
        return $this->belongsTo(MasterLine::class, 'master_line_id', 'id');
    }
    public function style()
    {
        return $this->belongsTo(MasterStyle::class, 'master_style_id', 'id');
    }
    public function box()
    {
        return $this->belongsTo(MasterBox::class, 'master_box_id', 'id');
    }
    public function needle()
    {
        return $this->belongsTo(MasterNeedle::class, 'master_needle_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
