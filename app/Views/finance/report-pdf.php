<?php 
// PDF Print View - Finance Report
$currency = $settings['currency_symbol'] ?? '$';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Finance Report - <?= e($start_date) ?> to <?= e($end_date) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header h2 { font-size: 18px; font-weight: normal; color: #666; }
        .summary-cards { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .summary-card { flex: 1; padding: 15px; margin: 0 10px; text-align: center; color: white; }
        .summary-card:first-child { margin-left: 0; }
        .summary-card:last-child { margin-right: 0; }
        .card-income { background-color: #28a745; }
        .card-expenses { background-color: #dc3545; }
        .card-balance { background-color: #007bff; }
        .card-balance.negative { background-color: #dc3545; }
        .summary-card h3 { font-size: 14px; margin-bottom: 5px; }
        .summary-card p { font-size: 20px; font-weight: bold; }
        .section { margin-bottom: 25px; }
        .section h3 { font-size: 16px; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .total-row { background-color: #f8f9fa; font-weight: bold; }
        .income { color: #28a745; }
        .expense { color: #dc3545; }
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
        <h2>Finance Report - <?= e($start_date) ?> to <?= e($end_date) ?></h2>
    </div>

    <div class="summary-cards">
        <div class="summary-card card-income">
            <h3>Total Income</h3>
            <p><?= $currency . number_format($totalDonations ?? 0, 2) ?></p>
        </div>
        <div class="summary-card card-expenses">
            <h3>Total Expenses</h3>
            <p><?= $currency . number_format($totalExpenses ?? 0, 2) ?></p>
        </div>
        <div class="summary-card card-balance <?= ($netBalance ?? 0) < 0 ? 'negative' : '' ?>">
            <h3>Net Balance</h3>
            <p><?= $currency . number_format($netBalance ?? 0, 2) ?></p>
        </div>
    </div>

    <div class="section">
        <h3>Monthly Comparison</h3>
        <?php if (empty($donationsMonthly) && empty($expensesMonthly)): ?>
        <p>No data available for the selected period.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Income</th>
                    <th class="text-right">Expenses</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Combine months from both donations and expenses
                $months = [];
                foreach ($donationsMonthly as $d) {
                    $months[$d->month] = ['donations' => $d->total, 'expenses' => 0];
                }
                foreach ($expensesMonthly as $e) {
                    if (isset($months[$e->month])) {
                        $months[$e->month]['expenses'] = $e->total;
                    } else {
                        $months[$e->month] = ['donations' => 0, 'expenses' => $e->total];
                    }
                }
                ksort($months);
                ?>
                <?php foreach ($months as $month => $data): ?>
                <tr>
                    <td><?= date('F Y', strtotime($month . '-01')) ?></td>
                    <td class="text-right income"><?= $currency . number_format($data['donations'], 2) ?></td>
                    <td class="text-right expense"><?= $currency . number_format($data['expenses'], 2) ?></td>
                    <td class="text-right <?= ($data['donations'] - $data['expenses']) >= 0 ? 'income' : 'expense' ?>">
                        <?= $currency . number_format($data['donations'] - $data['expenses'], 2) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>Total</td>
                    <td class="text-right income"><?= $currency . number_format($totalDonations ?? 0, 2) ?></td>
                    <td class="text-right expense"><?= $currency . number_format($totalExpenses ?? 0, 2) ?></td>
                    <td class="text-right <?= ($netBalance ?? 0) >= 0 ? 'income' : 'expense' ?>">
                        <?= $currency . number_format($netBalance ?? 0, 2) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>Generated on <?= date('F j, Y g:i A') ?></p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print / Save as PDF</button>
    </div>
</body>
</html>
