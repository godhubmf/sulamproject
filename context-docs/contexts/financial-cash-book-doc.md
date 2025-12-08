# Financial - Cash Book (Buku Tunai)

This document explains the Cash Book page and all related files so a junior developer can safely read, modify, or extend the feature. It describes imports, variables, logic flow, templates, repositories, CSS, and where to change things.

**Paths**
- **Page (route):** `features/shared/lib/routes.php` (route: `/financial/cash-book`)
- **Page controller entry:** `features/financial/admin/pages/cash-book.php`
- **View (HTML/PHP):** `features/financial/admin/views/cash-book.php`
- **Controller (business logic):** `features/financial/admin/controllers/FinancialController.php`
- **Repositories (data layer):**
  - `features/financial/shared/lib/DepositAccountRepository.php`
  - `features/financial/shared/lib/PaymentAccountRepository.php`
  - `features/financial/shared/lib/FinancialSettingsRepository.php` (used by controller)
- **Database bootstrap used by page:** `features/shared/lib/database/mysqli-db.php`
- **Auth/session helpers:** `features/shared/lib/auth/session.php`
- **Utilities (helper functions):** `features/shared/lib/utilities/functions.php`
- **Page CSS:** `features/financial/admin/assets/css/financial.css`
- **Layout components:** `features/shared/components/layouts/app-layout.php` and `features/shared/components/layouts/base.php`

**Goal of the page**
- Display a traditional cash book (buku tunai): chronological list of transactions combining deposits (IN) and payments (OUT) with running balances for cash and bank.
- Show opening balances for the selected fiscal year and calculate running balances per transaction.

**How the route is wired**
- The route `/financial/cash-book` is defined in `features/shared/lib/routes.php` and includes the page file: `features/financial/admin/pages/cash-book.php`.

**`features/financial/admin/pages/cash-book.php` — what it does (step-by-step)**
- Includes required shared helpers:
  - `initSecureSession()`, `requireAuth()`, `requireAdmin()` from `features/shared/lib/auth/session.php`.
  - Common helpers from `features/shared/lib/utilities/functions.php` (helpers like `url()` and `e()`).
  - Database connection (exposes `$mysqli`) from `features/shared/lib/database/mysqli-db.php`.
- Requires the controller file: `FinancialController.php` and instantiates it with the `$mysqli` connection.
- Calls `$controller->cashBook()` to get data for the page. That method returns an array with keys used by the view (see below).
- Prepares a `$pageHeader` array used by the layout.
- Adds page-specific CSS by referencing `features/financial/admin/assets/css/financial.css`.
- Buffers the page content by including the view: `features/financial/admin/views/cash-book.php`.
- Wraps the content in the app layout and then includes the base layout to render the full page.

Files to inspect when editing the page entry/flow:
- `features/financial/admin/pages/cash-book.php` (entry and includes) — change includes, session behavior, or add pre-processing here.
- `features/shared/components/layouts/app-layout.php` and `base.php` — change top-level layout or page wrappers.

**`FinancialController::cashBook()` — business logic explained**
- Signature: `public function cashBook(?int $fiscalYear = null): array`
- Defaults `$fiscalYear` to current year if not provided.
- Loads opening balances for the fiscal year via `FinancialSettingsRepository::getByFiscalYear()`:
  - `opening_cash_balance` and `opening_bank_balance` are read and used as starting balances.
- Fetches combined transactions via `getCashBookData($fiscalYear)`.
- Initializes running balances: `$tunaiBalance` (cash) and `$bankBalance` with opening balances.
- Iterates through transactions in chronological order (oldest -> newest) and:
  - Converts `amount` to float and examines `type` (`'IN'` or `'OUT'`) and `payment_method` (`'cash'` or other meaning bank).
  - Updates cash or bank running balances accordingly (add for IN, subtract for OUT).
  - Appends `tunai_balance` and `bank_balance` to each transaction row for display.
- Returns an array with keys:
  - `title`, `transactions`, `tunaiBalance`, `bankBalance`, `openingCash`, `openingBank`, `fiscalYear`, `hasSettings`.

