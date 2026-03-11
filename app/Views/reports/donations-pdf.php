<?php 
// PDF Print View - Donations Report
$currency = $settings['currency_symbol'] ?? '$';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Donations Report - <?= e($start_date) ?> to <?= e($end_date) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header h2 { font-size: 18px; font-weight: normal; color: #666; }
        .total-box { background-color: #28a745; color: white; padding: 20px; text-align: center; margin-bottom: 30px; }
        .total-box h3 { font-size: 14px; margin-bottom: 5px; }
        .total-box p { font-size: 28px; font-weight: bold; }
        .section { margin-bottom: 25px; }
        .section h3 { font-size: 16px; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
        .text-right { text-align: right; }
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
        <h2>Donations Report - <?= e($start_date) ?> to <?= e($end_date) ?></h2>
    </div>

    <div class="total-box">
        <h3>Total Donations</h3>
        <p><?= $currency . number_format($total ?? 0, 2) ?></p>
    </div>

    <div class="section">
        <h3>Donations by Type</h3>
        <?php if (empty($byType)): ?>
        <p>No donation records found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th class="text-right">Count</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($byType as $item): ?>
                <tr>
                    <td><?= ucfirst(e($item->donation_type)) ?></td>
                    <td class="text-right"><?= number_format($item->count) ?></td>
                    <td class="text-right"><?= $currency . number_format($item->total, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>Monthly Donations</h3>
        <?php if (empty($monthly)): ?>
        <p>No donation records found.</p>
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

    <div class="section">
        <h3>Top Donors</h3>
        <?php if (empty($topDonors)): ?>
        <p>No donation records found.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Member</th>
                    <th class="text-right">Total Donated</th>
                    <th class="text-right">Donations Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topDonors as $donor): ?>
                <tr>
                    <td><?= e($donor->first_name . ' ' . $donor->last_name) ?></td>
                    <td class="text-right"><?= $currency . number_format($donor->total, 2) ?></td>
                    <td class="text-right"><?= number_format($donor->count) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
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
