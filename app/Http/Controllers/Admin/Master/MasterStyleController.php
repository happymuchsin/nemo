<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MasterBuyer;
use App\Models\MasterCategory;
use App\Models\MasterFabric;
use App\Models\MasterSample;
use App\Models\MasterStyle;
use App\Models\MasterSubCategory;
use App\Models\Needle;
use App\Models\NeedleDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use stdClass;

class MasterStyleController extends Controller
{
    public function __construct()
    {
        $this->middleware('ajax-session-expired');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $page = 'admin_master_style';
        $title = 'ADMIN MASTER STYLE';

        HelperController::activityLog('OPEN ADMIN MASTER STYLE', 'master_styles', 'read', $request->ip(), $request->userAgent());

        $admin_master = 'menu-open';

        $buyer = MasterBuyer::get();
        $category = MasterCategory::get();
        $subcategory = MasterSubCategory::get();
        $sample = MasterSample::get();
        $fabric = MasterFabric::get();

        return view('Admin.Master.Style.index', compact('title', 'page', 'admin_master', 'buyer', 'category', 'subcategory', 'sample', 'fabric'));
    }

    public function data(Request $request)
    {
        $range_date = explode(' - ', $request->filter_range_date);
        $start = $range_date[0] ? $range_date[0] : Carbon::today()->subMonth();
        $end = $range_date[1] ? $range_date[1] : Carbon::today();
        $filter_master_buyer_id = $request->filter_master_buyer_id;
        $filter_master_category_id = $request->filter_master_category_id;
        $filter_master_sub_category_id = $request->filter_master_sub_category_id;
        $filter_master_sample_id = $request->filter_master_sample_id;
        $filter_master_fabric_id = $request->filter_master_fabric_id;
        $data = MasterStyle::with(['buyer', 'category', 'sub_category', 'sample', 'fabric'])
            ->where('start', '>=', $start)
            ->when($filter_master_buyer_id != 'all', function ($q) use ($filter_master_buyer_id) {
                $q->where('master_buyer_id', $filter_master_buyer_id);
            })
            ->when($filter_master_category_id != 'all', function ($q) use ($filter_master_category_id) {
                $q->where('master_category_id', $filter_master_category_id);
            })
            ->when($filter_master_sub_category_id != 'all', function ($q) use ($filter_master_sub_category_id) {
                $q->where('master_sub_category_id', $filter_master_sub_category_id);
            })
            ->when($filter_master_sample_id != 'all', function ($q) use ($filter_master_sample_id) {
                $q->where('master_sample_id', $filter_master_sample_id);
            })
            ->when($filter_master_fabric_id != 'all', function ($q) use ($filter_master_fabric_id) {
                $q->where('master_fabric_id', $filter_master_fabric_id);
            })
            ->get();
        return datatables()->of($data)
            ->addColumn('buyer', function ($q) {
                return $q->buyer->name;
            })
            ->addColumn('category', function ($q) {
                return $q->category->name;
            })
            ->addColumn('sub_category', function ($q) {
                return $q->sub_category->name;
            })
            ->addColumn('sample', function ($q) {
                return $q->sample->name;
            })
            ->addColumn('fabric', function ($q) {
                return $q->fabric->name;
            })
            ->addColumn('action', function ($q) {
                return view('includes.admin.action', [
                    'edit' => route('admin.master.style.edit', ['id' => $q->id]),
                    'hapus' => route('admin.master.style.hapus', ['id' => $q->id]),
                ]);
            })
            ->make(true);
    }