**`FinancialController::getCashBookData()` — SQL and combination logic**
- Purpose: combine deposit (IN) and payment (OUT) tables into a single chronological list.
- Uses constants defined on repository classes to build SUM expressions for the category columns:
  - `DepositAccountRepository::CATEGORY_COLUMNS`
  - `PaymentAccountRepository::CATEGORY_COLUMNS`
- Builds two SELECTs:
  - SELECT from `financial_deposit_accounts` (receipt_number as `ref_no`, sum of deposit categories as `amount`, `'IN'` as type)
  - SELECT from `financial_payment_accounts` (voucher_number as `ref_no`, sum of payment categories as `amount`, `'OUT'` as type)
- Optionally filters by `YEAR(tx_date) = $fiscalYear` if a fiscal year is supplied.
- Executes union query `UNION ALL` and orders by `tx_date ASC, id ASC` to get chronological order.
- Returns rows as associative arrays.

Important notes about SQL and security:
- The method injects `$fiscalYear` directly into the SQL string (e.g. `WHERE YEAR(tx_date) = $fiscalYear`). Because `$fiscalYear` is an integer derived from current year or validated input, risk is low, but if you later accept arbitrary strings you should switch to prepared statements or cast to `(int)` before embedding.
- The repository classes use prepared statements for inserts/updates.

**Repositories — where categories are defined and how they affect the page**
- `DepositAccountRepository::CATEGORY_COLUMNS` and `::CATEGORY_LABELS`
  - Define column names for deposit categories (e.g., `geran_kerajaan`, `sumbangan_derma`, ...).
  - The controller sums these columns to compute each deposit row `amount`.
  - If you add/remove categories, update this list — the controller and SQL generation will automatically reflect changes.
- `PaymentAccountRepository::CATEGORY_COLUMNS` and `::CATEGORY_LABELS`
  - Define payment categories (e.g., `perayaan_islam`, `penyelenggaraan_masjid`, ...).
  - Same rules apply: add/remove here as the single source of truth for category columns.
- When adding columns to the DB schema, update the repository lists and migration SQL files under `database/migrations/`.

**`features/financial/admin/views/cash-book.php` — UI template explained**
- Expected variables (documented at top of file): `$transactions`, `$tunaiBalance`, `$bankBalance`, `$openingCash`, `$openingBank`, `$fiscalYear`, `$hasSettings`.
- Opening Balance Alert
  - If `$hasSettings` is false, displays a warning with a link to `url('financial/settings')` so the treasurer can set opening balances.
- Stat Cards (summary above the table)
  - Shows three stat cards: Cash Balance, Bank Balance, Total Balance.
  - Values are formatted with `number_format(..., 2)`.
  - These are styled by `stat-cards.css` (imported by `financial.css`).
- Cash Book Table structure
  - Table element: `<table class="table table-hover table--cash-book" id="cashBookTable">`
  - The table has grouped headers (Date/Ref/Description, Tunai In/Out, Bank In/Out, Balances, Actions).
  - An opening balance row with date `01/01/{fiscalYear}` is rendered with the opening balances.
  - If `$transactions` is empty, a placeholder row is shown.
  - For each transaction `$tx`:
    - `$isCash = $tx['payment_method'] === 'cash'`
    - `$isIn = $tx['type'] === 'IN'`
    - The code calculates `cashIn`, `cashOut`, `bankIn`, `bankOut` and places the amounts in the appropriate column.
    - Columns show `-` if not applicable.
    - Balance columns use the pre-computed `$tx['tunai_balance']` and `$tx['bank_balance']` computed in the controller.
    - Actions column prints a `Print Receipt` (IN) or `Print Voucher` (OUT) button that opens `financial/receipt-print?id={id}` or `financial/voucher-print?id={id}` in a new tab.
  - Table footer (`<tfoot>`) shows the current balances (cash, bank, total).

Practical editing notes for view changes
- To add or change a column label, edit the header in `views/cash-book.php`.
- To change date format, modify `date('d/m/Y', strtotime($tx['tx_date']))` in the view, or centralize formatting using `formatDate()` in `features/shared/lib/utilities/functions.php`.
- To add client-side features (sorting/pagination), add a JS file and include it via the page entry (`pages/cash-book.php`) or the layout; no dedicated JS currently exists for this table.

