<?php
// Interventions — note-log entries flagged as an intervention (emergency contact, EMS, or referral)
// within a date range. Source: noteLogs JOIN recipients.
$start = date('Y-m-d', strtotime($_GET['startDate']));
$end   = date('Y-m-d', strtotime($_GET['endDate']));

$rows = $db->select(
    'SELECT n.dateStamp, n.note, n.emergContactCalled, n.emsCalled, n.referralMade,
            r.firstName, r.lastName
     FROM noteLogs n
     JOIN recipients r ON r.id = n.recipient_id
     WHERE (n.emergContactCalled = 1 OR n.emsCalled = 1 OR n.referralMade = 1)
       AND CAST(n.dateStamp AS DATE) >= ? AND CAST(n.dateStamp AS DATE) <= ?
     ORDER BY n.dateStamp DESC',
    [$start, $end]
);

echo '<div class="rptHead"><h2>Interventions</h2><div class="rptMeta">'
    . date('m/d/Y', strtotime($start)) . ' &ndash; ' . date('m/d/Y', strtotime($end))
    . ' &middot; Generated ' . date('m/d/Y g:i a') . '</div></div>';

if (!$rows) {
    echo '<div class="rptEmpty">No interventions recorded in this range.</div>';
} else {
    echo '<div class="rptCount">' . count($rows) . ' intervention(s)</div>';
    echo '<table class="rpt"><tr><th>Date</th><th>Recipient</th><th>Type</th><th>Note</th></tr>';
    foreach ($rows as $r) {
        $tags = [];
        if ($r['emergContactCalled'] == 1) $tags[] = '<span class="tag tag-amber">Emergency Contact</span>';
        if ($r['emsCalled'] == 1)          $tags[] = '<span class="tag tag-red">EMS</span>';
        if ($r['referralMade'] == 1)       $tags[] = '<span class="tag tag-grey">Referral</span>';
        echo '<tr>'
            . '<td>' . date('m/d/Y', strtotime($r['dateStamp'])) . '</td>'
            . '<td>' . htmlspecialchars($r['lastName'] . ', ' . $r['firstName']) . '</td>'
            . '<td>' . implode(' ', $tags) . '</td>'
            . '<td>' . nl2br(htmlspecialchars($r['note'])) . '</td>'
            . '</tr>';
    }
    echo '</table>';
}
