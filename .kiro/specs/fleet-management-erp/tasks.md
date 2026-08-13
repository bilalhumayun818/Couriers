# Implementation Plan: CX_COURIER Fleet Management & Courier Operations ERP

## Overview

This plan converts the CX_COURIER design into incremental coding tasks. The backend is a **Laravel 13 REST API** (PHP 8.3, MySQL, Laravel Sanctum) and the frontend is a **Next.js / React** application with Tailwind CSS v4 and Shadcn UI. Tasks are ordered so that each step compiles and wires together before moving to the next layer. Property-based tests use the `eris/eris` PHP library (min 100 iterations per property).

---

## Tasks

- [ ] 1. Project Setup & Infrastructure
  - [ ] 1.1 Configure Laravel backend foundations
    - Install and pin `laravel/sanctum`, `spatie/laravel-query-builder`, `eris/eris` (dev), `barryvdh/laravel-dompdf` in `composer.json`
    - Publish Sanctum config; set token expiry to 24 hours in `config/sanctum.php`
    - Register `EnsureTenantScope` and `CheckRole` middleware aliases in `bootstrap/app.php`
    - Create `routes/api.php` with `/api/v1` prefix group wired to Sanctum + tenant middleware
    - _Requirements: 1.2, 2.2_

  - [ ] 1.2 Scaffold the Next.js frontend project
    - Initialise a Next.js 14 App Router project inside `frontend/` with TypeScript strict mode
    - Install and configure Tailwind CSS v4, Shadcn UI (`npx shadcn-ui@latest init`), and Lucide Icons
    - Create `frontend/lib/api.ts` (Axios instance with base URL, auth header injection, and 401 interceptor that clears token and redirects to `/login`)
    - Create `frontend/lib/auth.ts` (AuthContext, token storage, `useAuth` hook)
    - Create `frontend/types/index.ts` with TypeScript interfaces matching all API shapes defined in the design
    - _Requirements: 3.1, 24.1_

  - [ ] 1.3 Create multi-tenancy core (TenantScope + middleware)
    - Create `app/Models/Tenant.php` with `HasMany` relationships to all core models
    - Create `app/Scopes/TenantScope.php` as a global Eloquent scope appending `WHERE tenant_id = ?`
    - Create `app/Http/Middleware/EnsureTenantScope.php` — resolves tenant from `auth()->user()->tenant_id` and binds it to the request
    - Create `app/Policies/TenantAwarePolicy.php` — base policy class with `belongsToTenant()` guard returning 403 on mismatch
    - _Requirements: 1.1, 1.2, 1.3_

  - [ ] 1.4 Write all database migrations
    - Create migrations in order: `tenants`, `users` (extend existing, add `tenant_id`, `role`, `is_active`), `vans`, `fixed_costs`, `drivers`, `van_driver_assignments`, `customers`, `trips`, `expenses`, `driver_advances`, `wage_payouts`, `investors`, `chart_of_accounts`, `ledger_entries`, `audit_logs`
    - Apply all composite unique indexes and foreign keys as documented in the design
    - _Requirements: 1.1, 5.1, 6.1, 7.1, 8.1, 9.1, 10.1, 11.1, 12.1, 13.1, 14.1, 23.1_

  - [ ] 1.5 Create Eloquent models with relationships and TenantScope
    - Create all models listed in the design (`Van`, `FixedCost`, `Driver`, `VanDriverAssignment`, `Customer`, `Trip`, `Expense`, `DriverAdvance`, `WagePayout`, `Investor`, `ChartOfAccount`, `LedgerEntry`, `AuditLog`) with `$fillable`, `$casts`, soft-delete traits where required, and `static::addGlobalScope(new TenantScope)` boot calls
    - Define all `HasMany`/`BelongsTo` relationships as per the ERD
    - _Requirements: 1.1, 5.2, 6.2, 8.2_

  - [ ] 1.6 Seed default Chart of Accounts for tenant provisioning
    - Update `DatabaseSeeder.php` to call a `TenantSeeder` that inserts the 13 required default ChartOfAccount categories (`Revenue`, `Fuel`, `Tolls`, `Spare Parts`, `Maintenance/Repairs`, `Monthly Lease`, `Road Tax`, `Insurance`, `Driver Wages`, `Driver Advances`, `Legal Cover`, `Capital`, `Equity Distributions`) for a new tenant
    - Mark all 13 as `is_system = TRUE`
    - _Requirements: 1.4, 14.2_

