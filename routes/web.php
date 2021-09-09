<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login');
Route::redirect('/register', '/login');

Route::middleware(['auth:sanctum', 'verified'])->group( function () {

    Route::get('/dashboard', function () {
        return Inertia\Inertia::render('Dashboard');
    })->name('dashboard');

    /**
     * system
     */
    Route::get('/user', [App\Http\Controllers\User::class, 'index'])
        ->name('user');
    Route::get('/user/edit/{id}', [App\Http\Controllers\User::class, 'edit'])
        ->name('user.edit');
    Route::get('/user/edit', [App\Http\Controllers\User::class, 'edit'])
        ->name('user.new');
    Route::post('/user/update/{id}', [App\Http\Controllers\User::class, 'update'])
        ->name('user.update');
    Route::post('/user/create', [App\Http\Controllers\User::class, 'store'])
        ->name('user.create');
    Route::get('/user/destroy/{id}', [App\Http\Controllers\User::class, 'destroy'])
        ->name('user.destroy');

    /**
     * dataflows
     */
    Route::get('/dataflows/report', [App\Http\Controllers\Dataflows\Report::class, 'index'])
        ->name('dataflows.report');
    Route::get('/dataflows/schedule', [App\Http\Controllers\Dataflows\Schedule::class, 'index'])
        ->name('dataflows.schedules');
    Route::get('/dataflows/schedule/edit/{id}', [App\Http\Controllers\Dataflows\Schedule::class, 'edit'])
        ->name('dataflows.schedule.edit');
    Route::post('/dataflows/schedule/update/{id}', [App\Http\Controllers\Dataflows\Schedule::class, 'update'])
        ->name('dataflows.schedule.update');
    Route::post('/dataflows/schedule/addToQueue/{id}', [App\Http\Controllers\Dataflows\Schedule::class, 'addToQueue'])
        ->name('dataflows.schedule.addToQueue');
    Route::get('/dataflows/report/view/{id}', [App\Http\Controllers\Dataflows\Report\View::class, 'index'])
        ->name('dataflows.report.view');

    /**
     * bizami
     */
    Route::get('/bizami/product', [App\Http\Controllers\Bizami\Product::class, 'index'])
        ->name('bizami.product');
    Route::get('/bizami/warehouse', [App\Http\Controllers\Bizami\Warehouse::class, 'index'])
        ->name('bizami.warehouse');
    Route::get('/bizami/warehouse/edit/{id}', [App\Http\Controllers\Bizami\Warehouse::class, 'edit'])
        ->name('bizami.warehouse.edit');
    Route::post('/bizami/warehouse/update/{id}', [App\Http\Controllers\Bizami\Warehouse::class, 'update'])
        ->name('bizami.warehouse.update');
    Route::get('/bizami/warehouse-state', [App\Http\Controllers\Bizami\WarehouseState::class, 'index'])
        ->name('bizami.warehouse-state');
    Route::get('/bizami/sale-document', [App\Http\Controllers\Bizami\SaleDocument::class, 'index'])
        ->name('bizami.sale-document');

    Route::get('/bizami/algorithm', [App\Http\Controllers\Bizami\Algorithm::class, 'index'])
        ->name('bizami.algorithm');
    Route::get('/bizami/algorithm/edit/{id}', [App\Http\Controllers\Bizami\Algorithm::class, 'edit'])
        ->name('bizami.algorithm.edit');
    Route::get('/bizami/algorithm/new', [App\Http\Controllers\Bizami\Algorithm::class, 'edit'])
        ->name('bizami.algorithm.new');
    Route::post('/bizami/algorithm/update/{id}', [App\Http\Controllers\Bizami\Algorithm::class, 'update'])
        ->name('bizami.algorithm.update');
    Route::post('/bizami/algorithm/create', [App\Http\Controllers\Bizami\Algorithm::class, 'store'])
        ->name('bizami.algorithm.create');
    Route::get('/bizami/algorithm/destroy/{id}', [App\Http\Controllers\Bizami\Algorithm::class, 'destroy'])
        ->name('bizami.algorithm.destroy');

    /**
     * bizami simualtion
     */
    Route::get('/bizami/simulations', [App\Http\Controllers\Bizami\Simulation::class, 'index'])
        ->name('bizami.simulations');
    Route::get('/bizami/simulation/new', [App\Http\Controllers\Bizami\Simulation::class, 'new'])
        ->name('bizami.simulation.new');
    Route::post('/bizami/simulation/new/create/{id?}', [App\Http\Controllers\Bizami\Simulation::class, 'create'])
        ->name('bizami.simulation.new.create');
    Route::get('/bizami/simulation/view/{id}/{view?}', [App\Http\Controllers\Bizami\Simulation::class, 'view'])
        ->name('bizami.simulation.view');
    Route::get('/bizami/simulation/TableData/{id}/{view?}', [App\Http\Controllers\Bizami\Simulation\TableData::class, 'index'])
        ->name('bizami.simulation.tableData');
    Route::post('/bizami/simulation/update/{id}/{view?}', [App\Http\Controllers\Bizami\Simulation::class, 'update'])
        ->name('bizami.simulation.update');
});


