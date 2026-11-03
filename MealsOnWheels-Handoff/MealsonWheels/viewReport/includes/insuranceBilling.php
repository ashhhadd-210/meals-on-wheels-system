<?php
$startDate = new DateTime($_GET['startDate']);
$endDate = new DateTime($_GET['endDate']);

if ($_GET['recipType'] == 'all') {
    $typeName = 'All';
    $recipients = $db->select('SELECT * FROM recipients ORDER BY lastName ASC, firstName ASC');
} else {
    $type = $db->select('SELECT * FROM clientStatus WHERE id = ?', [$_GET['recipType']])[0];
    $typeName = $type['name'];
    $recipients = $db->select('SELECT * FROM recipients WHERE clientStatus_id = ? ORDER BY lastName ASC, firstName ASC', [$type['id']]);
}
$recipientList = [];
foreach ($recipients as $recip) {
    $currentBilling = $db->select('SELECT * FROM billings WHERE billType_id = ? AND recipient_id = ? AND (startDate <= ? AND endDate >= ?) ORDER BY startDate DESC', [2, $recip['id'], $endDate->format('Y-m-d'), $startDate->format('Y-m-d')])[0];
    if ($currentBilling != false) {

        $x = [
            'name' => $recip['lastName'] . ', ' . $recip['firstName'],
            'dob' => date('m/d/y',strtotime($recip['dob'])),
            'billing' => $currentBilling
        ];
        $recipientList[] = $x;
    }
}

?>

<h3>Insurance Billing</h3>
<table class="billingTable">
    <tr>
        <td colspan="6"><?= $typeName ?></td>
        <td colspan="2"><?php echo $startDate->format('m/d/y') . ' - ' . $endDate->format('m/d/y') ?></td>
        <td colspan="2">Created <?= date('m/d/y - g:i a') ?></td>
    </tr>
    <tr>
        <th>Recipient</th>
        <th>DOB</th>
        <th>Sub ID</th>
        <th>Authorization #</th>
        <th>Issued Date</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Freq</th>
        <th>Units</th>
        <th>Meal Count</th>
    </tr>
    <?php
    foreach ($recipientList as $re) {
        echo '<tr>';
        echo '<td>' . $re['name'] . '</td>';
        echo '<td>' . $re['dob'] . '</td>';
        echo '<td>' . $re['billing']['subscriber_id'] . '</td>';
        echo '<td>' . $re['billing']['authorizationNumber'] . '</td>';
        echo '<td>' . date('m/d/y', strtotime($re['billing']['issueDate'])) . '</td>';
        echo '<td>' . date('m/d/y', strtotime($re['billing']['startDate'])) . '</td>';
        echo '<td>' . date('m/d/y', strtotime($re['billing']['endDate'])) . '</td>';
        echo '<td>' . $re['billing']['frequency'] . '</td>';
        echo '<td>' . $re['billing']['unitedApproved'] . '</td>';

        $interval = new DateInterval('P1D');
        $period = new DatePeriod($startDate, $interval, $endDate);
        $mealTotal = 0;
        foreach ($period as $date) {
            $ledger = $db->select('SELECT * FROM mealLedgers WHERE dateStamp = ?', [$date->format('Y-m-d')])[0];
            $weekday = (int)$date->format('N');
            if ($ledger == false) {
                // $exp_mealMatrix = explode(',',$re['mealMatrix']);
                // $curMatrixDay = explode('-',$exp_mealMatrix[$weekday]);
                // $mealTotal += $curMatrixDay[1];
                // $mealTotal += $curMatrixDay[2];
            } else {
                $ledgerList = explode(',', $ledger['recipMatrix']);
                foreach ($ledgerList as $relist) {
                    $listItem = explode('-', $relist);
                    if ((int)$recip['id'] == $listItem[0]) {
                        $mealTotal += $listItem[1];
                        $mealTotal += $listItem[2];
                    }
                }
            }
        }
        echo '<td>' . $mealTotal . '</td>';

        echo '</tr>';
    }
    ?>
</table>
<pre>
    <?php
    // print_r($recipientList);
    ?>
</pre>