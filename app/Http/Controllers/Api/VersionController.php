<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Models\ListApp;
use Exception;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function version(Request $request)
    {
        try {
            $app = $request->app;
            $listApp = ListApp::where('app', $app)->orderByRaw("CAST(SUBSTRING_INDEX(version, '.', 1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', -2), '.', 1) AS UNSIGNED) DESC,
                CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC")->first();
            return new ApiResource(200, 'success', $listApp);
        } catch (Exception $e) {
            return new ApiResource(422, 'Check Update Failed', []);
        }
    }

    public function update(Request $request)
    {
        $app = $request->app;
        $version = $request->version;
        $filename = $app . $version;

        return response()->file(storage_path("app/apk/$app/$filename.apk"), [
            "Content-Type" => "application/vnd.android.package-archive",
            "Content-Disposition" => "attachment; filename='$filename.apk'",
        ]);
    }
}
