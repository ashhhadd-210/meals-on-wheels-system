<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$user) {
    exit('Access Error');
}
?>
<h1>Dashboard</h1>
<div class="dashWrap">
    <div class="dashItem">
        <h3 class="dashItemTitle">Off Today</h3>
        <hr>
        <?php
        $offDays = $db->select('SELECT * FROM daysOff WHERE (startDate <= ? AND endDate >= ?) AND indefinate = 0 AND deactivate = 0', [date('Y-m-d'), date('Y-m-d')]);
        if (!$offDays) {
            echo 'No one off today';
        } else {
            foreach ($offDays as $od) {
                $recip = $db->select('SELECT recipients.id,recipients.firstName,recipients.lastName, routes.color, routes.name AS routeName FROM recipients JOIN routes ON routes.id = recipients.route_id WHERE recipients.id = ?', [$od['recipient_id']])[0];
                echo '<a href="/functions/redirects.php/?re=recipient&id=' . $recip['id'] . '" class="dashRowItem">' . $recip['firstName'] . ' ' . $recip['lastName'] . '<br><span style="color:'.$recip['color'].'">['.$recip['routeName'].']</span></a>';
            }
        }
        ?>
    </div>
    <div class="dashItem">
        <h3 class="dashItemTitle">Off Tomorrow</h3>
        <hr>
        <?php
        $tomorrow = (new DateTime())->modify('+1 day');
        $offDays = $db->select('SELECT * FROM daysOff WHERE (startDate <= ? AND endDate >= ?) AND indefinate = 0 AND deactivate = 0', [$tomorrow->format('Y-m-d'), $tomorrow->format('Y-m-d')]);
        if (!$offDays) {
            echo 'No one off tomorrow';
        } else {
            foreach ($offDays as $od) {
                $recip = $db->select('SELECT recipients.id,recipients.firstName,recipients.lastName, routes.color, routes.name AS routeName FROM recipients JOIN routes ON routes.id = recipients.route_id WHERE recipients.id = ?', [$od['recipient_id']])[0];
                echo '<a href="/functions/redirects.php/?re=recipient&id=' . $recip['id'] . '" class="dashRowItem">' . $recip['firstName'] . ' ' . $recip['lastName'] . '<br><span style="color:'.$recip['color'].'">['.$recip['routeName'].']</span></a>';
            }
        }
        ?>
    </div>
    <div class="dashItem">
        <h3 class="dashItemTitle">Off Temp</h3>
        <hr>
        <?php
        $offDays = $db->select('SELECT daysOff.reason, recipients.id AS recipRowID,recipients.firstName,recipients.lastName, routes.color, routes.name AS routeName, daysOff.id AS OD_ID FROM daysOff
        JOIN recipients ON daysOff.recipient_id = recipients.id 
        JOIN routes ON recipients.route_id  = routes.id 
        WHERE recipients.clientStatus_id = 1 AND daysOff.startDate <= ? AND daysOff.indefinate = 1 AND daysOff.deactivate = 0', [date('Y-m-d'), date('Y-m-d')]);
        if (!$offDays) {
            echo 'No one off temps';
        } else {
            foreach ($offDays as $od) {
                echo '<a href="/functions/redirects.php/?re=recipient&id=' . $od['recipRowID'] . '" class="dashRowItem">' . $od['firstName'] . ' ' . $od['lastName'] . '<br><span style="white-space:break-spaces;color:#c33a3a;">'.$od['reason'].'</span><br><span style="color:'.$od['color'].'">['.$od['routeName'].']</span></a>';
            }
        }
        ?>
    </div>
    <div class="dashItem">
        <h3 class="dashItemTitle">Follow Ups Due</h3>
        <hr>
        <?php
        $followUps = $db->select('SELECT * FROM noteLogs WHERE followUp = 1 AND complete = 0 AND followUpDate <= ?', [date('Y-m-d')]);
        if (!$followUps) {
            echo 'No follow ups today';
        } else {
            foreach ($followUps as $fu) {
                $recip = $db->select('SELECT id,firstName,lastName FROM recipients WHERE id = ?', [$fu['recipient_id']])[0];
                echo '<a href="/functions/redirects.php/?re=recipient&id=' . $recip['id'] . '" class="dashRowItem">' . $recip['firstName'] . ' ' . $recip['lastName'] . '<br>' . date('m/d/y', strtotime($fu['followUpDate'])) . '<br>' . substr($fu['note'], 0, 30) . '</a>';
            }
        }
        ?>
    </div>
    <div class="dashItem">
        <h3 class="dashItemTitle">Due Assessments</h3>
        <hr>
        <?php
        $dueAssess = $db->select(
            'SELECT assessmentScoreLinks.assessmentName, assessmentScores.textValue, assessmentScoreLinks.recipient_id 
        FROM assessmentScores 
        JOIN assessmentScoreLinks ON assessmentScoreLinks.id = assessmentScores.link_id 
        WHERE assessmentScores.scoreType_id = 5 AND assessmentScoreLinks.archived = 0 AND TRY_CAST(CAST(assessmentScores.textValue AS VARCHAR(50)) AS DATE) <= ?',
            [date('Y-m-d')]
        );
        foreach ($dueAssess as $da) {
            $recip = $db->select('SELECT id,firstName,lastName FROM recipients WHERE id = ?', [$da['recipient_id']])[0];
            echo '<a href="/functions/redirects.php/?re=recipient&id=' . $recip['id'] . '" class="dashRowItem">' . $recip['firstName'] . ' ' . $recip['lastName'] . '<br>' . date('m/d/y', strtotime($da['textValue'])) . '<br>' . $da['assessmentName'] . '</a>';
        }
        ?>
    </div>
    <div class="dashItem">
        <h3 class="dashItemTitle">Expiring Certs</h3>
        <hr>
        <?php
        $expCerts = $db->select('SELECT 
    *,
    CASE 
        WHEN c1.endDate < CAST(GETDATE() AS DATE) THEN 1
        ELSE 0
    END AS past
FROM certificateLogs AS c1
WHERE 
    c1.endDate <= DATEADD(DAY, 14, CAST(GETDATE() AS DATE))
    AND NOT EXISTS (
        SELECT 1
        FROM certificateLogs AS c2
        WHERE 
            c2.recipient_id = c1.recipient_id
            AND c2.startDate > c1.endDate
    ) ORDER BY c1.endDate DESC');
        if (!$expCerts) {
            echo 'None (for 14 days)';
        } else {
            foreach ($expCerts as $expc) {
                $class = ($expc['past'] == 1) ? '#ff2a20' : '#ffb020';
                $recip = $db->select('SELECT id,firstName,lastName FROM recipients WHERE id = ?', [$expc['recipient_id']])[0];
                echo '<a style="background-color:' . $class . '" href="/functions/redirects.php/?re=recipient&id=' . $recip['id'] . '" class="dashRowItem">' . $recip['firstName'] . ' ' . $recip['lastName'] . '<br>' . date('m/d/y', strtotime($expc['endDate'])) . '</a>';
            }
        }
        ?>
    </div>
</div>

<style>
    .dashWrap {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .dashItem {
        background-color: #eee;
        padding: 20px 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        border-radius: 5px;
        min-height: 200px;
        width: 250px;
        max-height: 500px;
        overflow-y: auto;
    }

    .dashItemTitle {
        margin: 0px;
    }

    .dashRowItem {
        width: 100%;
        background-color: white;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }
</style>