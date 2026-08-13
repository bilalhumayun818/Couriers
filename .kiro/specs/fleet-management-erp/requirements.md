# Requirements Document

## Introduction

CX_COURIER is a multi-tenant, web-based Fleet Management & Courier Operations ERP platform designed for logistics businesses. The system enables organisations to manage their entire fleet lifecycle — from vehicle registration and driver assignment through to daily trip logging, expense tracking, client invoicing, investor ledger management, and automated financial reporting (Profit & Loss, Balance Sheet, Trial Balance). Each tenant (logistics company) operates in a fully isolated data environment on a shared infrastructure. The platform is built on a Laravel REST API backend with MySQL, paired with a Next.js / React frontend using Tailwind CSS, Lucide Icons, and the Shadcn UI component library.

---

## Glossary

- **Tenant**: A logistics business that has registered on the CX_COURIER platform. All data is scoped to a tenant.
- **System**: The CX_COURIER application as a whole.
- **Auth_Service**: The authentication and authorisation subsystem responsible for user identity, roles, and permissions.
- **Fleet_Module**: The subsystem responsible for vehicle (van) registration, fixed costs configuration, and driver assignment.
- **Operations_Module**: The subsystem responsible for trip entry, daily expense logging, driver advances, and wage payouts.
- **CRM_Module**: The subsystem responsible for customer, driver, and investor/director profile management.
- **Ledger_Module**: The subsystem responsible for financial ledgers, chart of accounts, and financial statement generation.
- **Dashboard_Module**: The subsystem responsible for KPI metrics and visual summaries.
- **Van**: A registered vehicle (any type) belonging to a tenant fleet.
- **Driver**: A person assigned to operate a Van.
- **Trip**: A single courier or delivery job assigned to a Van and Customer.
- **Expense**: A financial outflow logged against a Van on a given date.
- **Customer**: A client company or individual that books courier services.
- **Investor**: A stakeholder who has injected capital into the tenant business.
- **Director**: A person with ownership and profit-sharing rights in the tenant business.
- **LedgerEntry**: A double-entry bookkeeping record linked to a Chart of Accounts category.
- **ChartOfAccount**: A named account category (e.g. Fuel, Maintenance, Lease, Insurance, Legal Cover, Revenue).
- **TrialBalance**: A report summarising all debit and credit balances across all ChartOfAccount entries.
- **ProfitLossReport**: A report showing total revenue minus total expenses for a selected period.
- **BalanceSheet**: A report showing Assets = Liabilities + Equity at a point in time.
- **Role**: A named permission set assigned to a user (Admin, Fleet Manager, Accountant, Driver).
- **Wage_Payout**: The net amount paid to a Driver after deducting advances from gross wages.

---

## Requirements

---

### Requirement 1: Multi-Tenancy & Tenant Isolation

**User Story:** As a logistics business owner, I want my company's data to be completely separate from other companies on the platform, so that sensitive financial and operational data is never visible to other tenants.

#### Acceptance Criteria

1. THE System SHALL associate every core data record (Vans, Drivers, Customers, Trips, Expenses, LedgerEntries, Investors) with a `tenant_id` foreign key.
2. WHEN a user authenticates, THE Auth_Service SHALL scope all subsequent data queries to the user's tenant.
3. IF a request attempts to access a record belonging to a different tenant, THEN THE System SHALL return an HTTP 403 Forbidden response.
4. WHEN a new tenant registers, THE System SHALL create an isolated tenant account with a unique `tenant_id` and provision default ChartOfAccount categories for that tenant.
5. THE System SHALL support a minimum of 100 concurrent tenants without cross-tenant data leakage.

---

### Requirement 2: User Authentication & Role-Based Access Control

**User Story:** As a system administrator, I want to manage user accounts with distinct role-based permissions, so that each user can only access functionality appropriate to their role.

#### Acceptance Criteria

