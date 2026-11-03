<?php
// Closed Call List — recipients whose current status is "Non Active" (closed cases), with contact info.
// Assumption (confirm in Phase 1): "Closed" maps to the "Non Active" clientStatus; there is no literal
// "Closed" status in the system (statuses are Active / Non Active / Applicant).
$rows = $db->select(
    'SELECT r.id, r.firstName, r.lastName, r.homePhone, r.cellPhone,
            r.addressLine1, r.city, r.state, r.zip,
            (SELECT TOP 1 l.logDate FROM clientStatusLog l
             WHERE l.recipient_id = r.id AND l.currentStatus = ? ORDER BY l.id DESC) AS closedDate
     FROM recipients r
     JOIN clientStatus cs ON cs.id = r.clientStatus_id
     WHERE cs.name = ?
     ORDER BY r.lastName ASC, r.firstName ASC',
    ['Non Active', 'Non Active']
);

echo '<div class="rptHead"><h2>Closed Call List</h2><div class="rptMeta">Recipients currently Non Active'
    . ' &middot; Generated ' . date('m/d/Y g:i a') . '</div></div>';

if (!$rows) {
    echo '<div class="rptEmpty">No Non Active recipients.</div>';
} else {
    echo '<div class="rptCount">' . count($rows) . ' closed recipient(s)</div>';
    echo '<table class="rpt"><tr><th>Recipient</th><th>Home</th><th>Cell</th><th>Address</th><th>Closed</th></tr>';
    foreach ($rows as $r) {
        $addr = trim($r['addressLine1'] . ', ' . $r['city'] . ', ' . $r['state'] . ' ' . $r['zip'], ', ');
        echo '<tr>'
            . '<td>' . htmlspecialchars($r['lastName'] . ', ' . $r['firstName']) . '</td>'
            . '<td>' . (empty($r['homePhone']) ? '—' : formatPhone($r['homePhone'])) . '</td>'
            . '<td>' . (empty($r['cellPhone']) ? '—' : formatPhone($r['cellPhone'])) . '</td>'
            . '<td>' . htmlspecialchars($addr ?: '—') . '</td>'
            . '<td>' . ($r['closedDate'] ? date('m/d/Y', strtotime($r['closedDate'])) : '—') . '</td>'
            . '</tr>';
    }
    echo '</table>';
}
