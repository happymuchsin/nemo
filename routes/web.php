<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\Master\MasterApprovalController;
use App\Http\Controllers\Admin\Master\MasterAreaController;
use App\Http\Controllers\Admin\Master\MasterBoxController;
use App\Http\Controllers\Admin\Master\MasterBoxNeedleController;
use App\Http\Controllers\Admin\Master\MasterBuyerController;
use App\Http\Controllers\Admin\Master\MasterCategoryController;
use App\Http\Controllers\Admin\Master\MasterCounterController;
use App\Http\Controllers\Admin\Master\MasterDivisionController;
use App\Http\Controllers\Admin\Master\MasterFabricController;
use App\Http\Controllers\Admin\Master\MasterHolidayController;
use App\Http\Controllers\Admin\Master\MasterLineController;
use App\Http\Controllers\Admin\Master\MasterMonthlyStockController;
use App\Http\Controllers\Admin\Master\MasterMorningStockController;
use App\Http\Controllers\Admin\Master\MasterNeedleController;
use App\Http\Controllers\Admin\Master\MasterPlacementController;
use App\Http\Controllers\Admin\Master\MasterPositionController;
use App\Http\Controllers\Admin\Master\MasterSampleController;
use App\Http\Controllers\Admin\Master\MasterStatusController;
use App\Http\Controllers\Admin\Master\MasterStyleController;
use App\Http\Controllers\Admin\Master\MasterSubCategoryController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\Tools\ToolsActivityLogController;
use App\Http\Controllers\Admin\Tools\ToolsNeedleControlController;
use App\Http\Controllers\Admin\Tools\ToolsPermissionController;
use App\Http\Controllers\Admin\Tools\ToolsRoleController;
use App\Http\Controllers\Admin\Tools\ToolsUserController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\NotifController;
use App\Http\Controllers\User\AdjustmentController;
use App\Http\Controllers\User\Approval\ApprovalAdjustmentController;
use App\Http\Controllers\User\Approval\ApprovalMissingFragmentController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\DeadStockController;
use App\Http\Controllers\User\NeedleReportController;
use App\Http\Controllers\User\Report\DailyBrokenNeedleController;
use App\Http\Controllers\User\Report\DailyStockController;
use App\Http\Controllers\User\Report\HighUserController;
use App\Http\Controllers\User\Report\IntervalUserController;
use App\Http\Controllers\User\Report\SummaryStockController;
use App\Http\Controllers\User\Report\SummaryWipController;
use App\Http\Controllers\User\Report\TimingLogController;
use App\Http\Controllers\User\Report\TrackByNeedleController;
use App\Http\Controllers\User\Report\TrackByOperatorController;
use App\Http\Controllers\User\Report\UsageNeedleController;
use App\Http\Controllers\User\Report\WipNeedleController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\StockController;
use App\Http\Controllers\User\WarehouseController;
use App\Mail\DailyAlertStock;
use App\Models\MasterNeedle;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/download/{apk?}', [DownloadController::class, 'download'])->name('download');


