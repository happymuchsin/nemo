<?php

namespace App\Console\Commands;

use App\Models\Approval;
use App\Models\ApprovalMissingFragment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MovingApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MovingApproval';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $approval = Approval::withTrashed()->where('status', '!=', 'migrated')->get();
        foreach ($approval as $a) {
            ApprovalMissingFragment::insert([
                'id' => $a->id,
                'tanggal' => $a->tanggal,
                'user_id' => $a->user_id,
                'master_line_id' => $a->master_line_id,
                'master_style_id' => $a->master_style_id,
                'master_needle_id' => $a->master_needle_id,
                'master_approval_id' => $a->master_approval_id,
                'master_area_id' => $a->master_area_id,
                'master_counter_id' => $a->master_counter_id,
                'needle_id' => $a->needle_id,
                'needle_status' => $a->needle_status,
                'tipe' => $a->tipe,
                'status' => $a->status,
                'remark' => $a->remark,
                'approve' => $a->approve,
                'reject' => $a->reject,
                'filename' => $a->filename,
                'ext' => $a->ext,
                'created_by' => $a->created_by,
                'updated_by' => $a->updated_by,
                'deleted_by' => $a->deleted_by,
                'created_at' => $a->created_at,
                'updated_at' => $a->updated_at,
                'deleted_at' => $a->deleted_at,
            ]);
            $a->remark = 'Migrated to approval_missing_fragments';
            $a->status = 'migrated';
            $a->deleted_by = 'developer';
            $a->deleted_at = $now;
            $a->save();
        }
    }
}
