# Kontra (Contra) Feature - Implementation Documentation

## Overview
The **Kontra** feature enables internal cash/bank transfers within the financial system. When money is moved between cash and bank accounts, the system automatically creates paired transactions to maintain accurate records.

## Purpose
- **What**: Internal transfer category for moving money between cash ↔ bank
- **Why**: Track internal movements without affecting financial statements
- **Where**: Appears in Deposit Account, Payment Account, and Cash Book (but NOT in Financial Statement)

---

## How It Works

### Creating a Kontra Transaction

#### Scenario 1: Cash → Bank Transfer (RM 500)
**User Action**: Create a Deposit with Kontra = RM 500, Payment Method = Bank

**System Response**:
1. Creates **Deposit** record:
   - Amount: RM 500 in Kontra category
   - Payment Method: `bank`
   - Description: (User-provided)
   
2. Auto-creates **Payment** record:
   - Amount: RM 500 in Kontra category
   - Payment Method: `cash`
   - Description: `(Contra) Transfer to Bank - Ref: Deposit #123`
   - Paid To: `Internal Transfer`

3. Links both records via `contra_pair_id`

#### Scenario 2: Bank → Cash Transfer (RM 300)
**User Action**: Create a Payment with Kontra = RM 300, Payment Method = Cash

**System Response**:
1. Creates **Payment** record:
   - Amount: RM 300 in Kontra category
   - Payment Method: `cash`
   - Description: (User-provided)
   
2. Auto-creates **Deposit** record:
   - Amount: RM 300 in Kontra category
   - Payment Method: `bank`
   - Description: `(Contra) Transfer to Cash - Ref: Payment #456`
   - Received From: `Internal Transfer`

3. Links both records via `contra_pair_id`

---

## Database Schema Changes

### New Columns Added

#### `financial_deposit_accounts`
```sql
kontra DECIMAL(12,2) UNSIGNED DEFAULT 0.00
contra_pair_id INT UNSIGNED NULL
is_contra_transaction TINYINT(1) NOT NULL DEFAULT 0
```

#### `financial_payment_accounts`
```sql
kontra DECIMAL(12,2) UNSIGNED DEFAULT 0.00
contra_pair_id INT UNSIGNED NULL
is_contra_transaction TINYINT(1) NOT NULL DEFAULT 0
```

### Field Descriptions
- **`kontra`**: Amount for internal transfer
- **`contra_pair_id`**: ID of the paired transaction in the opposite table
- **`is_contra_transaction`**: Flag to quickly identify contra entries (1 = yes, 0 = no)

---

## Code Changes Summary

### 1. Repository Updates
**Files Modified**:
- `features/financial/shared/lib/DepositAccountRepository.php`
- `features/financial/shared/lib/PaymentAccountRepository.php`

**Changes**:
- Added `'kontra'` to `CATEGORY_COLUMNS` array
- Added `'kontra' => 'Kontra'` to `CATEGORY_LABELS` array
- Added methods:
  - `updateContraPair($id, $contraPairId)` - Links paired transactions
  - `hasKontraAmount($data)` - Checks if transaction has kontra amount
  - `getContraPairId($id)` - Retrieves paired transaction ID

### 2. Controller Updates
**File Modified**: `features/financial/admin/controllers/FinancialController.php`

**Enhanced Methods**:

#### `storeDeposit()`
- Detects kontra amount
- Auto-creates paired payment transaction
- Links both via `contra_pair_id`

#### `storePayment()`
- Detects kontra amount
- Auto-creates paired deposit transaction
- Links both via `contra_pair_id`

#### `deleteDeposit()`
- Checks for contra pair
- Deletes both deposit AND paired payment (hard coupling)

#### `deletePayment()`
- Checks for contra pair
- Deletes both payment AND paired deposit (hard coupling)

### 3. Financial Statement Exclusion
**File Modified**: `features/financial/shared/lib/FinancialStatementController.php`

**Changes**:
- Modified `getReceiptsByCategory()` - Skips 'kontra' category
- Modified `getPaymentsByCategory()` - Skips 'kontra' category

**Result**: Kontra transactions do NOT appear in Financial Statement reports

---

## User Interface