Auth::routes();
Route::group(['middleware' => ['auth']], function () {
    // Route::get('/mailable', function () {
    //     $data  = [];

    //     $master_needle = MasterNeedle::orderBy('tipe')->orderBy('size')->get();
    //     foreach ($master_needle as $m) {
    //         $d = new stdClass;
    //         $d->brand = $m->brand;
    //         $d->tipe = $m->tipe;
    //         $d->size = $m->size;
    //         $d->code = $m->code;
    //         $d->min = 5;

    //         $in = Warehouse::where('master_needle_id', $m->id)->sum('in');
    //         $out = Warehouse::where('master_needle_id', $m->id)->sum('out');

    //         if ($in - $out <= $d->min) {
    //             $d->stock = $in - $out;
    //         } else {
    //             continue;
    //         }

    //         $data[] = $d;
    //     }

    //     if (count($data) > 0) {
    //         return new DailyAlertStock($data);
    //         // Mail::to('happymuchsin@gmail.com')->send(new DailyAlertStock($data));
    //     }
    // });

    Route::get('/logout', function () {
        Auth::logout();

        return redirect('/');
    });

    Route::get('/notif', [NotifController::class, 'notif'])->name('notif');
    Route::get('/notif-clicked/{tipe}', [NotifController::class, 'notif_clicked'])->name('notif-clicked');

    Route::prefix('/user')
        ->group(function () {
            Route::prefix('/dashboard')
                ->middleware(['permission:user-dashboard'])
                ->group(function () {
                    Route::get('', [UserDashboard::class, 'index'])->name('user.dashboard');
                    Route::post('data', [UserDashboard::class, 'data'])->name('user.dashboard.data');
                });

            Route::prefix('/report')
                ->middleware(['permission:user-report'])
                ->group(function () {
                    Route::get('', [ReportController::class, 'index'])->name('user.report');
                    Route::get('data', [ReportController::class, 'data'])->name('user.report.data');

                    Route::prefix('/summary-stock')
                        ->group(function () {
                            Route::get('', [SummaryStockController::class, 'index'])->name('user.report.summary-stock');
                            Route::get('data', [SummaryStockController::class, 'data'])->name('user.report.summary-stock.data');
                        });

                    Route::prefix('/usage-needle')
                        ->group(function () {
                            Route::get('', [UsageNeedleController::class, 'index'])->name('user.report.usage-needle');
                            Route::get('data', [UsageNeedleController::class, 'data'])->name('user.report.usage-needle.data');
                            Route::post('unduh', [UsageNeedleController::class, 'unduh'])->name('user.report.usage-needle.unduh');
                        });

                    Route::prefix('/daily-stock')
                        ->group(function () {
                            Route::get('', [DailyStockController::class, 'index'])->name('user.report.daily-stock');
                            Route::post('data', [DailyStockController::class, 'data'])->name('user.report.daily-stock.data');
                            Route::post('unduh', [DailyStockController::class, 'unduh'])->name('user.report.daily-stock.unduh');
                        });

                    Route::prefix('/timing-log')
                        ->group(function () {
                            Route::get('', [TimingLogController::class, 'index'])->name('user.report.timing-log');
                            Route::get('data', [TimingLogController::class, 'data'])->name('user.report.timing-log.data');
                            Route::post('unduh', [TimingLogController::class, 'unduh'])->name('user.report.timing-log.unduh');
                        });

                    Route::prefix('/track-by-operator')
                        ->group(function () {
                            Route::get('', [TrackByOperatorController::class, 'index'])->name('user.report.track-by-operator');
                            Route::get('data', [TrackByOperatorController::class, 'data'])->name('user.report.track-by-operator.data');
                        });

                    Route::prefix('/track-by-needle')
                        ->group(function () {
                            Route::get('', [TrackByNeedleController::class, 'index'])->name('user.report.track-by-needle');
                            Route::get('data', [TrackByNeedleController::class, 'data'])->name('user.report.track-by-needle.data');
                        });

                    Route::prefix('/wip-needle')
                        ->group(function () {
                            Route::get('', [WipNeedleController::class, 'index'])->name('user.report.wip-needle');
                            Route::get('data', [WipNeedleController::class, 'data'])->name('user.report.wip-needle.data');
                            Route::post('unduh', [WipNeedleController::class, 'unduh'])->name('user.report.wip-needle.unduh');
                        });

                    Route::prefix('/summary-wip')
                        ->group(function () {
                            Route::get('', [SummaryWipController::class, 'index'])->name('user.report.summary-wip');
                            Route::get('data', [SummaryWipController::class, 'data'])->name('user.report.summary-wip.data');
                        });

                    Route::prefix('/high-user')
                        ->group(function () {
                            Route::get('', [HighUserController::class, 'index'])->name('user.report.high-user');
                            Route::get('data', [HighUserController::class, 'data'])->name('user.report.high-user.data');
                            Route::post('unduh', [HighUserController::class, 'unduh'])->name('user.report.high-user.unduh');
                        });
                    Route::prefix('/interval-user')
                        ->group(function () {
                            Route::get('', [IntervalUserController::class, 'index'])->name('user.report.interval-user');
                            Route::post('data', [IntervalUserController::class, 'data'])->name('user.report.interval-user.data');
                            Route::post('unduh', [IntervalUserController::class, 'unduh'])->name('user.report.interval-user.unduh');
                        });
                    Route::prefix('/daily-broken-needle')
                        ->group(function () {
                            Route::get('', [DailyBrokenNeedleController::class, 'index'])->name('user.report.daily-broken-needle');
                            Route::get('data', [DailyBrokenNeedleController::class, 'data'])->name('user.report.daily-broken-needle.data');
                            Route::post('unduh', [DailyBrokenNeedleController::class, 'unduh'])->name('user.report.daily-broken-needle.unduh');
                        });
                });

            Route::prefix('/needle-report')
                ->middleware(['permission:user-needle-report'])
                ->group(function () {
                    Route::get('', [NeedleReportController::class, 'index'])->name('user.needle-report');
                    Route::get('data', [NeedleReportController::class, 'data'])->name('user.needle-report.data');
                });

            Route::prefix('/stock')
                ->middleware(['permission:user-stock'])
                ->group(function () {
                    Route::get('', [StockController::class, 'index'])->name('user.stock');
                    Route::get('data', [StockController::class, 'data'])->name('user.stock.data');
                    Route::post('spinner', [StockController::class, 'spinner'])->name('user.stock.spinner');
                    Route::post('needle', [StockController::class, 'needle'])->name('user.stock.needle');
                    Route::post('store', [StockController::class, 'store'])->name('user.stock.store');
                    Route::get('edit/{id?}', [StockController::class, 'edit'])->name('user.stock.edit');
                    Route::get('add/{id?}', [StockController::class, 'add'])->name('user.stock.add');
                    Route::get('history', [StockController::class, 'history'])->name('user.stock.history');
                    Route::post('update', [StockController::class, 'update'])->name('user.stock.update');
                    Route::get('hapus/{id?}', [StockController::class, 'hapus'])->name('user.stock.hapus');
                    Route::get('clear/{id?}', [StockController::class, 'clear'])->name('user.stock.clear');
                });

            Route::prefix('/warehouse')
                ->middleware(['permission:user-warehouse'])
                ->group(function () {
                    Route::get('', [WarehouseController::class, 'index'])->name('user.warehouse');
                    Route::get('data', [WarehouseController::class, 'data'])->name('user.warehouse.data');
                    Route::post('spinner', [WarehouseController::class, 'spinner'])->name('user.warehouse.spinner');
                    Route::post('needle', [WarehouseController::class, 'needle'])->name('user.warehouse.needle');
                    Route::post('store', [WarehouseController::class, 'store'])->name('user.warehouse.store');
                    Route::get('get/{id?}', [WarehouseController::class, 'get'])->name('user.warehouse.get');
                    Route::get('history', [WarehouseController::class, 'history'])->name('user.warehouse.history');
                    Route::post('update', [WarehouseController::class, 'update'])->name('user.warehouse.update');
                    Route::get('hapus/{id?}', [WarehouseController::class, 'hapus'])->name('user.warehouse.hapus');
                    Route::post('transfer', [WarehouseController::class, 'transfer'])->name('user.warehouse.transfer');
                });

            Route::prefix('/dead-stock')
                ->middleware(['permission:user-dead-stock'])
                ->group(function () {
                    Route::get('', [DeadStockController::class, 'index'])->name('user.dead-stock');
                    Route::get('data', [DeadStockController::class, 'data'])->name('user.dead-stock.data');
                    Route::get('get/{id?}', [DeadStockController::class, 'get'])->name('user.dead-stock.get');
                    Route::get('history', [DeadStockController::class, 'history'])->name('user.dead-stock.history');
                    Route::post('transfer', [DeadStockController::class, 'transfer'])->name('user.dead-stock.transfer');
                });

            Route::prefix('/adjustment')
                ->middleware(['permission:user-adjustment'])
                ->group(function () {
                    Route::get('', [AdjustmentController::class, 'index'])->name('user.adjustment');
                    Route::get('data', [AdjustmentController::class, 'data'])->name('user.adjustment.data');
                    Route::post('item', [AdjustmentController::class, 'item'])->name('user.adjustment.item');
                    Route::post('crup', [AdjustmentController::class, 'crup'])->name('user.adjustment.crup');
                    Route::get('get/{id?}', [AdjustmentController::class, 'get'])->name('user.adjustment.get');
                    Route::get('hapus/{id?}', [AdjustmentController::class, 'hapus'])->name('user.adjustment.hapus');
                    Route::get('recalculate/{id?}', [AdjustmentController::class, 'recalculate'])->name('user.adjustment.recalculate');
                    Route::post('unduh', [AdjustmentController::class, 'unduh'])->name('user.adjustment.unduh');
                });

            Route::prefix('/approval')
                ->middleware(['permission:user-approval'])
                ->group(function () {
                    Route::prefix('/missing-fragment')
                        ->group(function () {
                            Route::get('', [ApprovalMissingFragmentController::class, 'index'])->name('user.approval.missing-fragment');
                            Route::get('data', [ApprovalMissingFragmentController::class, 'data'])->name('user.approval.missing-fragment.data');
                            Route::get('approval/{id}/{status}', [ApprovalMissingFragmentController::class, 'approval'])->name('user.approval.missing-fragment.approval');
                        });
                    Route::prefix('/adjustment')
                        ->group(function () {
                            Route::get('', [ApprovalAdjustmentController::class, 'index'])->name('user.approval.adjustment');
                            Route::get('data', [ApprovalAdjustmentController::class, 'data'])->name('user.approval.adjustment.data');
                            Route::get('get/{id}', [ApprovalAdjustmentController::class, 'get'])->name('user.approval.adjustment.get');
                            Route::post('approval', [ApprovalAdjustmentController::class, 'approval'])->name('user.approval.adjustment.approval');
                        });
                });
        });

    Route::prefix('/admin')
        ->group(function () {
            Route::prefix('/dashboard')
                ->middleware(['permission:admin-dashboard'])
                ->group(function () {
                    Route::get('', [AdminDashboard::class, 'index'])->name('admin.dashboard');
                });

            Route::prefix('/master')
                ->middleware(['permission:admin-master'])
                ->group(function () {
                    Route::prefix('/holiday')
                        ->middleware(['permission:admin-master-holiday'])
                        ->group(function () {
                            Route::get('', [MasterHolidayController::class, 'index'])->name('admin.master.holiday');
                            Route::post('calendar', [MasterHolidayController::class, 'calendar'])->name('admin.master.holiday.calendar');
                            Route::post('crup', [MasterHolidayController::class, 'crup'])->name('admin.master.holiday.crup');
                        });

                    Route::prefix('/division')
                        ->middleware(['permission:admin-master-division'])
                        ->group(function () {
                            Route::get('', [MasterDivisionController::class, 'index'])->name('admin.master.division');
                            Route::get('data', [MasterDivisionController::class, 'data'])->name('admin.master.division.data');
                            Route::post('crup', [MasterDivisionController::class, 'crup'])->name('admin.master.division.crup');
                            Route::get('edit/{id?}', [MasterDivisionController::class, 'edit'])->name('admin.master.division.edit');
                            Route::get('hapus/{id?}', [MasterDivisionController::class, 'hapus'])->name('admin.master.division.hapus');
                        });

                    Route::prefix('/position')
                        ->middleware(['permission:admin-master-position'])
                        ->group(function () {
                            Route::get('', [MasterPositionController::class, 'index'])->name('admin.master.position');
                            Route::get('data', [MasterPositionController::class, 'data'])->name('admin.master.position.data');
                            Route::post('crup', [MasterPositionController::class, 'crup'])->name('admin.master.position.crup');
                            Route::get('edit/{id?}', [MasterPositionController::class, 'edit'])->name('admin.master.position.edit');
                            Route::get('hapus/{id?}', [MasterPositionController::class, 'hapus'])->name('admin.master.position.hapus');
                        });

                    Route::prefix('/approval')
                        ->middleware(['permission:admin-master-approval'])
                        ->group(function () {
                            Route::get('', [MasterApprovalController::class, 'index'])->name('admin.master.approval');
                            Route::get('data', [MasterApprovalController::class, 'data'])->name('admin.master.approval.data');
                            Route::post('crup', [MasterApprovalController::class, 'crup'])->name('admin.master.approval.crup');
                            Route::get('hapus/{id?}', [MasterApprovalController::class, 'hapus'])->name('admin.master.approval.hapus');
                        });

                    Route::prefix('/area')
                        ->middleware(['permission:admin-master-area'])
                        ->group(function () {
                            Route::get('', [MasterAreaController::class, 'index'])->name('admin.master.area');
                            Route::get('data', [MasterAreaController::class, 'data'])->name('admin.master.area.data');
                            Route::post('crup', [MasterAreaController::class, 'crup'])->name('admin.master.area.crup');
                            Route::get('edit/{id?}', [MasterAreaController::class, 'edit'])->name('admin.master.area.edit');
                            Route::get('hapus/{id?}', [MasterAreaController::class, 'hapus'])->name('admin.master.area.hapus');
                        });

                    Route::prefix('/line')
                        ->middleware(['permission:admin-master-line'])
                        ->group(function () {
                            Route::get('', [MasterLineController::class, 'index'])->name('admin.master.line');
                            Route::get('data', [MasterLineController::class, 'data'])->name('admin.master.line.data');
                            Route::post('crup', [MasterLineController::class, 'crup'])->name('admin.master.line.crup');
                            Route::get('edit/{id?}', [MasterLineController::class, 'edit'])->name('admin.master.line.edit');
                            Route::get('hapus/{id?}', [MasterLineController::class, 'hapus'])->name('admin.master.line.hapus');
                        });

                    Route::prefix('/counter')
                        ->middleware(['permission:admin-master-counter'])
                        ->group(function () {
                            Route::get('', [MasterCounterController::class, 'index'])->name('admin.master.counter');
                            Route::get('data', [MasterCounterController::class, 'data'])->name('admin.master.counter.data');
                            Route::post('crup', [MasterCounterController::class, 'crup'])->name('admin.master.counter.crup');
                            Route::get('edit/{id?}', [MasterCounterController::class, 'edit'])->name('admin.master.counter.edit');
                            Route::get('hapus/{id?}', [MasterCounterController::class, 'hapus'])->name('admin.master.counter.hapus');
                        });

                    Route::prefix('/box')
                        ->middleware(['permission:admin-master-box'])
                        ->group(function () {
                            Route::get('', [MasterBoxController::class, 'index'])->name('admin.master.box');
                            Route::get('data', [MasterBoxController::class, 'data'])->name('admin.master.box.data');
                            Route::post('crup', [MasterBoxController::class, 'crup'])->name('admin.master.box.crup');
                            Route::get('edit/{id?}', [MasterBoxController::class, 'edit'])->name('admin.master.box.edit');
                            Route::get('hapus/{id?}', [MasterBoxController::class, 'hapus'])->name('admin.master.box.hapus');
                        });

                    Route::prefix('/box-needle')
                        ->middleware(['permission:admin-master-box'])
                        ->group(function () {
                            Route::get('', [MasterBoxNeedleController::class, 'index'])->name('admin.master.box-needle');
                            Route::get('data', [MasterBoxNeedleController::class, 'data'])->name('admin.master.box-needle.data');
                            Route::post('spinner', [MasterBoxNeedleController::class, 'spinner'])->name('admin.master.box-needle.spinner');
                            Route::post('needle', [MasterBoxNeedleController::class, 'needle'])->name('admin.master.box-needle.needle');
                            Route::post('store', [MasterBoxNeedleController::class, 'store'])->name('admin.master.box-needle.store');
                            Route::get('hapus/{id?}', [MasterBoxNeedleController::class, 'hapus'])->name('admin.master.box-needle.hapus');
                        });

                    Route::prefix('/placement')
                        ->middleware(['permission:admin-master-placement'])
                        ->group(function () {
                            Route::get('', [MasterPlacementController::class, 'index'])->name('admin.master.placement');
                            Route::get('data', [MasterPlacementController::class, 'data'])->name('admin.master.placement.data');
                            Route::post('spinner', [MasterPlacementController::class, 'spinner'])->name('admin.master.placement.spinner');
                            Route::post('crup', [MasterPlacementController::class, 'crup'])->name('admin.master.placement.crup');
                            Route::get('edit/{id?}', [MasterPlacementController::class, 'edit'])->name('admin.master.placement.edit');
                            Route::get('hapus/{id?}', [MasterPlacementController::class, 'hapus'])->name('admin.master.placement.hapus');
                        });

                    Route::prefix('/status')
                        ->middleware(['permission:admin-master-status'])
                        ->group(function () {
                            Route::get('', [MasterStatusController::class, 'index'])->name('admin.master.status');
                            Route::get('data', [MasterStatusController::class, 'data'])->name('admin.master.status.data');
                            Route::post('crup', [MasterStatusController::class, 'crup'])->name('admin.master.status.crup');
                            Route::get('edit/{id?}', [MasterStatusController::class, 'edit'])->name('admin.master.status.edit');
                            Route::get('hapus/{id?}', [MasterStatusController::class, 'hapus'])->name('admin.master.status.hapus');
                        });

                    Route::prefix('/needle')
                        ->middleware(['permission:admin-master-needle'])
                        ->group(function () {
                            Route::get('', [MasterNeedleController::class, 'index'])->name('admin.master.needle');
                            Route::get('data', [MasterNeedleController::class, 'data'])->name('admin.master.needle.data');
                            Route::post('crup', [MasterNeedleController::class, 'crup'])->name('admin.master.needle.crup');
                            Route::get('edit/{id?}', [MasterNeedleController::class, 'edit'])->name('admin.master.needle.edit');
                            Route::get('hapus/{id?}', [MasterNeedleController::class, 'hapus'])->name('admin.master.needle.hapus');
                        });

                    Route::prefix('/monthly-stock')
                        ->middleware(['permission:admin-master-monthly-stock'])
                        ->group(function () {
                            Route::get('', [MasterMonthlyStockController::class, 'index'])->name('admin.master.monthly-stock');
                            Route::get('data', [MasterMonthlyStockController::class, 'data'])->name('admin.master.monthly-stock.data');
                            Route::post('crup', [MasterMonthlyStockController::class, 'crup'])->name('admin.master.monthly-stock.crup');
                            Route::get('edit/{id?}', [MasterMonthlyStockController::class, 'edit'])->name('admin.master.monthly-stock.edit');
                            Route::get('hapus/{id?}', [MasterMonthlyStockController::class, 'hapus'])->name('admin.master.monthly-stock.hapus');
                        });

                    Route::prefix('/morning-stock')
                        ->middleware(['permission:admin-master-morning-stock'])
                        ->group(function () {
                            Route::get('', [MasterMorningStockController::class, 'index'])->name('admin.master.morning-stock');
                            Route::get('data', [MasterMorningStockController::class, 'data'])->name('admin.master.morning-stock.data');
                            Route::post('crup', [MasterMorningStockController::class, 'crup'])->name('admin.master.morning-stock.crup');
                            Route::get('edit/{id?}', [MasterMorningStockController::class, 'edit'])->name('admin.master.morning-stock.edit');
                            Route::get('hapus/{id?}', [MasterMorningStockController::class, 'hapus'])->name('admin.master.morning-stock.hapus');
                        });

                    Route::prefix('/buyer')
                        ->middleware(['permission:admin-master-buyer'])
                        ->group(function () {
                            Route::get('', [MasterBuyerController::class, 'index'])->name('admin.master.buyer');
                            Route::get('data', [MasterBuyerController::class, 'data'])->name('admin.master.buyer.data');
                            Route::post('crup', [MasterBuyerController::class, 'crup'])->name('admin.master.buyer.crup');
                            Route::get('edit/{id?}', [MasterBuyerController::class, 'edit'])->name('admin.master.buyer.edit');
                            Route::get('hapus/{id?}', [MasterBuyerController::class, 'hapus'])->name('admin.master.buyer.hapus');
                        });

                    Route::prefix('/category')
                        ->middleware(['permission:admin-master-category'])
                        ->group(function () {
                            Route::get('', [MasterCategoryController::class, 'index'])->name('admin.master.category');
                            Route::get('data', [MasterCategoryController::class, 'data'])->name('admin.master.category.data');
                            Route::post('crup', [MasterCategoryController::class, 'crup'])->name('admin.master.category.crup');
                            Route::get('edit/{id?}', [MasterCategoryController::class, 'edit'])->name('admin.master.category.edit');
                            Route::get('hapus/{id?}', [MasterCategoryController::class, 'hapus'])->name('admin.master.category.hapus');
                        });

                    Route::prefix('/sub-category')
                        ->middleware(['permission:admin-master-sub-category'])
                        ->group(function () {
                            Route::get('', [MasterSubCategoryController::class, 'index'])->name('admin.master.sub-category');
                            Route::get('data', [MasterSubCategoryController::class, 'data'])->name('admin.master.sub-category.data');
                            Route::post('crup', [MasterSubCategoryController::class, 'crup'])->name('admin.master.sub-category.crup');
                            Route::get('edit/{id?}', [MasterSubCategoryController::class, 'edit'])->name('admin.master.sub-category.edit');
                            Route::get('hapus/{id?}', [MasterSubCategoryController::class, 'hapus'])->name('admin.master.sub-category.hapus');
                        });

                    Route::prefix('/sample')
                        ->middleware(['permission:admin-master-sample'])
                        ->group(function () {
                            Route::get('', [MasterSampleController::class, 'index'])->name('admin.master.sample');
                            Route::get('data', [MasterSampleController::class, 'data'])->name('admin.master.sample.data');
                            Route::post('crup', [MasterSampleController::class, 'crup'])->name('admin.master.sample.crup');
                            Route::get('edit/{id?}', [MasterSampleController::class, 'edit'])->name('admin.master.sample.edit');
                            Route::get('hapus/{id?}', [MasterSampleController::class, 'hapus'])->name('admin.master.sample.hapus');
                        });

                    Route::prefix('/fabric')
                        ->middleware(['permission:admin-master-fabric'])
                        ->group(function () {
                            Route::get('', [MasterFabricController::class, 'index'])->name('admin.master.fabric');
                            Route::get('data', [MasterFabricController::class, 'data'])->name('admin.master.fabric.data');
                            Route::post('crup', [MasterFabricController::class, 'crup'])->name('admin.master.fabric.crup');
                            Route::get('edit/{id?}', [MasterFabricController::class, 'edit'])->name('admin.master.fabric.edit');
                            Route::get('hapus/{id?}', [MasterFabricController::class, 'hapus'])->name('admin.master.fabric.hapus');
                        });

                    Route::prefix('/style')
                        ->middleware(['permission:admin-master-style'])
                        ->group(function () {
                            Route::get('', [MasterStyleController::class, 'index'])->name('admin.master.style');
                            Route::get('data', [MasterStyleController::class, 'data'])->name('admin.master.style.data');
                            Route::post('crup', [MasterStyleController::class, 'crup'])->name('admin.master.style.crup');
                            Route::get('edit/{id?}', [MasterStyleController::class, 'edit'])->name('admin.master.style.edit');
                            Route::get('hapus/{id?}', [MasterStyleController::class, 'hapus'])->name('admin.master.style.hapus');
                            Route::get('template', [MasterStyleController::class, 'template'])->name('admin.master.style.template');
                            Route::post('import', [MasterStyleController::class, 'import'])->name('admin.master.style.import');
                        });
                });

            Route::prefix('/tools')
                ->middleware(['permission:admin-tools'])
                ->group(function () {
                    Route::prefix('/needle-control')
                        ->middleware(['permission:admin-tools-needle-control'])
                        ->group(function () {
                            Route::get('', [ToolsNeedleControlController::class, 'index'])->name('admin.tools.needle-control');
                            Route::get('data', [ToolsNeedleControlController::class, 'data'])->name('admin.tools.needle-control.data');
                            Route::post('update', [ToolsNeedleControlController::class, 'update'])->name('admin.tools.needle-control.update');
                            Route::get('edit/{tipe?}/{id?}', [ToolsNeedleControlController::class, 'edit'])->name('admin.tools.needle-control.edit');
                            Route::get('hapus/{tipe?}/{id?}', [ToolsNeedleControlController::class, 'hapus'])->name('admin.tools.needle-control.hapus');
                        });

                    Route::prefix('/user')
                        ->middleware(['permission:admin-tools-user'])
                        ->group(function () {
                            Route::get('', [ToolsUserController::class, 'index'])->name('admin.tools.user');
                            Route::get('/data', [ToolsUserController::class, 'data'])->name('admin.tools.user.data');
                            Route::post('/crup', [ToolsUserController::class, 'crup'])->name('admin.tools.user.crup');
                            Route::get('/edit/{id?}', [ToolsUserController::class, 'edit'])->name('admin.tools.user.edit');
                            Route::get('/check/{id?}', [ToolsUserController::class, 'check'])->name('admin.tools.user.check');
                            Route::post('/hapus', [ToolsUserController::class, 'hapus'])->name('admin.tools.user.hapus');
                            Route::get('/detail/{id?}/{username?}', [ToolsUserController::class, 'detail'])->name('admin.tools.user.detail');
                            Route::get('/data-role', [ToolsUserController::class, 'data_role'])->name('admin.tools.user.data-role');
                            Route::get('/spinner', [ToolsUserController::class, 'spinner'])->name('admin.tools.user.spinner');
                            Route::post('/crup-role', [ToolsUserController::class, 'crup_role'])->name('admin.tools.user.crup-role');
                            Route::get('/hapus-role/{user_id?}/{id?}', [ToolsUserController::class, 'hapus_role'])->name('admin.tools.user.hapus-role');
                        });

                    Route::prefix('/activity-log')
                        ->middleware(['permission:admin-tools-activity-log'])
                        ->group(function () {
                            Route::get('', [ToolsActivityLogController::class, 'index'])->name('admin.tools.activity-log');
                            Route::get('/data', [ToolsActivityLogController::class, 'data'])->name('admin.tools.activity-log.data');
                            Route::get('/hapus', [ToolsActivityLogController::class, 'hapus'])->name('admin.tools.activity-log.hapus');
                        });

                    Route::prefix('/permission')
                        ->middleware(['permission:admin-tools-permission'])
                        ->group(function () {
                            Route::get('', [ToolsPermissionController::class, 'index'])->name('admin.tools.permission');
                            Route::get('/data', [ToolsPermissionController::class, 'data'])->name('admin.tools.permission.data');
                            Route::post('/crup', [ToolsPermissionController::class, 'crup'])->name('admin.tools.permission.crup');
                            Route::get('/edit/{id?}', [ToolsPermissionController::class, 'edit'])->name('admin.tools.permission.edit');
                            Route::get('/hapus/{id?}', [ToolsPermissionController::class, 'hapus'])->name('admin.tools.permission.hapus');
                        });

                    Route::prefix('/role')
                        ->middleware(['permission:admin-tools-role'])
                        ->group(function () {
                            Route::get('', [ToolsRoleController::class, 'index'])->name('admin.tools.role');
                            Route::get('/data', [ToolsRoleController::class, 'data'])->name('admin.tools.role.data');
                            Route::post('/crup', [ToolsRoleController::class, 'crup'])->name('admin.tools.role.crup');
                            Route::get('/edit/{id?}', [ToolsRoleController::class, 'edit'])->name('admin.tools.role.edit');
                            Route::get('/hapus/{id?}', [ToolsRoleController::class, 'hapus'])->name('admin.tools.role.hapus');
                            Route::get('/detail/{id?}/{name?}', [ToolsRoleController::class, 'detail'])->name('admin.tools.role.detail');
                            Route::get('/spinner', [ToolsRoleController::class, 'spinner'])->name('admin.tools.role.spinner');
                            Route::get('/data-permission', [ToolsRoleController::class, 'data_permission'])->name('admin.tools.role.data-permission');
                            Route::post('/crup-permission', [ToolsRoleController::class, 'crup_permission'])->name('admin.tools.role.crup-permission');
                            Route::get('/hapus-permission/{role_id?}/{id?}', [ToolsRoleController::class, 'hapus_permission'])->name('admin.tools.role.hapus-permission');
                        });
                });

            Route::prefix('/profile')
                // ->middleware(['permission:admin-profile'])
                ->group(function () {
                    Route::get('', [ProfileController::class, 'index'])->name('admin.profile');
                    Route::post('/change', [ProfileController::class, 'change'])->name('admin.profile.change');
                });
        });
});
