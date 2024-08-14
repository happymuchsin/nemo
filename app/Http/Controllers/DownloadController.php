<?php

namespace App\Http\Controllers;

use App\Models\ListApp;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function download($apk)
    {
        $la = ListApp::where('app', 'like', '%' . $apk . '%')->orderByRaw("CAST(SUBSTRING_INDEX(version, '.', 1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', -2), '.', 1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC")->first();
        if ($la) {
            $filename = $apk . $la->version;
            return response()->download(storage_path("app/apk/$apk/$filename.apk"), "$filename.apk", [
                "Content-Type" => "application/vnd.android.package-archive",
            ]);
        }
    }
}