### Forms
The Kontra field automatically appears in:
- **Add Deposit** form (`/financial/deposit-account/add`)
- **Edit Deposit** form (`/financial/deposit-account/edit`)
- **Add Payment** form (`/financial/payment-account/add`)
- **Edit Payment** form (`/financial/payment-account/edit`)

The field is rendered like other category fields (numeric input, min=0, step=0.01)

### Listing Pages
Kontra column appears in:
- **Deposit Account** listing (`/financial/deposit-account`)
- **Payment Account** listing (`/financial/payment-account`)
- **Cash Book** (`/financial/cash-book`)

---

## Hard Coupling Behavior

### On Create
- Creating a deposit/payment with kontra automatically creates the paired transaction
- Both transactions are linked via `contra_pair_id`

### On Edit
- ⚠️ **Current Implementation**: Edit only affects the edited record
- 🔄 **Future Enhancement**: Could sync changes to paired transaction

### On Delete
- Deleting either side automatically deletes BOTH transactions
- This ensures data integrity (no orphaned contra entries)

---

## Validation Rules

### Existing Validation (Still Applies)
- At least one category must have amount > 0
- Date, Description, Received From/Paid To are required
- Payment Method is required

### Kontra-Specific
- Kontra amount must be ≥ 0
- If kontra > 0, system auto-creates paired transaction
- Paired transactions use opposite payment methods:
  - Deposit (bank) → Payment (cash)
  - Deposit (cash) → Payment (bank)
  - Payment (cash) → Deposit (bank)
  - Payment (bank) → Deposit (cash)

---

## Migration File
**File**: `database/migrations/019_add_kontra_category_and_pairing.sql`

**To Run**:
```bash
php database/run_migration_013.php
```

---

## Testing Checklist

### Basic Functionality
- [ ] Create deposit with kontra amount → Verify paired payment is created
- [ ] Create payment with kontra amount → Verify paired deposit is created
- [ ] Verify contra pair IDs are correctly set
- [ ] Verify `is_contra_transaction` flag is set to 1

### Display
- [ ] Kontra column appears in Deposit Account listing
- [ ] Kontra column appears in Payment Account listing
- [ ] Kontra column appears in Cash Book
- [ ] Kontra amounts display correctly (RM format)

### Financial Statement
- [ ] Create transactions with kontra
- [ ] Generate financial statement
- [ ] Verify kontra does NOT appear in receipts section
- [ ] Verify kontra does NOT appear in payments section

### Hard Coupling
- [ ] Delete a deposit with contra pair → Verify payment is also deleted
- [ ] Delete a payment with contra pair → Verify deposit is also deleted
- [ ] Delete a regular deposit (no pair) → Verify only deposit is deleted

### Cash Book Balance
- [ ] Create cash→bank transfer
- [ ] Verify cash balance decreases
- [ ] Verify bank balance increases
- [ ] Verify total balance remains the same

---

## Important Notes

1. **Internal Transfers Only**: Kontra is for moving money between your own accounts, not external transactions

2. **No Double-Counting**: Because kontra is excluded from Financial Statement, it won't inflate income/expenses

3. **Balance Neutral**: Cash Book shows both sides, so you can track the movement, but total balance remains unchanged

4. **Auto-Generated Descriptions**: Paired transactions have system-generated descriptions like:
   - `(Contra) Transfer to Bank - Ref: Deposit #123`
   - `(Contra) Transfer to Cash - Ref: Payment #456`

5. **Payment Reference**: Auto-generated as `CONTRA-YmdHis` (e.g., `CONTRA-20251215134500`)

---

## Future Enhancements (Optional)

1. **Edit Synchronization**: Update paired transaction when editing
2. **Bulk Transfers**: UI for creating multiple transfers at once
3. **Transfer History**: Dedicated page showing only contra transactions
4. **Reconciliation Report**: Compare contra pairs for audit purposes

---

## Files Modified/Created

### Created
- `database/migrations/019_add_kontra_category_and_pairing.sql`
- `database/run_migration_013.php`

### Modified
- `features/financial/shared/lib/DepositAccountRepository.php`
- `features/financial/shared/lib/PaymentAccountRepository.php`
- `features/financial/admin/controllers/FinancialController.php`
- `features/financial/shared/lib/FinancialStatementController.php`

---

**Implementation Date**: 2025-12-15  
**Status**: ✅ Complete  
**Migration**: 019_add_kontra_category_and_pairing.sql
