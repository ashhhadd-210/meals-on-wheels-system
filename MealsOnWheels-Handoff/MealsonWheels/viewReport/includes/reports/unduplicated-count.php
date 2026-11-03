<?php
// Unduplicated Program Count — distinct recipients who received >=1 meal within the date range,
// with demographic breakdowns. Source: mealLedgers.recipMatrix (CSV: "recipId-hotCount-frozenCount,...").
$start = date('Y-m-d', strtotime($_GET['startDate']));
$end   = date('Y-m-d', strtotime($_GET['endDate']));

$ledgers = $db->select(
    'SELECT recipMatrix FROM mealLedgers WHERE dateStamp >= ? AND dateStamp <= ?',
    [$start, $end]
);

// Collect distinct recipient ids that received at least one meal.
$served = [];
$mealTotal = 0;
foreach ($ledgers as $lg) {
    if (empty($lg['recipMatrix'])) {
        continue;
    }
    foreach (explode(',', $lg['recipMatrix']) as $entry) {
        $p = explode('-', $entry);
        if (count($p) >= 3) {
            $meals = (int) $p[1] + (int) $p[2];
            if ($meals > 0) {
                $served[(int) $p[0]] = true;
                $mealTotal += $meals;
            }
        }
    }
}
$ids = array_keys($served);

// Build a lookup of recipient demographics (status + age) for the served ids.
$byStatus = [];
$under60 = 0;
$sixtyPlus = 0;
$now = new DateTime('now');
if ($ids) {
    $recips = $db->select('SELECT r.id, r.dob, cs.name AS status
                           FROM recipients r LEFT JOIN clientStatus cs ON cs.id = r.clientStatus_id');
    $map = [];
    foreach ($recips as $r) {
        $map[(int) $r['id']] = $r;
    }
    foreach ($ids as $id) {
        if (!isset($map[$id])) {
            continue;
        }
        $st = $map[$id]['status'] ?: 'Unknown';
        $byStatus[$st] = ($byStatus[$st] ?? 0) + 1;
        if (!empty($map[$id]['dob'])) {
            $age = (new DateTime($map[$id]['dob']))->diff($now)->y;
            if ($age < 60) $under60++; else $sixtyPlus++;
        }
    }
    ksort($byStatus);
}

echo '<div class="rptHead"><h2>Unduplicated Program Count</h2><div class="rptMeta">'
    . date('m/d/Y', strtotime($start)) . ' &ndash; ' . date('m/d/Y', strtotime($end))
    . ' &middot; Generated ' . date('m/d/Y g:i a') . '</div></div>';

echo '<div class="rptCount">Unduplicated recipients served: <b>' . count($ids) . '</b>'
    . ' &middot; Total meals delivered: <b>' . $mealTotal . '</b></div>';

if (!$ids) {
    echo '<div class="rptEmpty">No meals recorded in this range.</div>';
} else {
    echo '<table class="rpt"><tr><th>Breakdown by Status</th><th class="num">Recipients</th></tr>';
    foreach ($byStatus as $name => $n) {
        echo '<tr><td>' . htmlspecialchars($name) . '</td><td class="num">' . $n . '</td></tr>';
    }
    echo '<tr class="totRow"><td>Total</td><td class="num">' . count($ids) . '</td></tr>';
    echo '</table>';

    echo '<table class="rpt" style="margin-top:14px;"><tr><th>Age Group (by DOB)</th><th class="num">Recipients</th></tr>';
    echo '<tr><td>Under 60</td><td class="num">' . $under60 . '</td></tr>';
    echo '<tr><td>60 and over</td><td class="num">' . $sixtyPlus . '</td></tr>';
    $noDob = count($ids) - $under60 - $sixtyPlus;
    if ($noDob > 0) {
        echo '<tr><td>No DOB on file</td><td class="num">' . $noDob . '</td></tr>';
    }
    echo '</table>';
}
