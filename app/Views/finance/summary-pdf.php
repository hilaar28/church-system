<?php 
// PDF Print View - Monthly Summary
$currency = $settings['currency_symbol'] ?? '$';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Monthly Financial Summary - <?= e($monthName) ?> <?= e($year) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header h2 { font-size: 18px; font-weight: normal; color: #666; }
        .section { margin-bottom: 25px; }
        .section h3 { font-size: 16px; margin-bottom: 10px; padding: 5px 10px; color: white; }
        .income-header { background-color: #28a745; }
        .expense-header { background-color: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .total-row { background-color: #f8f9fa; font-weight: bold; }
        .balance { padding: 15px; text-align: center; font-size: 18px; font-weight: bold; color: white; }
        .balance-positive { background-color: #007bff; }
        .balance-negative { background-color: #dc3545; }
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
        <h2>Monthly Financial Summary - <?= e($monthName) ?> <?= e($year) ?></h2>
    </div>

    <div class="section">
        <h3 class="income-header">Income</h3>
        <table>
            <tr>
                <td>Tithes</td>
                <td class="text-right"><?= $currency . number_format($donations->tithes ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Offerings</td>
                <td class="text-right"><?= $currency . number_format($donations->offerings ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Donations</td>
                <td class="text-right"><?= $currency . number_format($donations->donations ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Special Offerings</td>
                <td class="text-right"><?= $currency . number_format($donations->special_offerings ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Building Fund</td>
                <td class="text-right"><?= $currency . number_format($donations->building_fund ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Mission</td>
                <td class="text-right"><?= $currency . number_format($donations->mission ?? 0, 2) ?></td>
            </tr>
            <tr class="total-row">
                <td>Total Income</td>
                <td class="text-right"><?= $currency . number_format($donationsTotal ?? 0, 2) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3 class="expense-header">Expenses</h3>
        <table>
            <tr>
                <td>Pastor Expenses</td>
                <td class="text-right"><?= $currency . number_format($expenses->pastor_expenses ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Rentals</td>
                <td class="text-right"><?= $currency . number_format($expenses->rentals ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Rates</td>
                <td class="text-right"><?= $currency . number_format($expenses->rates ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Improvements</td>
                <td class="text-right"><?= $currency . number_format($expenses->improvements ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Levies</td>
                <td class="text-right"><?= $currency . number_format($expenses->levies ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Utilities</td>
                <td class="text-right"><?= $currency . number_format($expenses->utilities ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Supplies</td>
                <td class="text-right"><?= $currency . number_format($expenses->supplies ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Other</td>
                <td class="text-right"><?= $currency . number_format($expenses->other ?? 0, 2) ?></td>
            </tr>
            <tr class="total-row">
                <td>Total Expenses</td>
                <td class="text-right"><?= $currency . number_format($expensesTotal ?? 0, 2) ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="balance <?= ($balance ?? 0) >= 0 ? 'balance-positive' : 'balance-negative' ?>">
            Net Balance: <?= $currency . number_format($balance ?? 0, 2) ?>
        </div>
    </div>

    <div class="footer">
        <p>Generated on <?= date('F j, Y g:i A') ?></p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print / Save as PDF</button>
    </div>
</body>
</html>