- [ ] 2. Authentication & RBAC
  - [ ] 2.1 Implement AuthController (login, logout, me)
    - Create `app/Http/Controllers/Auth/AuthController.php` with `login`, `logout`, and `me` actions
    - `login`: validate email+password via `LoginRequest`, hash-verify with bcrypt cost ≥ 12, issue Sanctum token with 24h expiry, return `{token, expires_at}`; return generic 401 on any failure (never distinguish username vs password)
    - `logout`: revoke current token
    - `me`: return authenticated user + tenant
    - _Requirements: 2.2, 2.3, 2.6_

  - [ ] 2.2 Implement CheckRole middleware
    - Create `app/Http/Middleware/CheckRole.php` — reads `$user->role`, compares against allowed roles for the route; returns 403 on mismatch
    - Apply role guards to every route group as per the API design table
    - _Requirements: 2.4_

  - [ ]* 2.3 Write property test for authentication token expiry and bcrypt cost (Property 4)
    - **Property 4: Authentication Token Expiry Bound**
    - **Validates: Requirements 2.2, 2.6**
    - Use `eris/eris` to generate random valid user credentials; assert token `expires_at` ≤ `now + 24h` and password hash passes `password_needs_rehash` cost check

  - [ ]* 2.4 Write property test for credential indistinguishability (Property 5)
    - **Property 5: Credential Error Response Indistinguishability**
    - **Validates: Requirements 2.3**
    - Generate random invalid credential combos (wrong email, wrong password, both wrong); assert all return `401` with identical response body shape

  - [ ]* 2.5 Write property test for RBAC enforcement (Property 6)
    - **Property 6: Role-Based Access Control Enforcement**
    - **Validates: Requirements 2.4**
    - Enumerate all (role, endpoint, method) combinations from the permissions matrix; generate random users per role; assert grant/deny matches the matrix exactly

  - [ ] 2.6 Implement Settings — User & Role Management (UserController)
    - Create `app/Http/Controllers/Settings/UserController.php` with `index`, `store`, `update` actions
    - `store`: create user within same `tenant_id`, hash password with bcrypt cost 12
    - `update`: allow role change and `is_active` toggle; if `is_active = false`, call `$user->tokens()->delete()` to invalidate sessions
    - Guard: reject self-deactivation with 422 and message "You cannot deactivate your own account."
    - _Requirements: 21.1, 21.2, 21.3, 21.4, 21.5_

  - [ ] 2.7 Implement frontend Login page and AuthProvider
    - Create `frontend/app/(auth)/login/page.tsx` with Shadcn UI form (email + password), inline validation errors, loading spinner during submit
    - Wire to `POST /api/v1/auth/login`; store token in `AuthContext`; redirect to `/dashboard` on success
    - Implement `RouteGuard` HOC that redirects unauthenticated users to `/login`
    - _Requirements: 2.2, 2.3, 24.3, 24.4_

- [ ] 3. Sidebar Navigation Layout
  - [ ] 3.1 Implement Sidebar layout components
    - Create `frontend/components/layout/Sidebar.tsx` — renders the 6 top-level sections (Dashboard, Fleet Management, Operations & Daily Logs, Stakeholders & CRM, Financial Ledgers & Reports, System Settings) in the specified order
    - Create `SidebarGroup.tsx` — collapsible group with expand/collapse animation (no full page reload); persist expand state to `sessionStorage`
    - Create `SidebarItem.tsx` — applies active visual state via `usePathname()`
    - Create `TopBar.tsx` — user avatar, tenant name, logout button
    - Create `MobileSidebarDrawer.tsx` — off-canvas drawer toggled by hamburger button, hidden when viewport ≥ 768px
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

  - [ ] 3.2 Implement root AppLayout wiring sidebar into all app routes
    - Create `frontend/app/(app)/layout.tsx` — `SidebarLayout` wrapping all authenticated module pages, includes `RouteGuard`
    - Ensure sidebar collapses on mobile (< 768px) using Tailwind responsive classes
    - _Requirements: 3.1, 3.4, 24.1_