1. THE Auth_Service SHALL support four roles: Admin, Fleet Manager, Accountant, and Driver.
2. WHEN a user submits valid credentials, THE Auth_Service SHALL issue a session token with an expiry of no more than 24 hours.
3. IF a user submits invalid credentials, THEN THE Auth_Service SHALL return an HTTP 401 Unauthorised response and SHALL NOT disclose whether the username or password was incorrect.
4. WHEN an authenticated user attempts an action, THE Auth_Service SHALL verify the user's role before granting or denying access according to the permissions table below:
   - Admin: full access to all modules and System Settings.
   - Fleet Manager: full access to Fleet_Module and Operations_Module; read access to Dashboard_Module and CRM_Module.
   - Accountant: full access to Ledger_Module; read access to all other modules.
   - Driver: read access to their own profile and assigned trip records only.
5. IF a user's session token has expired, THEN THE Auth_Service SHALL redirect the user to the login page.
6. THE Auth_Service SHALL hash all stored passwords using bcrypt with a minimum cost factor of 12.

---

### Requirement 3: Sidebar Navigation Layout

**User Story:** As a logged-in user, I want a persistent collapsible sidebar with clearly labelled navigation sections, so that I can move between modules quickly.

#### Acceptance Criteria

1. THE System SHALL render a sidebar containing the following top-level sections in order: Dashboard, Fleet Management, Operations & Daily Logs, Stakeholders & CRM, Financial Ledgers & Reports, System Settings.
2. WHEN a user clicks a collapsible group in the sidebar, THE System SHALL expand or collapse the group's child navigation items without a full page reload.
3. WHEN a user navigates to a page, THE System SHALL apply an active visual state to the corresponding sidebar item.
4. WHILE the viewport width is below 768 pixels, THE System SHALL collapse the sidebar to a hidden off-canvas drawer accessible via a toggle button.
5. THE System SHALL persist the sidebar collapsed/expanded state for the current session.

---

### Requirement 4: Dashboard & KPI Metrics

**User Story:** As a Fleet Manager or Admin, I want a dashboard showing key performance indicators and visual charts, so that I can assess fleet performance at a glance.

#### Acceptance Criteria

1. THE Dashboard_Module SHALL display the following KPI cards for the current tenant: Active Fleet Count, Total Daily Revenue (sum of today's Trip fares), Fleet Operating Expenses (sum of today's Expense records), and Net Profit Margin ((Daily Revenue − Daily Expenses) / Daily Revenue × 100, expressed as a percentage).
2. WHEN the dashboard page loads, THE Dashboard_Module SHALL retrieve and render the KPI data within 3 seconds under normal load conditions.
3. THE Dashboard_Module SHALL render a bar chart showing Revenue vs. Expenses per Van for the selected date range (defaulting to the current calendar month).
4. WHEN a user selects a different date range on the dashboard, THE Dashboard_Module SHALL refresh all KPI cards and charts to reflect the selected period.
5. IF no Trip or Expense records exist for the selected period, THEN THE Dashboard_Module SHALL display zero values and an informational message stating "No data available for the selected period."

---

### Requirement 5: Fleet Management — Vehicle (Van) CRUD

**User Story:** As a Fleet Manager, I want to register, update, and deactivate vehicles in the fleet, so that the system always reflects the current state of our physical assets.

#### Acceptance Criteria

1. THE Fleet_Module SHALL allow an authorised user to create a Van record with the following mandatory fields: Plate Number (unique within tenant), Make/Model, Year, and Status (one of: Active, Maintenance, Leased).
2. WHEN a Van is created, THE Fleet_Module SHALL store the record with a timestamp and the `tenant_id` of the creating user.
3. THE Fleet_Module SHALL allow an authorised user to update any Van field except `tenant_id`.
4. WHEN a Van's Status is set to "Maintenance", THE Fleet_Module SHALL prevent new Trip records from being assigned to that Van until the Status is changed to "Active".
5. THE Fleet_Module SHALL allow an authorised user to soft-delete a Van, retaining all historical Trip and Expense records linked to that Van.
6. THE Fleet_Module SHALL display all Vans in a paginated, searchable, and filterable table supporting filter by Status.

