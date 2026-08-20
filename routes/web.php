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
use App\Http\Controllers\Operations\TripController;
Route::get('/operations/trips',              [TripController::class, 'index'])->name('operations.trips');
Route::post('/operations/trips',             [TripController::class, 'store'])->name('operations.trips.store');
Route::post('/operations/trips/{trip}/void', [TripController::class, 'void'])->name('operations.trips.void');
use App\Http\Controllers\Operations\ExpenseController;

Route::get('/operations/expenses',              [ExpenseController::class, 'index'])->name('operations.expenses');
Route::post('/operations/expenses',             [ExpenseController::class, 'store'])->name('operations.expenses.store');
Route::put('/operations/expenses/{expense}',    [ExpenseController::class, 'update'])->name('operations.expenses.update');
Route::delete('/operations/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('operations.expenses.destroy');
use App\Http\Controllers\Operations\WageController;

Route::get('/operations/wages',                    [WageController::class, 'index'])->name('operations.wages');
Route::post('/operations/wages/advance',           [WageController::class, 'storeAdvance'])->name('operations.wages.advance');
Route::delete('/operations/wages/advance/{advance}', [WageController::class, 'destroyAdvance'])->name('operations.wages.advance.destroy');
Route::post('/operations/wages/preview',           [WageController::class, 'preview'])->name('operations.wages.preview');
Route::post('/operations/wages/payout',            [WageController::class, 'storePayout'])->name('operations.wages.payout');
use App\Http\Controllers\CRM\CustomerController;

Route::get('/crm/customers',           [CustomerController::class, 'index'])->name('crm.customers');
Route::post('/crm/customers',          [CustomerController::class, 'store'])->name('crm.customers.store');
Route::get('/crm/customers/{customer}',[CustomerController::class, 'show'])->name('crm.customers.show');
Route::put('/crm/customers/{customer}',[CustomerController::class, 'update'])->name('crm.customers.update');
Route::delete('/crm/customers/{customer}',[CustomerController::class, 'destroy'])->name('crm.customers.destroy');
Route::get('/crm/drivers',       fn() => view('demo.crm.drivers'))->name('crm.drivers');
Route::get('/crm/investors',     fn() => view('demo.crm.investors'))->name('crm.investors');
Route::get('/ledger/van-ledger',     fn() => view('demo.ledger.van-ledger'))->name('ledger.van');
Route::get('/ledger/customer-ledger',fn() => view('demo.ledger.customer-ledger'))->name('ledger.customer');
Route::get('/ledger/trial-balance',  fn() => view('demo.ledger.trial-balance'))->name('ledger.trial-balance');
Route::get('/ledger/profit-loss',    fn() => view('demo.ledger.profit-loss'))->name('ledger.profit-loss');
Route::get('/ledger/balance-sheet',  fn() => view('demo.ledger.balance-sheet'))->name('ledger.balance-sheet');
Route::get('/settings/users',        fn() => view('demo.settings.users'))->name('settings.users');
Route::get('/settings/tenant',       fn() => view('demo.settings.tenant'))->name('settings.tenant');
