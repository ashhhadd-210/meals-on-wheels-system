<?php
// Physician Certification Due — recipients whose most recent certification ends within N days
// (or is already expired). Source: certificateLogs (latest per recipient) JOIN recipients/physicians/clinics.
// Mirrors the dashboard "Expiring Certs" logic (NOT EXISTS later cert).
$days = (isset($_GET['daysAhead']) && $_GET['daysAhead'] !== '') ? (int) $_GET['daysAhead'] : 30;

$rows = $db->select(
    'SELECT c1.startDate, c1.endDate, r.firstName, r.lastName,
            p.firstName AS pFirst, p.lastName AS pLast, cl.name AS clinic,
            CASE WHEN c1.endDate < CAST(GETDATE() AS DATE) THEN 1 ELSE 0 END AS past
     FROM certificateLogs c1
     JOIN recipients r  ON r.id  = c1.recipient_id
     LEFT JOIN physicians p ON p.id = c1.physician
     LEFT JOIN clinics cl   ON cl.id = c1.clinic
     WHERE c1.endDate <= DATEADD(DAY, ' . $days . ', CAST(GETDATE() AS DATE))
       AND NOT EXISTS (
           SELECT 1 FROM certificateLogs c2
           WHERE c2.recipient_id = c1.recipient_id AND c2.startDate > c1.endDate
       )
     ORDER BY c1.endDate ASC'
);
// Note: $days is (int)-cast above, so it is safe to inline. It cannot be bound as a
// parameter here because pdo_sqlsrv binds it as nvarchar, which DATEADD() rejects.

echo '<div class="rptHead"><h2>Physician Certification Due</h2><div class="rptMeta">Ending within '
    . $days . ' day(s) of ' . date('m/d/Y') . ' (includes already expired) &middot; Generated '
    . date('m/d/Y g:i a') . '</div></div>';

if (!$rows) {
    echo '<div class="rptEmpty">No certifications are due within this window.</div>';
} else {
    echo '<div class="rptCount">' . count($rows) . ' certification(s)</div>';
    echo '<table class="rpt"><tr><th>Recipient</th><th>Physician</th><th>Clinic</th>'
        . '<th>Cert Start</th><th>Cert End</th><th>Status</th></tr>';
    foreach ($rows as $r) {
        $phys = trim(($r['pFirst'] ?? '') . ' ' . ($r['pLast'] ?? ''));
        $tag = ($r['past'] == 1)
            ? '<span class="tag tag-red">Expired</span>'
            : '<span class="tag tag-amber">Due</span>';
        echo '<tr>'
            . '<td>' . htmlspecialchars($r['lastName'] . ', ' . $r['firstName']) . '</td>'
            . '<td>' . htmlspecialchars($phys ?: '—') . '</td>'
            . '<td>' . htmlspecialchars($r['clinic'] ?: '—') . '</td>'
            . '<td>' . ($r['startDate'] ? date('m/d/Y', strtotime($r['startDate'])) : '—') . '</td>'
            . '<td>' . ($r['endDate'] ? date('m/d/Y', strtotime($r['endDate'])) : '—') . '</td>'
            . '<td>' . $tag . '</td>'
            . '</tr>';
    }
    echo '</table>';
}