- [ ] 4. Shared Frontend Components
  - [ ] 4.1 Build DataTable shared component
    - Create `frontend/components/shared/DataTable.tsx` — accepts columns config, data array, pagination meta, sort state, filter state; renders Shadcn UI `Table` with header sort toggles, pagination controls, and per-column filter inputs
    - _Requirements: 5.6, 8.4, 9.4, 11.4, 15.2, 16.2_

  - [ ] 4.2 Build KpiCard, DateRangePicker, ExportButton, LoadingSpinner, ErrorBanner, ConfirmDialog, FormModal
    - `KpiCard.tsx`: title, value, unit, trend indicator
    - `DateRangePicker.tsx`: calendar popover with from/to date selection
    - `ExportButton.tsx`: dropdown for CSV / PDF download via API
    - `LoadingSpinner.tsx`: centred spinner overlay on parent container
    - `ErrorBanner.tsx`: error message + retry button
    - `ConfirmDialog.tsx`: Shadcn UI `AlertDialog` with customisable title/message/confirm label
    - `FormModal.tsx`: Shadcn UI `Dialog` wrapper accepting a form component as children
    - _Requirements: 24.2, 24.3, 24.4, 24.5_

  - [ ] 4.3 Build currency / date / percentage formatters
    - Create `frontend/lib/formatters.ts` with `formatCurrency(amount, symbol)`, `formatDate(date)`, `formatPercent(value)` functions used across all module pages
    - _Requirements: 22.2_

- [ ] 5. Fleet Module — Vans
  - [ ] 5.1 Implement VanController (CRUD + soft-delete)
    - Create `app/Http/Controllers/Fleet/VanController.php` with `index`, `store`, `show`, `update`, `destroy`
    - `store`: validate plate_number uniqueness within `tenant_id`; set `status` default `active`; stamp `tenant_id`
    - `update`: block `tenant_id` field; enforce maintenance-status trip-block rule (return 422 if assigning trip to maintenance van)
    - `destroy`: soft-delete only; never hard-delete
    - `index`: paginated, searchable by plate/make, filterable by `status`
    - Create `StoreVanRequest.php` and `UpdateVanRequest.php` FormRequests
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

  - [ ] 5.2 Implement frontend Vans pages
    - Create `frontend/app/(app)/fleet/vans/page.tsx` — `DataTable` with van list, search input, status filter dropdown, "Add Van" button opening `FormModal`
    - Create `frontend/app/(app)/fleet/vans/[id]/page.tsx` — van detail with edit form, fixed costs section, current driver assignment, assignment history table
    - Create `frontend/components/modules/fleet/VanForm.tsx`
    - _Requirements: 5.1, 5.3, 5.6_

- [ ] 6. Fleet Module — Fixed Costs
  - [ ] 6.1 Implement FixedCostController
    - Create `app/Http/Controllers/Fleet/FixedCostController.php` with `show` and `upsert` (create-or-update) actions scoped to `van_id`
    - Associate `tenant_id` from authenticated user
    - Create `StoreFixedCostRequest.php` with validation for all four fields
    - _Requirements: 6.1, 6.2, 6.3_

  - [ ] 6.2 Add service-schedule alert logic to DashboardController
    - In `DashboardController`, query `fixed_costs` where `next_service_date BETWEEN today AND today+7` for the tenant; include a `maintenance_alerts` array in the KPI response
    - _Requirements: 6.5_

  - [ ] 6.3 Implement frontend Fixed Costs page
    - Create `frontend/app/(app)/fleet/fixed-costs/page.tsx` — list of vans with their fixed cost figures; inline edit form per van
    - Display maintenance alert badges on vans with upcoming service dates
    - _Requirements: 6.1, 6.3, 6.5_

- [ ] 7. Fleet Module — Driver Assignments
  - [ ] 7.1 Implement VanDriverAssignmentController
    - Create `app/Http/Controllers/Fleet/VanDriverAssignmentController.php` with `index` (assignment history for a van) and `store`
    - `store`: check if driver is currently assigned to another van; if yes, return 422 prompt payload (flag `requires_confirmation: true`); if confirmed (`force: true` in request), close previous assignment's `end_date = today` and create new assignment
    - Enforce exactly-one-active-assignment constraint at application layer
    - _Requirements: 7.1, 7.2, 7.3, 7.4_

  - [ ] 7.2 Implement frontend Driver Assignments page
    - Create `frontend/app/(app)/fleet/assignments/page.tsx` — table of current van↔driver assignments, "Reassign" button triggering `ConfirmDialog` when driver already assigned elsewhere
    - _Requirements: 7.1, 7.3_

