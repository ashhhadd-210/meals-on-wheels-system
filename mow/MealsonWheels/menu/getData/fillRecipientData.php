<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    $params = [];
    $sql = 'SELECT';
    $whereUsed = false;

    if(isset($_POST['amount'])){
        if($_POST['amount'] != 0){
            $sql .= ' TOP ' . $_POST['amount'];
        }
        $_SESSION['recipAmount'] = $_POST['amount'];
    }

    $sql .= ' * FROM recipients';

    if(!isset($_SESSION['statusFilter'])){
        $_SESSION['statusFilter'] = 1;
    } else {
        $_SESSION['statusFilter'] = $_POST['status'];
    }


    if($_SESSION['statusFilter'] != 'all'){
        $con = ($whereUsed) ? ' AND' : ' WHERE';
        $sql .=  $con . ' clientStatus_id = ?';
        $params[] = $_POST['status'];
        $whereUsed = true;

        $_SESSION['statusFilter'] = $_POST['status'];
    }

    if(!empty($_POST['textFilter'])){
        $con = ($whereUsed) ? ' AND' : ' WHERE';
        $sql .=  $con . ' (firstName LIKE ? OR lastName LIKE ? OR homePhone LIKE ? OR cellPhone LIKE ? OR addressLine1 LIKE ?)';
        $query = '%'.$_POST['textFilter'].'%';
        $params[] = $query;
        $params[] = $query;
        $params[] = $query;
        $params[] = $query;
        $params[] = $query;
        $whereUsed = true;
    }

    

    $sql .= ' ORDER BY lastName,firstName ASC';

    $recipients = $db->select($sql,$params);
    
    // exit(time().'_'.$_POST['status']);
    if ($recipients != false) {
        foreach ($recipients as $recip) {
            $select = '';
            if(isset($_SESSION['redirect'])){
                if($_SESSION['redirect'] == $recip['id']){
                    $select = ' active';
                    echo '<script>editContent(null,\'recipient\',' . $recip['id'] . ')</script>';
                    
                }
            } else {
                if(isset($_SESSION['last_recip'])){
                    if($_SESSION['last_recip'] == $recip['id']){
                        $select = ' active';
                        echo '<script>editContent(null,\'recipient\',' . $recip['id'] . ')</script>';
                    }
                }
            }
            echo '<div class="contentRow'.$select.'" onclick="editContent(this,\'recipient\',' . $recip['id'] . ')">';
            echo '<span class="recipFistName">'.$recip['lastName'].',</span>';
            echo '<span class="recipLastName">'.$recip['firstName'].'</span>';
            $nickName = (empty($recip['nickName'])) ? '' : '\''.$recip['nickName'].'\'';
            echo '<span class="recipNickName">'.$nickName.'</span>';
            echo '<img src="/media/icons/arrow.png" style="width:10px">';
            echo '</div>';
        }
        if(isset($_SESSION['redirect'])) {
            unset($_SESSION['redirect']);
        }
        echo '<small>Showing '.count($recipients).' of '.count($db->select('SELECT id FROM recipients')).'</small>';
    } else {
        echo '<div class="contentRow">';
        echo 'No Recipients';
        echo '<br>'.$db->error;
        echo '</div>';
    }
}
exit();