    public function template(Request $request)
    {
        HelperController::activityLog("DOWNLOAD TEMPLATE MASTER STYLE", null, 'download', $request->ip(), $request->userAgent());

        $sp = new Spreadsheet;
        $wsBuyer = $sp->getActiveSheet();
        $wsBuyer->setTitle('Master Buyer');
        $protect = $wsBuyer->getProtection();
        $protect->setPassword('qwe123');
        $protect->setAutoFilter(false);
        $protect->setSort(true);
        $protect->setSheet(true);

        $wsBuyer->getStyle('A1:B1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $wsBuyer->getStyle('A1:B1')->getFont()->setBold(true);
        $wsBuyer->getStyle('A1:B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $wsBuyer->getCell('A1')->setValue('No');
        $wsBuyer->getCell('B1')->setValue('Name');

        $k = 1;
        $i = 0;
        $data = MasterBuyer::get();
        foreach ($data as $d) {
            $k++;
            $i++;

            $wsBuyer->getStyle("A$k:B$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $wsBuyer->getCell("A$k")->setValue($i);
            $wsBuyer->getCell("B$k")->setValue($d->name);
        }

        foreach ($wsBuyer->getColumnIterator() as $column) {
            $wsBuyer->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $wsBuyer->setAutoFilter($wsBuyer->calculateWorksheetDimension());

        $wsSample = $sp->createSheet();
        $wsSample->setTitle('Master Sample');
        $protect = $wsSample->getProtection();
        $protect->setPassword('qwe123');
        $protect->setAutoFilter(false);
        $protect->setSort(true);
        $protect->setSheet(true);

        $wsSample->getStyle('A1:B1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $wsSample->getStyle('A1:B1')->getFont()->setBold(true);
        $wsSample->getStyle('A1:B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $wsSample->getCell('A1')->setValue('No');
        $wsSample->getCell('B1')->setValue('Name');

        $k = 1;
        $i = 0;
        $data = MasterSample::get();
        foreach ($data as $d) {
            $k++;
            $i++;

            $wsSample->getStyle("A$k:B$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $wsSample->getCell("A$k")->setValue($i);
            $wsSample->getCell("B$k")->setValue($d->name);
        }

        foreach ($wsSample->getColumnIterator() as $column) {
            $wsSample->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $wsSample->setAutoFilter($wsSample->calculateWorksheetDimension());

        $wsCategory = $sp->createSheet();
        $wsCategory->setTitle('Master Category');
        $protect = $wsCategory->getProtection();
        $protect->setPassword('qwe123');
        $protect->setAutoFilter(false);
        $protect->setSort(true);
        $protect->setSheet(true);

        $wsCategory->getStyle('A1:B1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $wsCategory->getStyle('A1:B1')->getFont()->setBold(true);
        $wsCategory->getStyle('A1:B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $wsCategory->getCell('A1')->setValue('No');
        $wsCategory->getCell('B1')->setValue('Name');

        $k = 1;
        $i = 0;
        $data = MasterCategory::get();
        foreach ($data as $d) {
            $k++;
            $i++;

            $wsCategory->getStyle("A$k:B$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $wsCategory->getCell("A$k")->setValue($i);
            $wsCategory->getCell("B$k")->setValue($d->name);
        }

        foreach ($wsCategory->getColumnIterator() as $column) {
            $wsCategory->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $wsCategory->setAutoFilter($wsCategory->calculateWorksheetDimension());

        $wsSubCategory = $sp->createSheet();
        $wsSubCategory->setTitle('Master Sub Category');
        $protect = $wsSubCategory->getProtection();
        $protect->setPassword('qwe123');
        $protect->setAutoFilter(false);
        $protect->setSort(true);
        $protect->setSheet(true);

        $wsSubCategory->getStyle('A1:B1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $wsSubCategory->getStyle('A1:B1')->getFont()->setBold(true);
        $wsSubCategory->getStyle('A1:B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $wsSubCategory->getCell('A1')->setValue('No');
        $wsSubCategory->getCell('B1')->setValue('Name');

        $k = 1;
        $i = 0;
        $data = MasterSubCategory::get();
        foreach ($data as $d) {
            $k++;
            $i++;

            $wsSubCategory->getStyle("A$k:B$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $wsSubCategory->getCell("A$k")->setValue($i);
            $wsSubCategory->getCell("B$k")->setValue($d->name);
        }

        foreach ($wsSubCategory->getColumnIterator() as $column) {
            $wsSubCategory->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $wsSubCategory->setAutoFilter($wsSubCategory->calculateWorksheetDimension());

        $wsFabric = $sp->createSheet();
        $wsFabric->setTitle('Master Fabric');
        $protect = $wsFabric->getProtection();
        $protect->setPassword('qwe123');
        $protect->setAutoFilter(false);
        $protect->setSort(true);
        $protect->setSheet(true);

        $wsFabric->getStyle('A1:B1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $wsFabric->getStyle('A1:B1')->getFont()->setBold(true);
        $wsFabric->getStyle('A1:B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $wsFabric->getCell('A1')->setValue('No');
        $wsFabric->getCell('B1')->setValue('Name');

        $k = 1;
        $i = 0;
        $data = MasterFabric::get();
        foreach ($data as $d) {
            $k++;
            $i++;

            $wsFabric->getStyle("A$k:B$k")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $wsFabric->getCell("A$k")->setValue($i);
            $wsFabric->getCell("B$k")->setValue($d->name);
        }

        foreach ($wsFabric->getColumnIterator() as $column) {
            $wsFabric->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $wsFabric->setAutoFilter($wsFabric->calculateWorksheetDimension());

        $wsInstruction = $sp->createSheet();
        $wsInstruction->setTitle('Instruction');
        $protect = $wsInstruction->getProtection();
        $protect->setPassword('qwe123');
        $protect->setSort(true);
        $protect->setSheet(true);

        $wsInstruction->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $wsInstruction->getStyle('A1:J1')->getFont()->setBold(true);
        $wsInstruction->getStyle('A1:J1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $wsInstruction->getCell('A1')->setValue('No');
        $wsInstruction->getCell('B1')->setValue('Buyer');
        $wsInstruction->getCell('C1')->setValue('Style');
        $wsInstruction->getCell('D1')->setValue('Sample Type');
        $wsInstruction->getCell('E1')->setValue('Category');
        $wsInstruction->getCell('F1')->setValue('Sub Category');
        $wsInstruction->getCell('G1')->setValue('Fabric');
        $wsInstruction->getCell('H1')->setValue('Season');
        $wsInstruction->getCell('I1')->setValue('Start');
        $wsInstruction->getCell('J1')->setValue('End');

        $wsInstruction->getCell('A2')->setValue('1, 2, 3, .. (REQUIRED)');
        $wsInstruction->getCell('B2')->setValue('You can copy from Master Buyer Sheet at column Name (REQUIRED)');
        $wsInstruction->getCell('C2')->setValue('Free Text (REQUIRED)');
        $wsInstruction->getCell('D2')->setValue('You can copy from Master Sample Sheet at column Name (REQUIRED)');
        $wsInstruction->getCell('E2')->setValue('You can copy from Master Category Sheet at column Name (REQUIRED)');
        $wsInstruction->getCell('F2')->setValue('You can copy from Master Sub Category Sheet at column Name (REQUIRED)');
        $wsInstruction->getCell('G2')->setValue('You can copy from Master Fabric Sheet at column Name (REQUIRED)');
        $wsInstruction->getCell('H2')->setValue('Free Text (REQUIRED)');
        $wsInstruction->getCell('I2')->setValue('Format Y-m-d (REQUIRED)');
        $wsInstruction->getCell('J2')->setValue('Format Y-m-d (REQUIRED)');

        foreach ($wsInstruction->getColumnIterator() as $column) {
            $wsInstruction->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $ws = $sp->createSheet();
        $ws->setTitle('Upload');
        $ws->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $ws->getStyle('A1:J1')->getFont()->setBold(true);
        $ws->getStyle('A1:J1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $ws->getCell('A1')->setValue('No');
        $ws->getCell('B1')->setValue('Buyer');
        $ws->getCell('C1')->setValue('Style');
        $ws->getCell('D1')->setValue('Sample Type');
        $ws->getCell('E1')->setValue('Category');
        $ws->getCell('F1')->setValue('Sub Category');
        $ws->getCell('G1')->setValue('Fabric');
        $ws->getCell('H1')->setValue('Season');
        $ws->getCell('I1')->setValue('Start');
        $ws->getCell('J1')->setValue('End');

        foreach ($ws->getColumnIterator() as $column) {
            $ws->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($sp);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Template Master Style.xlsx"');
        $writer->save('php://output');
        exit();
    }

    public function import(Request $request)
    {
        try {
            $now = Carbon::now();
            $path = $request->file('excel')->getRealPath();
            $sp = IOFactory::load($path);
            $sheet = $sp->setActiveSheetIndex(6);
            $rows = $sheet->toArray();
            $first = 1;
            DB::beginTransaction();
            $warning = [];
            foreach ($rows as $r) {
                if ($first > 1) {
                    if (
                        $r[1] != "" &&
                        $r[2] != "" &&
                        $r[3] != "" &&
                        $r[4] != "" &&
                        $r[5] != "" &&
                        $r[6] != "" &&
                        $r[7] != "" &&
                        $r[8] != "" &&
                        $r[9] != ""
                    ) {
                        $d = new stdClass;
                        $no = $r[0];
                        $buyer = strtoupper($r[1]);
                        $name = strtoupper($r[2]);
                        $sample = strtoupper($r[3]);
                        $category = strtoupper($r[4]);
                        $sub_category = strtoupper($r[5]);
                        $fabric = strtoupper($r[6]);
                        $season = strtoupper($r[7]);
                        $start = date('Y-m-d', strtotime($r[8]));
                        $end = date('Y-m-d', strtotime($r[9]));

                        $masterBuyer = MasterBuyer::where('name', $buyer)->first();
                        if (!$masterBuyer) {
                            $d->warning = "Master Buyer not found! at Number $no, Please refer to Instruction Sheet";
                            $warning[] = $d;
                            continue;
                        }

                        $masterSample = MasterSample::where('name', $sample)->first();
                        if (!$masterSample) {
                            $d->warning = "Master Sample not found! at Number $no, Please refer to Instruction Sheet";
                            $warning[] = $d;
                            continue;
                        }

                        $masterCategory = MasterCategory::where('name', $category)->first();
                        if (!$masterCategory) {
                            $d->warning = "Master Category not found! at Number $no, Please refer to Instruction Sheet";
                            $warning[] = $d;
                            continue;
                        }

                        $masterSubCategory = MasterSubCategory::where('name', $sub_category)->first();
                        if (!$masterSubCategory) {
                            $d->warning = "Master Sub Category not found! at Number $no, Please refer to Instruction Sheet";
                            $warning[] = $d;
                            continue;
                        }

                        $masterFabric = MasterFabric::where('name', $fabric)->first();
                        if (!$masterFabric) {
                            $d->warning = "Master Fabric not found! at Number $no, Please refer to Instruction Sheet";
                            $warning[] = $d;
                            continue;
                        }

                        $srf = self::number();

                        MasterStyle::create([
                            'master_buyer_id' => $masterBuyer->id,
                            'master_category_id' => $masterCategory->id,
                            'master_sub_category_id' => $masterSubCategory->id,
                            'master_sample_id' => $masterSample->id,
                            'master_fabric_id' => $masterFabric->id,
                            'srf' => $srf,
                            'season' => $season,
                            'name' => $name,
                            'start' => $start,
                            'end' => $end,
                            'created_by' => Auth::user()->username,
                            'created_at' => $now,
                        ]);
                        HelperController::activityLog("CREATE MASTER STYLE", 'master_styles', 'create', $request->ip(), $request->userAgent(), json_encode([
                            'master_buyer_id' => $masterBuyer->id,
                            'master_category_id' => $masterCategory->id,
                            'master_sub_category_id' => $masterSubCategory->id,
                            'master_sample_id' => $masterSample->id,
                            'master_fabric_id' => $masterFabric->id,
                            'srf' => $srf,
                            'season' => $season,
                            'name' => $name,
                            'start' => $start,
                            'end' => $end,
                            'created_by' => Auth::user()->username,
                            'created_at' => $now,
                        ]));
                    }
                }
                $first++;
            }
            if ($warning) {
                DB::rollBack();
                return response()->json($warning, 422);
            }
            DB::commit();
            return response()->json('Import Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Import Failed', 422);
        }
    }

    public function crup(Request $request)
    {
        $id = $request->id;
        $master_buyer_id = $request->master_buyer_id;
        $master_category_id = $request->master_category_id;
        $master_sub_category_id = $request->master_sub_category_id;
        $master_sample_id = $request->master_sample_id;
        $master_fabric_id = $request->master_fabric_id;
        // $srf = $request->srf;
        $srf = self::number();
        $season = strtoupper($request->season);
        $name = strtoupper($request->name);
        $range_date = explode(' - ', $request->range_date);
        if (!$range_date[0] && !$range_date[1]) {
            return response()->json('Please select Start - End', 422);
        }
        $start = $range_date[0];
        $end = $range_date[1];

        try {
            DB::beginTransaction();

            if ($id == 0) {
                MasterStyle::create([
                    'master_buyer_id' => $master_buyer_id,
                    'master_category_id' => $master_category_id,
                    'master_sub_category_id' => $master_sub_category_id,
                    'master_sample_id' => $master_sample_id,
                    'master_fabric_id' => $master_fabric_id,
                    'srf' => $srf,
                    'season' => $season,
                    'name' => $name,
                    'start' => $start,
                    'end' => $end,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now(),
                ]);
                HelperController::activityLog("CREATE MASTER STYLE", 'master_styles', 'create', $request->ip(), $request->userAgent(), json_encode([
                    'master_buyer_id' => $master_buyer_id,
                    'master_category_id' => $master_category_id,
                    'master_sub_category_id' => $master_sub_category_id,
                    'master_sample_id' => $master_sample_id,
                    'master_fabric_id' => $master_fabric_id,
                    'srf' => $srf,
                    'season' => $season,
                    'name' => $name,
                    'start' => $start,
                    'end' => $end,
                    'created_by' => Auth::user()->username,
                    'created_at' => Carbon::now(),
                ]));
            } else {
                MasterStyle::where('id', $id)->update([
                    'master_buyer_id' => $master_buyer_id,
                    'master_sub_category_id' => $master_sub_category_id,
                    'master_sample_id' => $master_sample_id,
                    'master_fabric_id' => $master_fabric_id,
                    'srf' => $srf,
                    'season' => $season,
                    'name' => $name,
                    'start' => $start,
                    'end' => $end,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now(),
                ]);
                HelperController::activityLog("UPDATE MASTER STYLE", 'master_styles', 'update', $request->ip(), $request->userAgent(), json_encode([
                    'id' => $id,
                    'master_buyer_id' => $master_buyer_id,
                    'master_sub_category_id' => $master_sub_category_id,
                    'master_sample_id' => $master_sample_id,
                    'master_fabric_id' => $master_fabric_id,
                    'srf' => $srf,
                    'season' => $season,
                    'name' => $name,
                    'start' => $start,
                    'end' => $end,
                    'updated_by' => Auth::user()->username,
                    'updated_at' => Carbon::now(),
                ]), $id);
            }

            DB::commit();
            return response()->json('Save Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Save Failed', 422);
        }
    }

    public function edit($id)
    {
        $s = MasterStyle::where('id', $id)->first();
        $d = new stdClass;
        $d->id = $s->id;
        $d->master_buyer_id = $s->master_buyer_id;
        $d->master_category_id = $s->master_category_id;
        $d->master_sub_category_id = $s->master_sub_category_id;
        $d->master_sample_id = $s->master_sample_id;
        $d->master_fabric_id = $s->master_fabric_id;
        $d->srf = $s->srf;
        $d->season = $s->season;
        $d->name = $s->name;
        $d->range_date = $s->start . ' - ' . $s->end;
        return response()->json($d, 200);
    }

    public function hapus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $n = Needle::where('master_style_id', $id)->get();
            foreach ($n as $n) {
                NeedleDetail::where('needle_id', $n->id)->update([
                    'deleted_by' => Auth::user()->username,
                    'deleted_at' => Carbon::now(),
                ]);
            }
            Needle::where('master_style_id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            MasterStyle::where('id', $id)->update([
                'deleted_by' => Auth::user()->username,
                'deleted_at' => Carbon::now(),
            ]);
            HelperController::activityLog("DELETE MASTER STYLE", 'master_styles', 'delete', $request->ip(), $request->userAgent(), null, $id);
            DB::commit();
            return response()->json('Delete Successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json('Delete Failed', 422);
        }
    }

    static function number()
    {
        $Y = date('Y');
        $y = date('y');
        $m = date('m');
        $M = strtoupper(date('M'));
        $s = MasterStyle::whereYear('created_at', $Y)->whereMonth('created_at', $m)->orderBy('srf', 'desc')->first();
        if ($s) {
            $explode = explode($M . $y, $s->srf);
            $no = intval($explode[0]);
            $next = $no + 1;
            if (strlen($next) == 1) {
                $isi = '00' . $next;
            } else if (strlen($next) == 2) {
                $isi = '0' . $next;
            } else {
                $isi = $next;
            }
            $code = $isi . $M . $y;
        } else {
            $code = '001' . $M . $y;
        }
        return $code;
    }
}