---

### Requirement 6: Fleet Management — Fixed Costs Configuration

**User Story:** As a Fleet Manager, I want to configure fixed monthly costs per van, so that the system can accurately calculate total operating costs in financial reports.

#### Acceptance Criteria

1. THE Fleet_Module SHALL allow an authorised user to record the following fixed cost fields per Van: Monthly Lease Amount, Road Tax (annual, prorated monthly), Insurance Premium (monthly), and next Service Schedule date.
2. WHEN a fixed cost record is saved, THE Fleet_Module SHALL associate it with the Van's `tenant_id`.
3. THE Fleet_Module SHALL allow fixed cost records to be updated when actual costs change.
4. WHEN generating a Van-Wise Ledger, THE Ledger_Module SHALL include the Van's monthly fixed costs (prorated for partial months) in the expense calculation.
5. WHEN the Service Schedule date for a Van is within 7 calendar days, THE System SHALL display a maintenance alert on the Dashboard_Module.

---

### Requirement 7: Fleet Management — Driver Assignment

**User Story:** As a Fleet Manager, I want to assign a primary driver to each van, so that accountability for trips and expenses is clearly tracked.

#### Acceptance Criteria

1. THE Fleet_Module SHALL allow an authorised user to assign exactly one primary Driver to a Van at any given time.
2. WHEN a Driver is assigned to a Van, THE Fleet_Module SHALL record the assignment start date.
3. IF a Driver is already assigned to another Van, THEN THE Fleet_Module SHALL display a confirmation prompt before reassigning, and SHALL record the previous assignment's end date.
4. THE Fleet_Module SHALL maintain a full history of Van-Driver assignment records with start and end dates.

---

### Requirement 8: Operations — Trip Entry

**User Story:** As an Operations user, I want to log trips with fare and customer details, so that revenue is accurately attributed to each van and customer.

#### Acceptance Criteria

