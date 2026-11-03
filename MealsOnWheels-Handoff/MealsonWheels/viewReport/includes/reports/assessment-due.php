<?php
// Assessment Due — recipient assessments whose "Next Due Date" (scoreType_id=5) is on/before a date.
// Source: assessmentScores (scoreType_id=5) JOIN assessmentScoreLinks (archived=0) JOIN recipients.
// Mirrors the dashboard "Due Assessments" widget.
$asOf = (!empty($_GET['asOf'])) ? date('Y-m-d', strtotime($_GET['asOf'])) : date('Y-m-d');

$rows = $db->select(
    'SELECT l.assessmentName, s.textValue AS dueDate, r.firstName, r.lastName
     FROM assessmentScores s
     JOIN assessmentScoreLinks l ON l.id = s.link_id
     JOIN recipients r ON r.id = l.recipient_id
     WHERE s.scoreType_id = 5 AND l.archived = 0
       AND TRY_CAST(CAST(s.textValue AS VARCHAR(50)) AS DATE) <= ?
     ORDER BY TRY_CAST(CAST(s.textValue AS VARCHAR(50)) AS DATE) ASC',
    [$asOf]
);

echo '<div class="rptHead"><h2>Assessment Due</h2><div class="rptMeta">Due on or before '
    . date('m/d/Y', strtotime($asOf)) . ' &middot; Generated ' . date('m/d/Y g:i a') . '</div></div>';

if (!$rows) {
    echo '<div class="rptEmpty">No assessments are due as of this date.</div>';
} else {
    echo '<div class="rptCount">' . count($rows) . ' assessment(s) due</div>';
    echo '<table class="rpt"><tr><th>Recipient</th><th>Assessment</th><th>Due Date</th></tr>';
    foreach ($rows as $r) {
        $due = $r['dueDate'];
        $dueFmt = (strtotime($due)) ? date('m/d/Y', strtotime($due)) : htmlspecialchars($due);
        echo '<tr>'
            . '<td>' . htmlspecialchars($r['lastName'] . ', ' . $r['firstName']) . '</td>'
            . '<td>' . htmlspecialchars($r['assessmentName']) . '</td>'
            . '<td>' . $dueFmt . '</td>'
            . '</tr>';
    }
    echo '</table>';
}