- [ ] 8. Core Services — Ledger, Audit & Wage
  - [ ] 8.1 Implement LedgerService
    - Create `app/Services/LedgerService.php` with:
      - `recordRevenue(Trip $trip)`: create LedgerEntry (debit Accounts Receivable, credit Revenue) linked via `reference_type = 'trip'`
      - `reverseEntry(LedgerEntry $entry, string $reason)`: create offsetting entry with `is_reversal = TRUE`, `reversed_entry_id = $entry->id`; never delete original
      - `recordExpense(Expense $expense)`: create LedgerEntry (debit expense account, credit Cash/Bank)
      - `recordAdvance(DriverAdvance $advance)`: create LedgerEntry (debit Driver Advances, credit Cash/Bank)
      - `recordWage(WagePayout $payout)`: create LedgerEntry (debit Driver Wages, credit Cash/Bank)
      - `recordCapitalInjection` / `recordDistribution` for investor transactions
    - _Requirements: 8.2, 9.2, 10.2, 13.5, 23.3_

  - [ ] 8.2 Implement AuditService
    - Create `app/Services/AuditService.php` with `log(string $action, Model $model, array $before, array $after)` method
    - Create `app/Observers/` for `Trip`, `Expense`, `LedgerEntry`, `WagePayout`, `DriverAdvance` — each observer calls `AuditService::log` in `created`, `updated`, `deleted` hooks
    - Register observers in `AppServiceProvider`
    - _Requirements: 23.1, 23.2_

  - [ ] 8.3 Implement WageCalculationService
    - Create `app/Services/WageCalculationService.php` with `compute(float $grossWage, float $totalAdvances): array` returning `['net_wage' => ..., 'is_negative' => ..., 'total_advances' => ...]`
    - `net_wage = grossWage - totalAdvances`; `is_negative = net_wage < 0`
    - _Requirements: 10.4, 10.5_

  - [ ]* 8.4 Write property test for Operation-to-LedgerEntry round trip (Property 7)
    - **Property 7: Operation-to-LedgerEntry Round Trip**
    - **Validates: Requirements 8.2, 9.2**
    - Generate random Trip/Expense records; assert exactly one LedgerEntry created per record; assert credit(Revenue) = fare_amount for trips and debit(COA) = amount for expenses; assert `reference_type` + `reference_id` retrieves the originating record

  - [ ]* 8.5 Write property test for Ledger Reversal Immutability (Property 8)
    - **Property 8: Ledger Reversal Preserves Immutability**
    - **Validates: Requirements 8.3, 9.3, 23.3**
    - Generate sequences of trip-void and expense-delete events; assert original LedgerEntry still exists; assert reversal entry `is_reversal = TRUE`; assert sum(original.debit - reversal.debit, original.credit - reversal.credit) = 0 per account

  - [ ]* 8.6 Write property test for Net Wage Calculation (Property 9)
    - **Property 9: Net Wage Calculation Correctness**
    - **Validates: Requirements 10.4, 10.5**
    - Use `eris/eris` `pos()` and `vector(pos(), choose(0,10))` generators; assert `net_wage = grossWage - SUM(advances)` to 3 decimal places; assert `is_negative = TRUE` when advances > gross; assert `confirmed_by` required when `is_negative`

  - [ ]* 8.7 Write property test for Audit Log Completeness (Property 11)
    - **Property 11: Audit Log Completeness**
    - **Validates: Requirements 23.1**
    - Generate sequences of create/update/delete/void operations on all audited models; assert exactly one AuditLog per operation; assert `user_id` non-null; assert `created_at` within 1 second; assert `before_values` non-null for update/delete, `after_values` non-null for create/update

- [ ] 9. Operations Module — Trips
  - [ ] 9.1 Implement TripController
    - Create `app/Http/Controllers/Operations/TripController.php` with `index`, `store`, `show`, `update`, `void`
    - `store`: validate van is `active` (return 422 if in maintenance); compute `tax_amount` and `total_amount` from tenant's `tax_rate`; call `LedgerService::recordRevenue()`; check customer credit limit and attach `credit_warning` to response if exceeded (but save the trip)
    - `void`: set `status = 'voided'`; call `LedgerService::reverseEntry()` on linked LedgerEntry
    - `index`: paginated, filterable by `van_id`, `customer_id`, date range
    - Create `StoreTripRequest.php` and `UpdateTripRequest.php`
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [ ] 9.2 Implement frontend Trips page
    - Create `frontend/app/(app)/operations/trips/page.tsx` — `DataTable` with trip list, filters (van, customer, date range), "Add Trip" button opening `FormModal`
    - Show credit warning banner when API returns `credit_warning`
    - Show pre-tax and post-tax fare when tax rate > 0
    - Create `frontend/components/modules/operations/TripForm.tsx` (van select from active only, customer select, date picker, origin/destination, fare input)
    - _Requirements: 8.1, 8.4, 8.5, 22.4_

