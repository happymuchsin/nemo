<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalMissingFragment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function needle()
    {
        return $this->belongsTo(Needle::class, 'needle_id', 'id');
    }

    public function master_needle()
    {
        return $this->belongsTo(MasterNeedle::class, 'master_needle_id', 'id');
    }

    public function master_line()
    {
        return $this->belongsTo(MasterLine::class, 'master_line_id', 'id');
    }

    public function master_style()
    {
        return $this->belongsTo(MasterStyle::class, 'master_style_id', 'id');
    }

    public function approval()
    {
        return $this->belongsTo(MasterApproval::class, 'master_approval_id', 'id');
    }
}
