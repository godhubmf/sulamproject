<?php
/**
 * Deposit Add/Edit Form View
 * Variables expected: $record (null for add), $categoryColumns, $categoryLabels, $errors, $old
 */

$isEdit = !empty($record);
$formData = $isEdit ? $record : ($old ?? []);
?>
<div class="card page-card">
    <div class="card-header">
        <h3><?php echo $isEdit ? 'Edit Deposit Record' : 'Add New Deposit Record'; ?></h3>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="padding: 1rem; margin-bottom: 1rem; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <!-- Receipt Number -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="receipt_number">No. Resit (Receipt Number)</label>
                <input type="text" id="receipt_number" name="receipt_number" class="form-control" 
                       placeholder="e.g. RR/2025/001"
                       value="<?php echo htmlspecialchars($formData['receipt_number'] ?? ''); ?>">
                <small class="form-text text-muted">Optional. Leave blank to auto-generate later.</small>
            </div>

            <!-- Date -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="tx_date">Tarikh (Date) <span style="color: red;">*</span></label>
                <input type="date" id="tx_date" name="tx_date" class="form-control" required 
                       value="<?php echo htmlspecialchars($formData['tx_date'] ?? date('Y-m-d')); ?>">
            </div>

            <!-- Received From -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="received_from">Diterima Dari (Received From) <span style="color: red;">*</span></label>
                <input type="text" id="received_from" name="received_from" class="form-control" required 
                       placeholder="e.g. Ahmad bin Ali"
                       value="<?php echo htmlspecialchars($formData['received_from'] ?? ''); ?>">
            </div>

            <!-- Description -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="description">Butiran (Description) <span style="color: red;">*</span></label>
                <input type="text" id="description" name="description" class="form-control" required 
                       placeholder="e.g. Kutipan Jumaat"
                       value="<?php echo htmlspecialchars($formData['description'] ?? ''); ?>">
            </div>

            <!-- Payment Method -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="payment_method">Kaedah Pembayaran (Payment Method) <span style="color: red;">*</span></label>
                <select id="payment_method" name="payment_method" class="form-control" required>
                    <option value="">-- Select Method --</option>
                    <option value="cash" <?php echo ($formData['payment_method'] ?? '') === 'cash' ? 'selected' : ''; ?>>Tunai (Cash)</option>
                    <option value="bank" <?php echo ($formData['payment_method'] ?? '') === 'bank' ? 'selected' : ''; ?>>Bank Transfer</option>
                </select>
            </div>

            <!-- Payment Reference -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="payment_reference">No. Transaksi (Transaction Number)</label>
                <input type="text" id="payment_reference" name="payment_reference" class="form-control" 
                       placeholder="e.g. TRX123456"
                       value="<?php echo htmlspecialchars($formData['payment_reference'] ?? ''); ?>">
                <small class="form-text text-muted">Required if payment method is Bank.</small>
            </div>

            <!-- Category Amounts -->
            <h4 style="margin-top: 1.5rem; margin-bottom: 1rem;">Category Amounts (RM)</h4>
            <p class="text-muted" style="margin-bottom: 1rem; font-size: 0.9rem;">Enter the amount in the appropriate category. At least one category must have a value greater than 0.</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                <?php foreach ($categoryColumns as $col): ?>
                <div class="form-group">
                    <label for="<?php echo $col; ?>"><?php echo htmlspecialchars($categoryLabels[$col]); ?></label>
                    <input type="number" id="<?php echo $col; ?>" name="<?php echo $col; ?>" 
                           class="form-control" step="0.01" min="0" placeholder="0.00"
                           value="<?php echo htmlspecialchars($formData[$col] ?? ''); ?>">
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Buttons -->
            <div class="form-actions" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update Record' : 'Save Record'; ?>
                </button>
                <a href="<?php echo url('financial/deposit-account'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