- [ ] 10. Operations Module — Expenses
  - [ ] 10.1 Implement ExpenseController
    - Create `app/Http/Controllers/Operations/ExpenseController.php` with `index`, `store`, `show`, `update`, `destroy`
    - `store`: validate COA category is active; call `LedgerService::recordExpense()`
    - `destroy`: soft-delete (set `status = 'deleted'`); call `LedgerService::reverseEntry()`
    - `index`: paginated, filterable by `van_id`, `chart_of_account_id`, date range
    - Create `StoreExpenseRequest.php`
    - _Requirements: 9.1, 9.2, 9.3, 9.4_

  - [ ] 10.2 Implement frontend Expenses page
    - Create `frontend/app/(app)/operations/expenses/page.tsx` — `DataTable` with expense list, category filter, van filter, date range filter, "Add Expense" button
    - Create `frontend/components/modules/operations/ExpenseForm.tsx`
    - _Requirements: 9.1, 9.4_

- [ ] 11. Operations Module — Driver Advances & Wages
  - [ ] 11.1 Implement DriverAdvanceController and WagePayoutController
    - `DriverAdvanceController`: `index`, `store`, `destroy`
      - `store`: call `LedgerService::recordAdvance()`; set `van_id` from driver's current assignment
    - `WagePayoutController`: `index`, `preview`, `store`
      - `preview`: call `WageCalculationService::compute()` without persisting; return result
      - `store`: run `WageCalculationService::compute()`; if `is_negative = TRUE`, require `confirmed_by` in request body (return 422 if absent); persist and call `LedgerService::recordWage()`
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

  - [ ] 11.2 Implement frontend Advances and Wages pages
    - Create `frontend/app/(app)/operations/advances/page.tsx` — driver advance list with "Add Advance" button
    - Create `frontend/app/(app)/operations/wages/page.tsx` — pay period selector, driver selector, gross wage input, "Preview Net Wage" button showing live calculation result, `ConfirmDialog` for negative net wage scenarios
    - _Requirements: 10.1, 10.4, 10.5, 10.6_

- [ ] 12. Checkpoint — Core Operations
  - Ensure all backend routes for Auth, Fleet, and Operations return correct responses; run `php artisan test --filter=Auth --filter=Fleet --filter=Operations`; ask the user if questions arise.

- [ ] 13. CRM Module — Customers
  - [ ] 13.1 Implement CustomerController
    - Create `app/Http/Controllers/CRM/CustomerController.php` with `index`, `store`, `show`, `update`, `destroy`
    - `show`: compute `outstanding_balance = SUM(trips.total_amount) - SUM(payments_received)` from ledger
    - `index`: searchable by `company_name`, filterable by outstanding balance; paginated
    - `destroy`: soft-delete; retain all historical records
    - Create `StoreCustomerRequest.php`
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_

  - [ ] 13.2 Implement frontend Customers pages
    - Create `frontend/app/(app)/crm/customers/page.tsx` — searchable/filterable customer directory
    - Create `frontend/app/(app)/crm/customers/[id]/page.tsx` — customer profile with outstanding balance badge, invoice history table, edit form
    - Create `frontend/components/modules/crm/CustomerForm.tsx`
    - _Requirements: 11.1, 11.2, 11.3, 11.4_

- [ ] 14. CRM Module — Drivers
  - [ ] 14.1 Implement DriverController
    - Create `app/Http/Controllers/CRM/DriverController.php` with `index`, `store`, `show`, `update`, `destroy`
    - `show`: include `current_assignment`, `assignment_history`, `advances`, `wage_payouts` in response
    - `index`: searchable by `full_name` and `licence_number`
    - Role guard: Driver role may only access their own `show` endpoint
    - _Requirements: 12.1, 12.3, 12.4_

  - [ ] 14.2 Add licence expiry alert to DashboardController
    - Query `drivers` where `licence_expiry_date BETWEEN today AND today+30` for the tenant; include `licence_expiry_alerts` array in KPI response
    - _Requirements: 12.2_

  - [ ] 14.3 Implement frontend Drivers pages
    - Create `frontend/app/(app)/crm/drivers/page.tsx` — searchable driver directory
    - Create `frontend/app/(app)/crm/drivers/[id]/page.tsx` — driver profile: assigned van, assignment history, advance history, wage payout history, licence expiry alert banner if within 30 days
    - Create `frontend/components/modules/crm/DriverForm.tsx`
    - _Requirements: 12.1, 12.2, 12.3, 12.4_

