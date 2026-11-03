<?php
// No Donation Report — active recipients with NO donation recorded within the date range.
// Source: recipients LEFT/NOT EXISTS donations.
$start = date('Y-m-d', strtotime($_GET['startDate']));
$end   = date('Y-m-d', strtotime($_GET['endDate']));

$rows = $db->select(
    'SELECT r.firstName, r.lastName, r.homePhone, r.cellPhone
     FROM recipients r
     WHERE r.clientStatus_id = 1
       AND NOT EXISTS (
           SELECT 1 FROM donations d
           WHERE d.recipient_id = r.id AND d.dateStamp >= ? AND d.dateStamp <= ?
       )
     ORDER BY r.lastName ASC, r.firstName ASC',
    [$start, $end]
);

echo '<div class="rptHead"><h2>No Donation Report</h2><div class="rptMeta">Active recipients with no donation '
    . date('m/d/Y', strtotime($start)) . ' &ndash; ' . date('m/d/Y', strtotime($end))
    . ' &middot; Generated ' . date('m/d/Y g:i a') . '</div></div>';

if (!$rows) {
    echo '<div class="rptEmpty">All active recipients have a donation in this range.</div>';
} else {
    echo '<div class="rptCount">' . count($rows) . ' recipient(s) with no donation</div>';
    echo '<table class="rpt"><tr><th>Recipient</th><th>Home</th><th>Cell</th></tr>';
    foreach ($rows as $r) {
        echo '<tr>'
            . '<td>' . htmlspecialchars($r['lastName'] . ', ' . $r['firstName']) . '</td>'
            . '<td>' . (empty($r['homePhone']) ? '—' : formatPhone($r['homePhone'])) . '</td>'
            . '<td>' . (empty($r['cellPhone']) ? '—' : formatPhone($r['cellPhone'])) . '</td>'
            . '</tr>';
    }
    echo '</table>';
}
