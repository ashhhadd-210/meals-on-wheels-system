<?php
// Route Totals — current active recipient counts per route. Source: routes LEFT JOIN recipients (active).
$rows = $db->select(
    'SELECT rt.id, rt.name, rt.color, COUNT(r.id) AS activeCount
     FROM routes rt
     LEFT JOIN recipients r ON r.route_id = rt.id AND r.clientStatus_id = 1
     GROUP BY rt.id, rt.name, rt.color'
);

// Natural sort by route number (matches the label-report ordering).
usort($rows, function ($a, $b) {
    return (int) extractNumber($a['name']) - (int) extractNumber($b['name']);
});

echo '<div class="rptHead"><h2>Route Totals</h2><div class="rptMeta">Active recipients per route'
    . ' &middot; Generated ' . date('m/d/Y g:i a') . '</div></div>';

$grand = 0;
echo '<table class="rpt"><tr><th>Route</th><th class="num">Active Recipients</th></tr>';
foreach ($rows as $r) {
    $grand += (int) $r['activeCount'];
    echo '<tr>'
        . '<td><span style="color:' . htmlspecialchars($r['color']) . '">&#9632;</span> ' . htmlspecialchars($r['name']) . '</td>'
        . '<td class="num">' . (int) $r['activeCount'] . '</td>'
        . '</tr>';
}
echo '<tr class="totRow"><td>Total (' . count($rows) . ' routes)</td><td class="num">' . $grand . '</td></tr>';
echo '</table>';
