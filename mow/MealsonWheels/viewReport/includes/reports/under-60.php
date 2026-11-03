<?php
// Under 60 — active recipients whose age (computed from dob) is under 60 as of the selected date.
// Age computed in PHP to avoid SQL date-arithmetic edge cases.
try {
    $asOf = new DateTime(!empty($_GET['asOf']) ? $_GET['asOf'] : 'now');
} catch (Exception $e) {
    $asOf = new DateTime('now');
}

$all = $db->select('SELECT firstName, lastName, dob FROM recipients WHERE clientStatus_id = 1 AND dob IS NOT NULL ORDER BY lastName ASC, firstName ASC');
$rows = [];
foreach ($all as $r) {
    try {
        $age = (new DateTime($r['dob']))->diff($asOf)->y;
    } catch (Exception $e) {
        continue;
    }
    if ($age < 60) {
        $r['age'] = $age;
        $rows[] = $r;
    }
}

echo '<div class="rptHead"><h2>Under 60</h2><div class="rptMeta">Active recipients under age 60 as of '
    . $asOf->format('m/d/Y') . ' &middot; Generated ' . date('m/d/Y g:i a') . '</div></div>';

if (!$rows) {
    echo '<div class="rptEmpty">No active recipients under 60 as of this date.</div>';
} else {
    echo '<div class="rptCount">' . count($rows) . ' recipient(s) under 60</div>';
    echo '<table class="rpt"><tr><th>Recipient</th><th>Date of Birth</th><th class="num">Age</th></tr>';
    foreach ($rows as $r) {
        echo '<tr>'
            . '<td>' . htmlspecialchars($r['lastName'] . ', ' . $r['firstName']) . '</td>'
            . '<td>' . date('m/d/Y', strtotime($r['dob'])) . '</td>'
            . '<td class="num">' . $r['age'] . '</td>'
            . '</tr>';
    }
    echo '</table>';
}
