<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRM\CustomerController;
use App\Http\Controllers\CRM\DriverController;
use App\Http\Controllers\CRM\InvestorController;
use App\Http\Controllers\Fleet\AssignmentController;
use App\Http\Controllers\Fleet\FixedCostController;
use App\Http\Controllers\Fleet\VanController;
use App\Http\Controllers\Ledger\CustomerLedgerController;
use App\Http\Controllers\Ledger\ProfitLossController;
use App\Http\Controllers\Ledger\TrialBalanceController;
use App\Http\Controllers\Ledger\VanLedgerController;
use App\Http\Controllers\Operations\ExpenseController;
use App\Http\Controllers\Operations\TripController;
use App\Http\Controllers\Operations\WageController;

// Demo routes — generic client presentation
// Route::get('/', fn() => redirect('/dashboard'));
Route::get('/', fn() => redirect('/courier/dashboard'));
Route::prefix('courier')->group(function () {
Route::get('/dashboard',         fn() => view('demo.dashboard'))->name('dashboard');
Route::get('/fleet/vans',                  [VanController::class, 'index'])->name('fleet.vans');
Route::post('/fleet/vans',                 [VanController::class, 'store'])->name('fleet.vans.store');
Route::put('/fleet/vans/{van}',            [VanController::class, 'update'])->name('fleet.vans.update');
Route::delete('/fleet/vans/{van}',         [VanController::class, 'destroy'])->name('fleet.vans.destroy');
Route::get('/fleet/vans/model/{model}',    [VanController::class, 'byModel'])->name('fleet.vans.model')->where('model', '.*');
Route::get('/fleet/fixed-costs',          [FixedCostController::class, 'index'])->name('fleet.fixed-costs');
Route::post('/fleet/vans/{van}/fixed-costs', [FixedCostController::class, 'upsert'])->name('fleet.fixed-costs.upsert');
Route::get('/fleet/assignments',           [AssignmentController::class, 'index'])->name('fleet.assignments');
Route::post('/fleet/assignments',          [AssignmentController::class, 'assign'])->name('fleet.assignments.assign');
Route::delete('/fleet/assignments/{assignment}', [AssignmentController::class, 'end'])->name('fleet.assignments.end');
Route::get('/operations/trips',              [TripController::class, 'index'])->name('operations.trips');
Route::post('/operations/trips',             [TripController::class, 'store'])->name('operations.trips.store');
Route::post('/operations/trips/{trip}/void', [TripController::class, 'void'])->name('operations.trips.void');
Route::get('/operations/expenses',              [ExpenseController::class, 'index'])->name('operations.expenses');
Route::post('/operations/expenses',             [ExpenseController::class, 'store'])->name('operations.expenses.store');
Route::put('/operations/expenses/{expense}',    [ExpenseController::class, 'update'])->name('operations.expenses.update');
Route::delete('/operations/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('operations.expenses.destroy');
Route::get('/operations/wages',                    [WageController::class, 'index'])->name('operations.wages');
Route::post('/operations/wages/advance',           [WageController::class, 'storeAdvance'])->name('operations.wages.advance');
Route::delete('/operations/wages/advance/{advance}', [WageController::class, 'destroyAdvance'])->name('operations.wages.advance.destroy');
Route::post('/operations/wages/preview',           [WageController::class, 'preview'])->name('operations.wages.preview');
Route::post('/operations/wages/payout',            [WageController::class, 'storePayout'])->name('operations.wages.payout');
Route::get('/crm/customers',           [CustomerController::class, 'index'])->name('crm.customers');
Route::post('/crm/customers',          [CustomerController::class, 'store'])->name('crm.customers.store');
Route::get('/crm/customers/{customer}',[CustomerController::class, 'show'])->name('crm.customers.show');
Route::put('/crm/customers/{customer}',[CustomerController::class, 'update'])->name('crm.customers.update');
Route::delete('/crm/customers/{customer}',[CustomerController::class, 'destroy'])->name('crm.customers.destroy');
Route::get('/crm/drivers',            [DriverController::class, 'index'])->name('crm.drivers');
Route::post('/crm/drivers',           [DriverController::class, 'store'])->name('crm.drivers.store');
Route::get('/crm/drivers/{driver}',   [DriverController::class, 'show'])->name('crm.drivers.show');
Route::put('/crm/drivers/{driver}',   [DriverController::class, 'update'])->name('crm.drivers.update');
Route::delete('/crm/drivers/{driver}',[DriverController::class, 'destroy'])->name('crm.drivers.destroy');
Route::get('/crm/investors',                           [InvestorController::class, 'index'])->name('crm.investors');
Route::post('/crm/investors',                          [InvestorController::class, 'store'])->name('crm.investors.store');
Route::put('/crm/investors/{investor}',                [InvestorController::class, 'update'])->name('crm.investors.update');
Route::delete('/crm/investors/{investor}',             [InvestorController::class, 'destroy'])->name('crm.investors.destroy');
Route::post('/crm/investors/{investor}/inject',        [InvestorController::class, 'inject'])->name('crm.investors.inject');
Route::post('/crm/investors/{investor}/distribute',    [InvestorController::class, 'distribute'])->name('crm.investors.distribute');
Route::get('/ledger/van-ledger',              [VanLedgerController::class, 'index'])->name('ledger.van');
Route::get('/ledger/van-ledger/{van}',         [VanLedgerController::class, 'show'])->name('ledger.van.detail');
Route::get('/ledger/customer-ledger',                  [CustomerLedgerController::class, 'index'])->name('ledger.customer');
Route::get('/ledger/customer-ledger/{customer}',       [CustomerLedgerController::class, 'show'])->name('ledger.customer.statement');
Route::get('/ledger/trial-balance', [TrialBalanceController::class, 'index'])->name('ledger.trial-balance');
Route::get('/ledger/profit-loss', [ProfitLossController::class, 'index'])->name('ledger.profit-loss');
Route::get('/ledger/balance-sheet',  fn() => view('demo.ledger.balance-sheet'))->name('ledger.balance-sheet');
Route::get('/settings/users',        fn() => view('demo.settings.users'))->name('settings.users');
Route::get('/settings/tenant',       fn() => view('demo.settings.tenant'))->name('settings.tenant');
});


// ── Isolated Demo Mode Group ──────────────────────────────────────────
Route::prefix('courier/demo')->name('demo.')->middleware(['demo.mode', 'demo.limit'])->group(function () {
    Route::get('/', fn() => redirect('/courier/demo/dashboard'));
    Route::get('/dashboard',         fn() => view('demo.dashboard'))->name('dashboard');

    Route::get('/fleet/vans',                  [VanController::class, 'index'])->name('fleet.vans');
    Route::post('/fleet/vans',                 [VanController::class, 'store'])->name('fleet.vans.store');
    Route::put('/fleet/vans/{van}',            [VanController::class, 'update'])->name('fleet.vans.update');
    Route::delete('/fleet/vans/{van}',         [VanController::class, 'destroy'])->name('fleet.vans.destroy');
    Route::get('/fleet/vans/model/{model}',    [VanController::class, 'byModel'])->name('fleet.vans.model')->where('model', '.*');

    Route::get('/fleet/fixed-costs',            [FixedCostController::class, 'index'])->name('fleet.fixed-costs');
    Route::post('/fleet/vans/{van}/fixed-costs', [FixedCostController::class, 'upsert'])->name('fleet.fixed-costs.upsert');

    Route::get('/fleet/assignments',           [AssignmentController::class, 'index'])->name('fleet.assignments');
    Route::post('/fleet/assignments',          [AssignmentController::class, 'assign'])->name('fleet.assignments.assign');
    Route::delete('/fleet/assignments/{assignment}', [AssignmentController::class, 'end'])->name('fleet.assignments.end');

    Route::get('/operations/trips',              [TripController::class, 'index'])->name('operations.trips');
    Route::post('/operations/trips',             [TripController::class, 'store'])->name('operations.trips.store');
    Route::post('/operations/trips/{trip}/void', [TripController::class, 'void'])->name('operations.trips.void');

    Route::get('/operations/expenses',              [ExpenseController::class, 'index'])->name('operations.expenses');
    Route::post('/operations/expenses',             [ExpenseController::class, 'store'])->name('operations.expenses.store');
    Route::put('/operations/expenses/{expense}',    [ExpenseController::class, 'update'])->name('operations.expenses.update');
    Route::delete('/operations/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('operations.expenses.destroy');

    Route::get('/operations/wages',                    [WageController::class, 'index'])->name('operations.wages');
    Route::post('/operations/wages/advance',           [WageController::class, 'storeAdvance'])->name('operations.wages.advance');
    Route::delete('/operations/wages/advance/{advance}', [WageController::class, 'destroyAdvance'])->name('operations.wages.advance.destroy');
    Route::post('/operations/wages/preview',           [WageController::class, 'preview'])->name('operations.wages.preview');
    Route::post('/operations/wages/payout',            [WageController::class, 'storePayout'])->name('operations.wages.payout');

    Route::get('/crm/customers',            [CustomerController::class, 'index'])->name('crm.customers');
    Route::post('/crm/customers',           [CustomerController::class, 'store'])->name('crm.customers.store');
    Route::get('/crm/customers/{customer}', [CustomerController::class, 'show'])->name('crm.customers.show');
    Route::put('/crm/customers/{customer}', [CustomerController::class, 'update'])->name('crm.customers.update');
    Route::delete('/crm/customers/{customer}',[CustomerController::class, 'destroy'])->name('crm.customers.destroy');

    Route::get('/crm/drivers',            [DriverController::class, 'index'])->name('crm.drivers');
    Route::post('/crm/drivers',           [DriverController::class, 'store'])->name('crm.drivers.store');
    Route::get('/crm/drivers/{driver}',   [DriverController::class, 'show'])->name('crm.drivers.show');
    Route::put('/crm/drivers/{driver}',   [DriverController::class, 'update'])->name('crm.drivers.update');
    Route::delete('/crm/drivers/{driver}',[DriverController::class, 'destroy'])->name('crm.drivers.destroy');

    Route::get('/crm/investors',                           [InvestorController::class, 'index'])->name('crm.investors');
    Route::post('/crm/investors',                          [InvestorController::class, 'store'])->name('crm.investors.store');
    Route::put('/crm/investors/{investor}',                [InvestorController::class, 'update'])->name('crm.investors.update');
    Route::delete('/crm/investors/{investor}',             [InvestorController::class, 'destroy'])->name('crm.investors.destroy');
    Route::post('/crm/investors/{investor}/inject',        [InvestorController::class, 'inject'])->name('crm.investors.inject');
    Route::post('/crm/investors/{investor}/distribute',    [InvestorController::class, 'distribute'])->name('crm.investors.distribute');

    Route::get('/ledger/van-ledger',              [VanLedgerController::class, 'index'])->name('ledger.van');
    Route::get('/ledger/van-ledger/{van}',         [VanLedgerController::class, 'show'])->name('ledger.van.detail');
    Route::get('/ledger/customer-ledger',          [CustomerLedgerController::class, 'index'])->name('ledger.customer');
    Route::get('/ledger/customer-ledger/{customer}',[CustomerLedgerController::class, 'show'])->name('ledger.customer.statement');
    Route::get('/ledger/trial-balance',           [TrialBalanceController::class, 'index'])->name('ledger.trial-balance');
    Route::get('/ledger/profit-loss',              [ProfitLossController::class, 'index'])->name('ledger.profit-loss');
    Route::get('/ledger/balance-sheet',           fn() => view('demo.ledger.balance-sheet'))->name('ledger.balance-sheet');
    Route::get('/settings/users',                 fn() => view('demo.settings.users'))->name('settings.users');
    Route::get('/settings/tenant',                fn() => view('demo.settings.tenant'))->name('settings.tenant');
});