**Styling: `features/financial/admin/assets/css/financial.css`**
- Imports:
  - `features/shared/assets/css/bento-grid.css` (layout grid utilities)
  - `features/shared/assets/css/stat-cards.css` (stat card styles)
- Key classes:
  - `.table--cash-book`: base style for the cash book table (min-width, font-size, paddings, color utility overrides)
  - `.sticky-col-left`: utility for sticky left columns if needed
- Where to change styles:
  - For table-specific styling, edit `features/financial/admin/assets/css/financial.css`.
  - For shared stat card changes, edit `features/shared/assets/css/stat-cards.css` (referenced by import).
  - If making global layout changes, edit `features/shared/assets/css/bento-grid.css`.

**Authentication & Session helpers**
- `features/shared/lib/auth/session.php` provides:
  - `initSecureSession()` — secure session init and periodic session id regeneration.
  - `requireAuth()`, `requireAdmin()` — used to restrict access to authenticated admin users.
  - `isAdmin()` uses `$_SESSION['role']` or `$_SESSION['roles']` to determine role.
- If users report being blocked, check session handling and ensure `$_SESSION['role']` is set on login.

**Database connection**
- `features/shared/lib/database/mysqli-db.php` exposes `$mysqli`.
- Uses environment variables `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`. Default `DB_NAME` is `masjidkamek`.
- For local development (Laragon), verify DB credentials in this file or via environment.

**Utilities used by page**
- `url($path)` — builds a path relative to `APP_BASE_PATH` if defined; used to build internal links and refer to assets.
- `e()` — shorthand for HTML escaping; views sometimes use `htmlspecialchars()` inline, but prefer `e()` to standardize escaping.

**How to change behavior / common tasks**
- Add a new deposit/payment category: 
  1. Add a new column to the relevant table via a migration under `database/migrations/`.
  2. Add the column name to `DepositAccountRepository::CATEGORY_COLUMNS` or `PaymentAccountRepository::CATEGORY_COLUMNS`.
  3. Add a display label to `::CATEGORY_LABELS`.
  4. Update any forms under `features/financial/admin/pages/*-add.php` and `*-edit.php` to include input fields for the new category.
  5. Update views that display category breakdowns (if any).

- Edit opening balances:
  - The opening balances are managed by `FinancialSettingsRepository` and saved via the Financial Settings page. The page link is `url('financial/settings')` from the cash book view. To change how opening balances are applied, modify `FinancialController::cashBook()` and/or `FinancialSettingsRepository`.

- Improve SQL safety or performance:
  - The union query is simple but may scan large tables. Add proper indexes on `tx_date` and consider limiting columns fetched if needed.
  - If you make `$fiscalYear` user-supplied, make sure to cast to `(int)` before interpolating, or convert to prepared statements.

**Related files you should open when working on Cash Book**
- `features/financial/admin/pages/cash-book.php` (page entry point)
- `features/financial/admin/views/cash-book.php` (template)
- `features/financial/admin/controllers/FinancialController.php` (logic)
- `features/financial/shared/lib/DepositAccountRepository.php` (deposit categories)
- `features/financial/shared/lib/PaymentAccountRepository.php` (payment categories)
- `features/financial/shared/lib/FinancialSettingsRepository.php` (opening balances; used by controller)
- `features/financial/admin/assets/css/financial.css` (styles)
- `features/shared/lib/database/mysqli-db.php` (DB connection)
- `features/shared/lib/auth/session.php` (auth & session helpers)
- `features/shared/components/layouts/app-layout.php` and `base.php` (layouts)

**Quick checklist for a change (safe workflow)**
- Update repository column arrays before changing DB schema.
- Add DB migration under `database/migrations/` for any schema change.
- Test locally with Laragon (`http://localhost/sulamproject/`) and confirm `register.php` provisioning if needed.
- Check `storage/logs/` for `error.log` or `debug.log` when debugging.
- Run through the page as an Admin user (login as admin) to confirm access checks.

---

If you'd like, I can:
- Generate a separate doc for the Payment/Deposit forms and pages (they are related and sizable), or
- Create a short developer checklist page for how to add a new category (with code snippets and a migration template).

File created: `context-docs/contexts/financial-cash-book-doc.md`