- [ ] 15. CRM Module — Investors
  - [ ] 15.1 Implement InvestorController
    - Create `app/Http/Controllers/CRM/InvestorController.php` with `index`, `store`, `show`, `update`, `injections.store`, `distributions.store`
    - `injections.store`: record capital injection; call `LedgerService::recordCapitalInjection()`
    - `distributions.store`: record distribution; call `LedgerService::recordDistribution()`; create LedgerEntry reducing Equity account
    - `show`: include running ledger (injections + distributions, sorted by date, with running balance)
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_

  - [ ] 15.2 Implement frontend Investors page
    - Create `frontend/app/(app)/crm/investors/page.tsx` — investor directory with capital totals
    - Create investor detail drawer/modal showing running ledger table (date, description, injection, distribution, balance)
    - Create `frontend/components/modules/crm/InvestorForm.tsx`
    - _Requirements: 13.1, 13.2, 13.3, 13.4_

- [ ] 16. Ledger Module — Chart of Accounts
  - [ ] 16.1 Implement ChartOfAccountController
    - Create `app/Http/Controllers/Ledger/ChartOfAccountController.php` with `index`, `store`, `update`
    - `update`: allow rename and `is_active` toggle; when deactivating, return 422 if any pending (non-reversed) LedgerEntries reference the account with future dates; block deletion of `is_system = TRUE` accounts
    - `store`: validate name uniqueness within tenant; set `is_system = FALSE`
    - _Requirements: 14.1, 14.3, 14.4_

  - [ ] 16.2 Implement frontend Chart of Accounts page
    - Create `frontend/app/(app)/ledger/chart-of-accounts/page.tsx` — table of accounts with type badge (Asset/Liability/Equity/Revenue/Expense), active/inactive toggle, "Add Account" button
    - _Requirements: 14.1, 14.3, 14.4_

- [ ] 17. Ledger Module — Van, Customer & Investor Ledgers
  - [ ] 17.1 Implement FinancialStatementService — ledger aggregation
    - Create `app/Services/FinancialStatementService.php` with:
      - `vanLedger(int $vanId, Carbon $from, Carbon $to)`: aggregate revenue, variable expenses, and prorated fixed costs per design formula; return per-van summary + transaction rows
      - `customerLedger(int $customerId, Carbon $from, Carbon $to)`: total trips booked, invoiced, payments received, outstanding balance, individual rows with running balance
      - `investorLedger(int $investorId, Carbon $from, Carbon $to)`: total injections, distributions, net retained, individual rows with running balance
    - _Requirements: 15.1, 15.4, 16.1, 16.3, 17.1, 17.3_

  - [ ] 17.2 Implement VanLedgerController, CustomerLedgerController, InvestorLedgerController
    - `VanLedgerController@index`: call `FinancialStatementService::vanLedger()`; support CSV export via `ExportButton`
    - `CustomerLedgerController@index`: call `FinancialStatementService::customerLedger()`; support CSV + PDF export
    - `InvestorLedgerController@index`: call `FinancialStatementService::investorLedger()`
    - _Requirements: 15.2, 15.3, 16.2, 16.4, 17.2_

  - [ ] 17.3 Implement frontend Van Ledger, Customer Ledger, Investor Ledger pages
    - `frontend/app/(app)/ledger/van-ledger/page.tsx` — van selector, date range, sortable summary table + detail rows, CSV export button
    - `frontend/app/(app)/ledger/customer-ledger/page.tsx` — customer selector, date range, transaction table with running balance, CSV + PDF export
    - `frontend/app/(app)/ledger/investor-ledger/page.tsx` — investor selector, date range, transaction table with running balance
    - _Requirements: 15.1, 15.2, 15.3, 16.1, 16.2, 16.3, 16.4, 17.1, 17.2, 17.3_

