<?php
// Status Change Report — client status changes within a date range. Source: clientStatusLog JOIN recipients.
$start = date('Y-m-d', strtotime($_GET['startDate']));
$end   = date('Y-m-d', strtotime($_GET['endDate']));
$statusFilter = $_GET['status'] ?? 'all';

$params = [$start, $end];
$sql = 'SELECT csl.logDate, csl.currentStatus, r.firstName, r.lastName
        FROM clientStatusLog csl
        JOIN recipients r ON r.id = csl.recipient_id
        WHERE csl.logDate >= ? AND csl.logDate <= ?';
$statusName = 'All statuses';
if ($statusFilter !== 'all' && ctype_digit((string) $statusFilter)) {
    $st = $db->select('SELECT name FROM clientStatus WHERE id = ?', [$statusFilter]);
    if ($st) {
        $statusName = $st[0]['name'];
        $sql .= ' AND csl.currentStatus = ?';
        $params[] = $st[0]['name'];
    }
}
$sql .= ' ORDER BY csl.logDate DESC, r.lastName ASC';
$rows = $db->select($sql, $params);

echo '<div class="rptHead"><h2>Status Change Report</h2><div class="rptMeta">'
    . date('m/d/Y', strtotime($start)) . ' &ndash; ' . date('m/d/Y', strtotime($end))
    . ' &middot; ' . htmlspecialchars($statusName) . ' &middot; Generated ' . date('m/d/Y g:i a') . '</div></div>';

if (!$rows) {
    echo '<div class="rptEmpty">No status changes in this range.</div>';
} else {
    echo '<div class="rptCount">' . count($rows) . ' status change(s)</div>';
    echo '<table class="rpt"><tr><th>Date</th><th>Recipient</th><th>New Status</th></tr>';
    foreach ($rows as $r) {
        echo '<tr><td>' . date('m/d/Y', strtotime($r['logDate'])) . '</td>'
            . '<td>' . htmlspecialchars($r['lastName'] . ', ' . $r['firstName']) . '</td>'
            . '<td>' . htmlspecialchars($r['currentStatus']) . '</td></tr>';
    }
    echo '</table>';
}
