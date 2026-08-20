<?php

use Illuminate\Support\Facades\Route;

// Demo routes — generic client presentation
Route::get('/', fn() => redirect('/dashboard'));
Route::get('/dashboard',         fn() => view('demo.dashboard'))->name('dashboard');
use App\Http\Controllers\Fleet\VanController;

Route::get('/fleet/vans',                  [VanController::class, 'index'])->name('fleet.vans');
Route::post('/fleet/vans',                 [VanController::class, 'store'])->name('fleet.vans.store');
Route::put('/fleet/vans/{van}',            [VanController::class, 'update'])->name('fleet.vans.update');
Route::delete('/fleet/vans/{van}',         [VanController::class, 'destroy'])->name('fleet.vans.destroy');
Route::get('/fleet/vans/model/{model}',    [VanController::class, 'byModel'])->name('fleet.vans.model')->where('model', '.*');
use App\Http\Controllers\Fleet\FixedCostController;

Route::get('/fleet/fixed-costs',          [FixedCostController::class, 'index'])->name('fleet.fixed-costs');
Route::post('/fleet/vans/{van}/fixed-costs', [FixedCostController::class, 'upsert'])->name('fleet.fixed-costs.upsert');
use App\Http\Controllers\Fleet\AssignmentController;

Route::get('/fleet/assignments',           [AssignmentController::class, 'index'])->name('fleet.assignments');
Route::post('/fleet/assignments',          [AssignmentController::class, 'assign'])->name('fleet.assignments.assign');
Route::delete('/fleet/assignments/{assignment}', [AssignmentController::class, 'end'])->name('fleet.assignments.end');
Route::get('/operations/trips',   fn() => view('demo.operations.trips'))->name('operations.trips');
Route::get('/operations/expenses',fn() => view('demo.operations.expenses'))->name('operations.expenses');
Route::get('/operations/wages',   fn() => view('demo.operations.wages'))->name('operations.wages');
Route::get('/crm/customers',     fn() => view('demo.crm.customers'))->name('crm.customers');
Route::get('/crm/drivers',       fn() => view('demo.crm.drivers'))->name('crm.drivers');
Route::get('/crm/investors',     fn() => view('demo.crm.investors'))->name('crm.investors');
Route::get('/ledger/van-ledger',     fn() => view('demo.ledger.van-ledger'))->name('ledger.van');
Route::get('/ledger/customer-ledger',fn() => view('demo.ledger.customer-ledger'))->name('ledger.customer');
Route::get('/ledger/trial-balance',  fn() => view('demo.ledger.trial-balance'))->name('ledger.trial-balance');
Route::get('/ledger/profit-loss',    fn() => view('demo.ledger.profit-loss'))->name('ledger.profit-loss');
Route::get('/ledger/balance-sheet',  fn() => view('demo.ledger.balance-sheet'))->name('ledger.balance-sheet');
Route::get('/settings/users',        fn() => view('demo.settings.users'))->name('settings.users');
Route::get('/settings/tenant',       fn() => view('demo.settings.tenant'))->name('settings.tenant');