- [ ] 18. Financial Statements — Trial Balance, P&L, Balance Sheet
  - [ ] 18.1 Implement FinancialStatementService — reports
    - Extend `FinancialStatementService` with:
      - `trialBalance(Carbon $from, Carbon $to)`: per COA: total_debit, total_credit, net_balance; grand totals; `balanced = (total_debit == total_credit)`
      - `profitLoss(Carbon $from, Carbon $to)`: gross revenue (SUM credit where type=revenue), total expenses grouped by COA (SUM debit where type=expense), net P/L
      - `balanceSheet(Carbon $asOf)`: total assets, liabilities, equity (including cumulative retained earnings); `reconciled = (assets == liabilities + equity)`
    - _Requirements: 18.1, 19.1, 20.1_

  - [ ] 18.2 Implement TrialBalanceController, ProfitLossController, BalanceSheetController
    - Each controller wraps the matching `FinancialStatementService` method and pipes through `ExportButton` (CSV + PDF via dompdf)
    - Return zero values with informational note when no transactions exist in range
    - Return discrepancy alert payload when Trial Balance is unbalanced or Balance Sheet is irreconcilable
    - _Requirements: 18.2, 18.3, 18.4, 19.2, 19.3, 19.4, 20.2, 20.3, 20.4_

  - [ ] 18.3 Implement frontend Trial Balance, P&L, Balance Sheet pages
    - `frontend/app/(app)/ledger/trial-balance/page.tsx` — date range picker, COA table with debit/credit/net columns, grand totals row; highlight discrepancy row in red when unbalanced, CSV + PDF export
    - `frontend/app/(app)/ledger/profit-loss/page.tsx` — date range picker, revenue section, expenses by category section, net P/L summary, informational empty state, CSV + PDF export
    - `frontend/app/(app)/ledger/balance-sheet/page.tsx` — as-of date picker, assets/liabilities/equity sections, reconciliation confirmation badge (green) or error alert (red), CSV + PDF export
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 19.1, 19.2, 19.3, 19.4, 20.1, 20.2, 20.3, 20.4_

  - [ ]* 18.4 Write property test for Double-Entry Ledger Balance Invariant (Property 10)
    - **Property 10: Double-Entry Ledger Balance Invariant**
    - **Validates: Requirements 18.1, 18.2, 19.1, 20.1, 20.2**
    - Generate random sequences of trips, expenses, wage payouts, capital injections, and distributions; after each sequence, run trial balance and assert `total_debit == total_credit`; run balance sheet and assert `assets == liabilities + equity`

- [ ] 19. Dashboard Module
  - [ ] 19.1 Implement DashboardController
    - Create `app/Http/Controllers/Dashboard/DashboardController.php` with `kpis` and `revenueExpenses` actions
    - `kpis`: for selected date range (default: current month): active fleet count, total revenue (SUM trip fares), fleet operating expenses (SUM expense amounts), net profit margin; include maintenance alerts and licence expiry alerts
    - `revenueExpenses`: per-van revenue and expenses array for bar chart
    - Return zero values + `no_data = true` flag when no records exist for the period
    - _Requirements: 4.1, 4.2, 4.4, 4.5, 6.5, 12.2_

  - [ ] 19.2 Implement frontend Dashboard page
    - Create `frontend/app/(app)/dashboard/page.tsx` — 4 `KpiCard` components (Active Fleet, Daily Revenue, Expenses, Net Margin), `DateRangePicker`, maintenance alert banner, licence expiry alert banner
    - Create `frontend/components/charts/RevenueExpenseBarChart.tsx` — Recharts or Shadcn chart bar chart, revenue vs expenses per van; re-fetches when date range changes
    - Show "No data available for the selected period." when `no_data = true`
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [ ] 20. System Settings
  - [ ] 20.1 Implement TenantSettingController
    - Create `app/Http/Controllers/Settings/TenantSettingController.php` with `show` and `update`
    - `update`: validate `currency_code` is valid ISO 4217 (3 chars), `tax_rate` is 0–100 with ≤ 2 decimal places; persist to `tenants` table; changes apply to new records only
    - _Requirements: 22.1, 22.2, 22.3, 22.5_

  - [ ] 20.2 Implement frontend Settings pages
    - Create `frontend/app/(app)/settings/users/page.tsx` — paginated user list (name, email, role, status), "Add User" button, role dropdown, deactivate toggle with self-deactivation guard message
    - Create `frontend/app/(app)/settings/tenant/page.tsx` — currency code + symbol fields, tax rate field (0–100, 2dp), save button; note stating updates apply to new records only
    - _Requirements: 21.1, 21.3, 21.4, 21.5, 22.1, 22.2, 22.3, 22.5_

- [ ] 21. Audit Trail
  - [ ] 21.1 Implement AuditLogController and frontend audit page
    - Create `app/Http/Controllers/AuditLogController.php` with `index` — paginated, filterable by `auditable_type`, `action`, `user_id`, and date range; accessible to Admin and Accountant roles only
    - Create `frontend/app/(app)/settings/audit-log/page.tsx` — filterable table showing: timestamp, user, action, model type, model ID, before/after value diff viewer (collapsible JSON)
    - _Requirements: 23.1, 23.2_

