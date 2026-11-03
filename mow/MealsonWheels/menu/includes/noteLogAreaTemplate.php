<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    
    if (isset($_POST['noteLogDataNeeded'])) {
        $noteLogs = $db->select('SELECT * FROM noteLogs WHERE recipient_id = ? ORDER BY dateStamp DESC', [$_SESSION['editing_recip_id']]);
        unset($_POST['noteLogDataNeeded']);
    }

    if ($noteLogs != false) {
        $lastYear = null;
        $lastMonth = null;
        $actualYear = date('Y');
        $actualMonth = date('F');
        foreach ($noteLogs as $log) {
            $year = date('Y',strtotime($log['dateStamp']));
            $month = date('F',strtotime($log['dateStamp']));

            if($year !== $lastYear){
                if(isset($lastMonth)){
                    echo '</div>';
                }
                if(isset($lastYear)){
                    echo '</div>';
                }
                $yearHeaderOpenClass = '';
                if($year == $actualYear){
                    $yearHeaderOpenClass = ' open';
                }
                echo '<div class="noteYearWrap">';
                echo '<h2 class="noteyearWrapHeader'.$yearHeaderOpenClass.'" onclick="toggleNoteYear(this)">'.$year.'</h2>';
                $lastYear = $year;
                $lastMonth = null;
            }

            if ($month !== $lastMonth){
                if (isset($lastMonth)){
                    echo '</div>';
                }
                $yearDisplay = 'display:none;';
                $yearOpenClass = '';
                if($year == $actualYear){
                    $yearDisplay = '';
                    if($month == $actualMonth){
                        $yearOpenClass = ' open';
                    }
                    
                }
                echo '<div class="noteMonthWrap" style="'.$yearDisplay.'">';
                echo '<h2 class="noteMonthWrapHeader'.$yearOpenClass.'" onclick="toggleNoteMonth(this)">'.$month.'</h2>';
                $lastMonth = $month;
            }
            $noteDisplay = 'display:none;';
            if($year == $actualYear && $month == $actualMonth){
                $noteDisplay = '';
            }

            echo '<div class="noteLogItemWrap" style="'.$noteDisplay.(($log['complete'] == 1) ? 'background-image:linear-gradient(#7edb7ebd,#89d2896e);' : '').'">';

            echo '<div class="noteLogDetailWrap">';
            echo '<span class="noteLogDate">' . date('m/d/y \a\t h:i A', strtotime($log['dateStamp'])) . '</span>';
            if($log['complete'] == 1){
                echo '<span class="noteFollowUp">Complete</span>';
            } else {
                echo '<span class="noteFollowUp">Follow Up: ' . (($log['followUp'] == 1) ? ('Yes ' . ((empty($log['followUpDate'])) ? '' : ' - ' . date('m/d/y', strtotime($log['followUpDate'])))) : 'No') . '</span>';
            }
            $author = $db->select('SELECT id,firstName,lastName FROM users WHERE id = ?', [$log['author_id']])[0];
            echo '<span class=noteLogAuthor">by ' . $author['firstName'] . ' ' . $author['lastName'] . '</span>';
            echo (($user['permissionLevel'] > 2) ? '<span style="cursor:pointer" onclick="editNoteLog(' . $log['id'] . ')">Edit</span>' : '');
            echo '</div>';

            echo '<div class="noteText">' . $log['note'] . '</div>';

            echo '<div class="noteLogActionWrap">';
            if($log['emergContactCalled'] == 1){echo '<span>Emergency Contact Called</span>';}
            if($log['emsCalled'] == 1){echo '<span>EMS Called</span>';}
            if($log['referralMade'] == 1){echo '<span>Referral Made</span>';}
            echo '</div>';

            echo '<h4 style="margin:0px">Contact Trace</h4>';
            echo '<div class="contactTraceWrap">';
            $contactsMade = $db->select('SELECT * FROM noteLogContacts WHERE log_id = ? ORDER BY dateStamp ASC,timeStamp ASC', [$log['id']]);
            if($contactsMade != false){
                foreach ($contactsMade as $trace) {
                    if(!empty($trace['contact_id'])){
                        $curCon = $db->select('SELECT id,firstName,lastName FROM contacts WHERE id = ?', [$trace['contact_id']])[0];
                        $displayName = '<span onclick="viewContact(' . $curCon['id'] . ')" class="contactTraceName">' . $curCon['firstName'] . ' ' . $curCon['lastName'] . '</span>';
                    } else {
                        $displayName = '<span style="color: #5b5be1;" class="contactTraceName">'.$trace['extName'].'</span>';
                    }
                    
                    $author = $db->select('SELECT id,firstName,lastName FROM users WHERE id = ?',[$trace['author_id']])[0];
                    echo '<div class="contactTraceItem">'.$displayName.'<span>'.date('m/d/y',strtotime($trace['dateStamp'])).' - ' . date('h:i A',strtotime($trace['timeStamp'])) . '</span><span class="contactDesc">' . $trace['contactDesc'] . '</span>
                    <span style="margin-left:auto;font-size:14px">'.$author['firstName'] . ' '. substr($author['lastName'],0,1) . '</span></div>';
                }
            } else {
                echo '<span>No contacts made</span>';
            }
            echo '</div>';
            echo '</div>';
        }
        if (isset($lastMonth)){
            echo '</div>';
        }
        if (isset($lastYear)){
            echo '</div>';
        }
    } else {
        echo '<span>No Note Logs</span>';
    }
}
