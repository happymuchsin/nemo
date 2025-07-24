<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Resources\ApiResource;
use App\Models\ApprovalMissingFragment;
use Exception;
use Illuminate\Http\Request;
use stdClass;

class ApprovalController extends Controller
{
    public function data(Request $request)
    {
        $username = $request->username;
        $area_id = $request->area_id;
        $lokasi_id = $request->lokasi_id;

        HelperController::activityLog('ANDROID OPEN APPROVAL', 'approvals', 'read', $request->ip(), $request->userAgent(), null, null, $username);

        try {
            $data = [];
            $s = ApprovalMissingFragment::with(['user', 'needle' => function ($q) {
                $q->with(['box', 'needle']);
            }, 'approval' => function ($q) {
                $q->with(['user']);
            }, 'master_line', 'master_style' => function ($q) {
                $q->with(['buyer']);
            }])
                ->where('master_area_id', $area_id)
                ->where('master_counter_id', $lokasi_id)
                ->whereNotIn('status', ['DONE', 'REJECT'])
                ->get();
            foreach ($s as $s) {
                $d = new stdClass;
                $d->id = $s->id;
                $d->username = $s->user->username;
                $d->name = $s->user->name;
                $d->status = $s->status;
                $d->line = $s->master_line->name;
                $d->requestDate = date('Y-m-d', strtotime($s->created_at));
                $d->requestTime = date('H:i:s', strtotime($s->created_at));
                $d->approvalName = $s->approval->user->name;
                $d->approvalUsername = $s->approval->user->username;
                $data[] = $d;
            }

            return new ApiResource(200, 'success', $data);
        } catch (Exception $e) {
            return new ApiResource(422, $e->getMessage(), '');
        }
    }
}