- [ ] 22. Tenant Isolation Property Tests
  - [ ]* 22.1 Write property test for Tenant Isolation — Record Ownership (Property 1)
    - **Property 1: Tenant Isolation — Record Ownership**
    - **Validates: Requirements 1.1, 1.2**
    - Generate random core entity records (Van, Driver, Customer, Trip, Expense, LedgerEntry, DriverAdvance, WagePayout, Investor) via their respective store endpoints authenticated as a user of a given tenant; assert every created record's `tenant_id` equals the authenticated user's `tenant_id`

  - [ ]* 22.2 Write property test for Cross-Tenant Access Rejection (Property 2)
    - **Property 2: Cross-Tenant Access Rejection**
    - **Validates: Requirements 1.3**
    - Generate pairs of tenants (A ≠ B); for each resource type and HTTP method, authenticate as tenant A and attempt to access/modify records belonging to tenant B; assert HTTP 403 for every combination

  - [ ]* 22.3 Write property test for Default Chart of Accounts Provisioning (Property 3)
    - **Property 3: Default Chart of Accounts Provisioning**
    - **Validates: Requirements 1.4, 14.2**
    - Generate N random new tenant registrations; for each, assert exactly 13 default COA categories are created; assert every category's `tenant_id` matches the new tenant; assert all 13 required category names are present

- [ ] 23. Checkpoint — Integration & Full-Stack Wiring
  - Wire all frontend API calls to their backend routes; verify end-to-end flows for: login → dashboard → create trip → view van ledger → view trial balance; ensure all `LoadingSpinner` and `ErrorBanner` components render correctly; run `php artisan test` and `cd frontend && npx vitest --run`; ask the user if questions arise.

- [ ] 24. Responsive UI & Accessibility Polish
  - [ ] 24.1 Apply responsive layout and WCAG 2.1 AA compliance
    - Audit all pages for viewport widths from 320px to 2560px using Tailwind responsive classes; ensure tables scroll horizontally on mobile
    - Audit colour contrast for all text and interactive elements to meet WCAG 2.1 AA (minimum 4.5:1 for normal text, 3:1 for large text)
    - Add `aria-label`, `role`, and keyboard `tabIndex` attributes to all interactive elements (sidebar items, table headers, form controls, modals)
    - _Requirements: 24.1, 24.2, 24.3_

  - [ ] 24.2 Implement global loading and error states
    - Confirm `LoadingSpinner` renders on all data-fetching components during in-flight requests
    - Confirm `ErrorBanner` with retry action renders on all API error responses
    - _Requirements: 24.4, 24.5_

- [ ] 25. Final Checkpoint — All Tests Pass
  - Run `php artisan test` (all PHPUnit unit, feature, and property tests); run `cd frontend && npx vitest --run` (all Vitest component tests); resolve any failures; ask the user if questions arise.

---

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP delivery
- Each task references specific requirements for traceability
- Checkpoints (tasks 12, 23, 25) are integration gates — do not skip them
- Property tests use `eris/eris` on the backend with a minimum of 100 generated inputs per property
- All monetary calculations use `DECIMAL(12,2)` — never float arithmetic
- The `TenantScope` global scope must be applied before any other query in every controller; never bypass it
- Currency symbol is read from `tenant.currency_symbol` at render time via `formatters.ts`
- Soft-deletes are used throughout — never hard-delete financial records

---

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.2"] },
    { "id": 1, "tasks": ["1.3", "1.4"] },
    { "id": 2, "tasks": ["1.5", "1.6"] },
    { "id": 3, "tasks": ["2.1", "2.2", "3.1", "4.1", "4.2", "4.3"] },
    { "id": 4, "tasks": ["2.3", "2.4", "2.5", "2.6", "2.7", "3.2"] },
    { "id": 5, "tasks": ["5.1", "6.1", "7.1", "8.1", "8.2", "8.3"] },
    { "id": 6, "tasks": ["5.2", "6.2", "6.3", "7.2", "8.4", "8.5", "8.6", "8.7"] },
    { "id": 7, "tasks": ["9.1", "10.1", "11.1", "13.1", "14.1", "14.2", "15.1", "16.1"] },
    { "id": 8, "tasks": ["9.2", "10.2", "11.2", "13.2", "14.3", "15.2", "16.2"] },
    { "id": 9, "tasks": ["17.1", "18.1", "19.1"] },
    { "id": 10, "tasks": ["17.2", "18.2", "19.2", "20.1"] },
    { "id": 11, "tasks": ["17.3", "18.3", "18.4", "20.2"] },
    { "id": 12, "tasks": ["21.1", "22.1", "22.2", "22.3"] },
    { "id": 13, "tasks": ["24.1", "24.2"] }
  ]
}
```
