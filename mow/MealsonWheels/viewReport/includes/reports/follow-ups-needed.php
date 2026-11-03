<?php
// Follow Ups Needed — open follow-up note logs due on/before a date.
// Source: noteLogs (followUp=1, complete=0) JOIN recipients. Mirrors the dashboard "Follow Ups Due" widget.
$asOf = (!empty($_GET['asOf'])) ? date('Y-m-d', strtotime($_GET['asOf'])) : date('Y-m-d');

$rows = $db->select(
    'SELECT n.followUpDate, n.note, n.dateStamp, r.firstName, r.lastName
     FROM noteLogs n
     JOIN recipients r ON r.id = n.recipient_id
     WHERE n.followUp = 1 AND n.complete = 0 AND n.followUpDate <= ?
     ORDER BY n.followUpDate ASC',
    [$asOf]
);

echo '<div class="rptHead"><h2>Follow Ups Needed</h2><div class="rptMeta">Due on or before '
    . date('m/d/Y', strtotime($asOf)) . ' &middot; Generated ' . date('m/d/Y g:i a') . '</div></div>';

if (!$rows) {
    echo '<div class="rptEmpty">No open follow-ups are due as of this date.</div>';
} else {
    echo '<div class="rptCount">' . count($rows) . ' follow-up(s) due</div>';
    echo '<table class="rpt"><tr><th>Recipient</th><th>Follow-Up Date</th><th>Logged</th><th>Note</th></tr>';
    foreach ($rows as $r) {
        echo '<tr>'
            . '<td>' . htmlspecialchars($r['lastName'] . ', ' . $r['firstName']) . '</td>'
            . '<td>' . date('m/d/Y', strtotime($r['followUpDate'])) . '</td>'
            . '<td>' . date('m/d/Y', strtotime($r['dateStamp'])) . '</td>'
            . '<td>' . nl2br(htmlspecialchars($r['note'])) . '</td>'
            . '</tr>';
    }
    echo '</table>';
}
