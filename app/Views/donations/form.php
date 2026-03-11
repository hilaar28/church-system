<?php 
$page_title = ($page_title ?? 'Add Donation') . ' - ' . SITE_NAME;
$auth = new Auth();
$isEdit = isset($donation) && !empty($donation['id']);
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $page_title ?></h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="http://localhost/church-system/public/donations/save">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $donation['id'] ?>">
            <?php endif; ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="member_id" class="form-label">Member</label>
                    <select class="form-select" id="member_id" name="member_id">
                        <option value="">Select Member (Optional)</option>
                        <?php if (isset($members)): ?>
                            <?php foreach ($members as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= ($donation['member_id'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label">Amount *</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" 
                           value="<?= old('amount', $donation['amount'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="donation_type" class="form-label">Donation Type *</label>
                    <select class="form-select" id="donation_type" name="donation_type" required>
                        <option value="">Select Type</option>
                        <option value="tithe" <?= ($donation['donation_type'] ?? '') == 'tithe' ? 'selected' : '' ?>>Tithe</option>
                        <option value="offering" <?= ($donation['donation_type'] ?? 'offering') == 'offering' ? 'selected' : '' ?>>Offering</option>
                        <option value="donation" <?= ($donation['donation_type'] ?? '') == 'donation' ? 'selected' : '' ?>>Donation</option>
                        <option value="special_offering" <?= ($donation['donation_type'] ?? '') == 'special_offering' ? 'selected' : '' ?>>Special Offering</option>
                        <option value="building_fund" <?= ($donation['donation_type'] ?? '') == 'building_fund' ? 'selected' : '' ?>>Building Fund</option>
                        <option value="mission" <?= ($donation['donation_type'] ?? '') == 'mission' ? 'selected' : '' ?>>Mission</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="campaign_id" class="form-label">Campaign</label>
                    <select class="form-select" id="campaign_id" name="campaign_id">
                        <option value="">Select Campaign (Optional)</option>
                        <?php if (isset($campaigns)): ?>
                            <?php foreach ($campaigns as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($donation['campaign_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="donation_date" class="form-label">Donation Date *</label>
                    <input type="date" class="form-control" id="donation_date" name="donation_date" 
                           value="<?= old('donation_date', $donation['donation_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="cash" <?= ($donation['payment_method'] ?? 'cash') == 'cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="check" <?= ($donation['payment_method'] ?? '') == 'check' ? 'selected' : '' ?>>Check</option>
                        <option value="card" <?= ($donation['payment_method'] ?? '') == 'card' ? 'selected' : '' ?>>Card</option>
                        <option value="bank_transfer" <?= ($donation['payment_method'] ?? '') == 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                        <option value="online" <?= ($donation['payment_method'] ?? '') == 'online' ? 'selected' : '' ?>>Online</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="check_number" class="form-label">Check Number</label>
                    <input type="text" class="form-control" id="check_number" name="check_number" 
                           value="<?= old('check_number', $donation['check_number'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="transaction_id" class="form-label">Transaction ID</label>
                    <input type="text" class="form-control" id="transaction_id" name="transaction_id" 
                           value="<?= old('transaction_id', $donation['transaction_id'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="receipt_number" class="form-label">Receipt Number</label>
                    <input type="text" class="form-control" id="receipt_number" name="receipt_number" 
                           value="<?= old('receipt_number', $donation['receipt_number'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" 
                               <?= ($donation['is_anonymous'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_anonymous">
                            Anonymous Donation
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $donation['notes'] ?? '') ?></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="http://localhost/church-system/public/donations" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Update Donation' : 'Add Donation' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
$scripts = '';
require APP_PATH . '/Views/layouts/main.php';
?>
