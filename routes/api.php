<?php

use App\Http\Controllers\api\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AouthController;
use App\Http\Controllers\api\BillController;
use App\Http\Controllers\api\CustmerController;
use App\Http\Controllers\api\BondsController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\DriverController;
use App\Http\Controllers\api\MaterialController;
use App\Http\Controllers\api\SupplierController;
use App\Http\Controllers\api\Buy_BillController;
use App\Http\Controllers\api\Sales_BillController;
use App\Http\Controllers\api\UnitController;
use App\Http\Controllers\api\reportController;
use App\Http\Controllers\api\billDetailController;
use App\Http\Controllers\api\BondRelationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('register',[AouthController::class,'register']);
Route::get('login',[AouthController::class,'login']);

//Basheer


Route::resource('/customer', CustmerController::class);

Route::resource('/bonds', BondsController::class);

Route::resource('/category', CategoryController::class);

Route::resource('/driver', DriverController::class);

Route::resource('/material', MaterialController::class);

Route::resource('/supplier', SupplierController::class);

Route::resource('/unit', UnitController::class);

Route::resource('/account', AccountController::class);

Route::resource('/bill_detail', billDetailController::class);


Route::controller(BondRelationController::class)->group(function () {

    Route::get('bond_relation', 'index');
    Route::get('bond_relation/{id}', 'show');
    Route::post('bond_relation/{id}', 'store');
    Route::put('bond_relation/{id}', 'update');
    Route::delete('bond_relation/{id}', 'destroy');
});

Route::controller(BillController::class)->group(function () {

    Route::get('bill', 'index');
    Route::get('bill/{id}', 'show');
    Route::post('bill', 'store');
    Route::put('bill/{id}', 'update');
    Route::delete('bill/{id}', 'destroy');
});

//
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/BillReportCustomer/{customer_id}', [reportController::class,'salesReportForClientStatement']);

Route::get('/BillReportdriver/{driver_name}', [reportController::class,'salesReportForDrivertStatement']);

Route::get('/BillReportsupplier/{supplier_name}', [reportController::class,'BuyReportForsupplierStatement']);

Route::get('/BillType/{type}', [reportController::class,'saleReportForTypeOfBill']);