1. THE Operations_Module SHALL provide a Trip Entry Form with the following mandatory fields: Van (select from Active Vans), Customer (select from tenant's Customer directory), Trip Date, Origin, Destination, and Fare Amount.
2. WHEN a Trip is saved, THE Operations_Module SHALL create a corresponding LedgerEntry in the Revenue ChartOfAccount category for the tenant.
3. THE Operations_Module SHALL allow an authorised user to edit or void a Trip record; voiding SHALL reverse the associated LedgerEntry.
4. THE Operations_Module SHALL display all Trips in a paginated table filterable by Van, Customer, and date range.
5. WHEN a Customer has exceeded their credit limit, THE Operations_Module SHALL display a warning but SHALL still allow the trip to be saved.

---

### Requirement 9: Operations — Daily Expense Entry

**User Story:** As an Operations user, I want to log daily expenses per van, so that operating costs are tracked against each vehicle.

#### Acceptance Criteria

1. THE Operations_Module SHALL provide a Daily Expense Entry Form with the following mandatory fields: Van, Expense Date, Expense Category (Fuel, Tolls, Spare Parts, Maintenance/Repairs — selected from ChartOfAccount categories), Amount, and an optional Description.
2. WHEN an Expense is saved, THE Operations_Module SHALL create a corresponding LedgerEntry in the appropriate ChartOfAccount category.
3. THE Operations_Module SHALL allow an authorised user to edit or delete an Expense record; deletion SHALL reverse the associated LedgerEntry.
4. THE Operations_Module SHALL display all Expenses in a paginated table filterable by Van, Category, and date range.

---

### Requirement 10: Operations — Driver Advances & Wage Payouts

**User Story:** As an Operations user, I want to record cash advances given to drivers and calculate their net wage payouts, so that driver compensation is accurately documented.

#### Acceptance Criteria

1. THE Operations_Module SHALL allow an authorised user to log a Driver Advance record with the fields: Driver, Advance Date, Amount, and Purpose (e.g. fuel advance, expense float).
2. WHEN a Driver Advance is saved, THE Operations_Module SHALL record it as an expense against the Driver's assigned Van.
3. THE Operations_Module SHALL allow an authorised user to record a Gross Wage amount for a Driver for a specified pay period.
4. WHEN a Wage_Payout is calculated, THE Operations_Module SHALL compute Net Wage = Gross Wage − sum of all Advances in the same pay period and display the result before saving.
5. IF the sum of Advances exceeds Gross Wage for a pay period, THEN THE Operations_Module SHALL display a warning indicating the Driver has a negative net payout and SHALL require Admin confirmation before saving.
6. THE Operations_Module SHALL display a Driver's advance and payout history in their profile within the CRM_Module.

---

### Requirement 11: Stakeholders & CRM — Customer Management

**User Story:** As a CRM user, I want to maintain a customer directory with billing and credit information, so that invoicing and account management are centralised.

#### Acceptance Criteria

1. THE CRM_Module SHALL allow an authorised user to create a Customer record with the following fields: Company Name, Contact Name, Billing Address, Phone, Email, and Credit Limit.
2. THE CRM_Module SHALL display each Customer's outstanding balance (total Trips invoiced − total payments received) on their profile page.
3. THE CRM_Module SHALL provide an invoice history view per Customer showing Trip dates, fare amounts, payment status, and invoice references.
4. THE CRM_Module SHALL support search and filter of the Customer directory by Company Name and outstanding balance.
5. THE CRM_Module SHALL allow soft-deletion of Customers, retaining all historical Trip and LedgerEntry records.

---

### Requirement 12: Stakeholders & CRM — Driver Profiles

**User Story:** As a Fleet Manager, I want to maintain detailed driver profiles, so that licensing, assignment, and payout information is always current.

#### Acceptance Criteria

1. THE CRM_Module SHALL allow an authorised user to create a Driver profile with the following fields: Full Name, National ID / Passport Number, Driver Licence Number, Licence Expiry Date, Contact Number, and Emergency Contact.
2. WHEN a Driver's Licence Expiry Date is within 30 calendar days, THE System SHALL display a licence expiry alert on the Dashboard_Module.
3. THE CRM_Module SHALL display the Driver's currently assigned Van, full assignment history, advance records, and Wage_Payout history on the Driver profile page.
4. THE CRM_Module SHALL support search of Driver profiles by name and licence number.

---

### Requirement 13: Stakeholders & CRM — Investor & Director Management

**User Story:** As an Admin or Accountant, I want to record investor and director profiles with capital contributions and bank account details, so that profit-sharing distributions are accurately tracked.

#### Acceptance Criteria

1. THE CRM_Module SHALL allow an authorised user to create an Investor/Director record with the following fields: Full Name, Role (Investor or Director), Bank Account Name, Bank Account Number, Bank Name, and Initial Capital Contribution.
2. THE CRM_Module SHALL allow an authorised user to record additional capital injections against an Investor/Director with a date and amount.
3. THE CRM_Module SHALL allow an authorised user to record profit-sharing distributions to an Investor/Director with a date and amount.
4. THE CRM_Module SHALL display a running ledger for each Investor/Director showing capital injections, distributions, and retained balance.
5. WHEN a profit-sharing distribution is recorded, THE Ledger_Module SHALL create a corresponding LedgerEntry reducing the Equity account.

---

### Requirement 14: Financial Ledgers — Chart of Accounts

**User Story:** As an Accountant, I want to manage a chart of accounts, so that all financial transactions are categorised consistently.

#### Acceptance Criteria

1. THE Ledger_Module SHALL provide a Chart of Accounts management interface where an authorised user can create, rename, and deactivate account categories.
2. THE System SHALL provision the following default ChartOfAccount categories for every new tenant: Revenue, Fuel, Tolls, Spare Parts, Maintenance/Repairs, Monthly Lease, Road Tax, Insurance, Driver Wages, Driver Advances, Legal Cover, Capital, Equity Distributions.
3. WHEN a ChartOfAccount category is deactivated, THE Ledger_Module SHALL prevent new LedgerEntries from being assigned to that category but SHALL retain all historical entries.
4. THE Ledger_Module SHALL support categorisation of accounts as Asset, Liability, Equity, Revenue, or Expense for Balance Sheet and P&L classification.

---

### Requirement 15: Financial Ledgers — Van-Wise Ledger

**User Story:** As an Accountant or Fleet Manager, I want to view a profit and loss summary per individual van, so that I can identify which vehicles are profitable.

#### Acceptance Criteria

1. THE Ledger_Module SHALL provide a Van-Wise Ledger view showing, per Van: Total Revenue (sum of Trip fares), Total Expenses (Fuel + Maintenance + Lease + Driver Wages + Tolls + Spare Parts), and Net Profit (Total Revenue − Total Expenses).
2. THE Van-Wise Ledger SHALL be filterable by Van, date range, and Expense Category.
3. THE Van-Wise Ledger SHALL be displayed in a paginated, sortable table and SHALL support export to CSV.
4. WHEN a date range filter is applied, THE Ledger_Module SHALL prorate fixed monthly costs (Lease, Insurance, Road Tax) to the selected period.

---

### Requirement 16: Financial Ledgers — Customer Ledger

**User Story:** As an Accountant, I want to view a customer account statement showing bookings, payments, and outstanding balances, so that I can manage receivables effectively.

#### Acceptance Criteria

1. THE Ledger_Module SHALL provide a Customer Ledger view showing per Customer: Total Trips booked, total fare invoiced, total payments received, and outstanding balance.
2. THE Customer Ledger SHALL be filterable by Customer and date range.
3. THE Customer Ledger SHALL display individual transaction rows (Trip date, description, debit, credit, running balance).
4. THE Customer Ledger SHALL support export to CSV and PDF.

---

### Requirement 17: Financial Ledgers — Investor Ledger

**User Story:** As an Admin, I want an investor ledger showing capital injections, distributions, and retained earnings per investor, so that equity positions are transparent.

#### Acceptance Criteria

1. THE Ledger_Module SHALL provide an Investor Ledger view showing per Investor/Director: total capital contributed, total distributions paid, and net retained balance.
2. THE Investor Ledger SHALL be filterable by Investor and date range.
3. THE Investor Ledger SHALL display individual transaction rows (date, description, injection amount, distribution amount, running balance).

---

### Requirement 18: Financial Statements — Trial Balance

**User Story:** As an Accountant, I want an automated Trial Balance report, so that I can verify that total debits equal total credits across all accounts.

#### Acceptance Criteria

1. THE Ledger_Module SHALL generate a Trial Balance report showing each ChartOfAccount category with its total debit balance, total credit balance, and net balance for a user-selected period.
2. WHEN the Trial Balance is generated, THE Ledger_Module SHALL display a totals row confirming whether total debits equal total credits.
3. IF total debits do not equal total credits, THEN THE Ledger_Module SHALL highlight the discrepancy in red and display an alert message.
4. THE Trial Balance SHALL support export to CSV and PDF.

---

### Requirement 19: Financial Statements — Profit & Loss Report

**User Story:** As an Admin or Accountant, I want an automated Profit & Loss report, so that I can assess business profitability over any period.

#### Acceptance Criteria

1. THE Ledger_Module SHALL generate a Profit & Loss Report for a user-selected date range showing: Gross Revenue (sum of all Revenue LedgerEntries), Total Operating Expenses (sum of all Expense LedgerEntries grouped by category), and Net Profit/Loss (Gross Revenue − Total Operating Expenses).
2. THE Profit & Loss Report SHALL group expense line items by ChartOfAccount category.
3. THE Profit & Loss Report SHALL support export to CSV and PDF.
4. WHEN the selected period contains no transactions, THE Ledger_Module SHALL render the report with zero values and an informational note.

---

### Requirement 20: Financial Statements — Balance Sheet

**User Story:** As an Admin or Accountant, I want an automated Balance Sheet, so that I can view the company's financial position at any point in time.

#### Acceptance Criteria

1. THE Ledger_Module SHALL generate a Balance Sheet as of a user-selected date showing: Total Assets (sum of Asset-classified ChartOfAccount balances), Total Liabilities (sum of Liability-classified balances), and Total Equity (sum of Equity-classified balances including retained earnings).
2. WHEN the Balance Sheet is generated, THE Ledger_Module SHALL verify that Total Assets = Total Liabilities + Total Equity and SHALL display a reconciliation confirmation message.
3. IF Total Assets do not equal Total Liabilities + Total Equity, THEN THE Ledger_Module SHALL highlight the discrepancy and display a reconciliation error alert.
4. THE Balance Sheet SHALL support export to CSV and PDF.

---

### Requirement 21: System Settings — User & Role Management

**User Story:** As an Admin, I want to manage user accounts and role assignments within my tenant, so that access is appropriately controlled as the team changes.

#### Acceptance Criteria

1. THE Auth_Service SHALL allow an Admin to create, update, and deactivate user accounts within the same tenant.
2. WHEN a user account is deactivated, THE Auth_Service SHALL immediately invalidate any active session tokens for that user.
3. THE Auth_Service SHALL allow an Admin to assign or change a user's Role.
4. IF an Admin attempts to deactivate their own account, THEN THE Auth_Service SHALL reject the operation and display an error message stating "You cannot deactivate your own account."
5. THE Auth_Service SHALL display a paginated list of all users within the tenant with their name, email, role, and account status.

---

### Requirement 22: System Settings — Currency & Tax Defaults

**User Story:** As an Admin, I want to configure default currency and tax settings for my tenant, so that all financial figures are displayed and calculated correctly.

#### Acceptance Criteria

1. THE System SHALL allow an Admin to set the tenant's default currency (ISO 4217 currency code) and a display currency symbol.
2. WHEN a currency is set, THE System SHALL apply the selected currency symbol to all monetary display fields across all modules for that tenant.
3. THE System SHALL allow an Admin to configure a default VAT/Tax percentage rate (0–100, supporting up to 2 decimal places).
4. WHEN a tax rate is configured and a Trip fare is entered, THE Operations_Module SHALL display both the pre-tax and post-tax fare amounts.
5. THE System SHALL allow the currency and tax settings to be updated at any time; updates SHALL apply to new records only and SHALL NOT retroactively alter historical LedgerEntries.

---

### Requirement 23: Data Integrity & Audit Trail

**User Story:** As an Admin, I want every financial record change to be logged with a timestamp and the user who made it, so that the system provides a full audit trail.

#### Acceptance Criteria

1. THE System SHALL log every create, update, and delete action on Trip, Expense, LedgerEntry, Wage_Payout, and Driver Advance records with the acting user's ID, the timestamp, and the before/after field values.
2. THE Ledger_Module SHALL make the audit log accessible to Admins and Accountants via a filterable audit trail view.
3. WHEN a LedgerEntry is reversed (due to Trip void or Expense deletion), THE System SHALL create a new offsetting LedgerEntry rather than deleting the original.
4. THE System SHALL retain all audit log entries for a minimum of 7 years.

---

### Requirement 24: Responsive UI & Accessibility

**User Story:** As a user accessing CX_COURIER from any device, I want the interface to be responsive and accessible, so that I can work effectively from desktop, tablet, or mobile.

#### Acceptance Criteria

1. THE System SHALL render all pages correctly on viewport widths from 320px to 2560px.
2. THE System SHALL meet WCAG 2.1 Level AA colour contrast requirements for all text and interactive elements.
3. THE System SHALL provide keyboard navigation support for all interactive elements including sidebar navigation, tables, and forms.
4. WHILE a data fetch is in progress, THE System SHALL display a loading indicator on the affected component.
5. IF a network request fails, THEN THE System SHALL display a user-readable error message and offer a retry action.
