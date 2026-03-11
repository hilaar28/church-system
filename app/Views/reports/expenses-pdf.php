<?php 
// PDF Print View - Expenses Report
$currency = $settings['currency_symbol'] ?? '$';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Expenses Report - <?= e($start_date) ?> to <?= e($end_date) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header h2 { font-size: 18px; font-weight: normal; color: #666; }
        .total-box { background-color: #dc3545; color: white; padding: 20px; text-align: center; margin-bottom: 30px; }
        .total-box h3 { font-size: 14px; margin-bottom: 5px; }
        .total-box p { font-size: 28px; font-weight: bold; }
        .section { margin-bottom: 25px; }
        .section h3 { font-size: 16px; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .pending-header { background-color: #dc3545; color: white; padding: 10px; margin-top: 20px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= SITE_NAME ?? 'Church Management System' ?></h1>
        <h2>Expenses Report - <?= e($start_date) ?> to <?= e($end_date) ?></h2>
    </div>

    <div class="total-box">
        <h3>Total Expenses</h3>
        <p><?= $currency . number_format($total ?? 0, 2) ?></p>
    </div>

    <div class="section">
        <h3>Expenses by Category</h3>
        <?php if (empty($byType)): ?>
        <p>No expense records found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Count</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($byType as $item): ?>
                <tr>
                    <td><?= ucfirst(str_replace('_', ' ', e($item->expense_type))) ?></td>
                    <td class="text-right"><?= number_format($item->count) ?></td>
                    <td class="text-right"><?= $currency . number_format($item->total, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>Monthly Expenses</h3>
        <?php if (empty($monthly)): ?>
        <p>No expense records found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($monthly as $item): ?>
                <tr>
                    <td><?= date('F Y', strtotime($item->month . '-01')) ?></td>
                    <td class="text-right"><?= $currency . number_format($item->total, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <?php if (!empty($pending)): ?>
    <div class="section">
        <div class="pending-header">
            <h3 style="margin:0;">Pending Approvals (<?= count($pending) ?>)</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending as $expense): ?>
                <tr>
                    <td><?= date('Y-m-d', strtotime($expense->expense_date)) ?></td>
                    <td><?= e($expense->description) ?></td>
                    <td class="text-right"><?= $currency . number_format($expense->amount, 2) ?></td>
                    <td><?= e($expense->category_name ?? 'N/A') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Generated on <?= date('F j, Y g:i A') ?></p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print / Save as PDF</button>
    </div>
</body>
</html>
