<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    $contacts = $db->select('SELECT * FROM contacts ORDER BY lastName,firstName ASC');
    if ($contacts != false) {
        foreach ($contacts as $recip) {
            $select = '';
            if (!isset($_SESSION['redirect'])) {
                if (isset($_SESSION['last_contact'])) {
                    if ($_SESSION['last_contact'] == $recip['id']) {
                        $select = ' active';
                        echo '<script>editContent(null,\'contact\',' . $recip['id'] . ')</script>';
                    }
                }
            } else {
                if ($_SESSION['redirect'] == 'contacts') {
                    if ($_SESSION['redirect_mod'] == 'new') {
                        echo "<script>addContent('contact')</script>";
                        // echo "<script>fillContactData()</script>";
                        break;
                    } else if ($_SESSION['redirect_mod'] == 'view' && ($_SESSION['redirect_data'] == $recip['id'])) {
                        $select = ' active';
                        echo '<script>editContent(null,\'contact\',' . $_SESSION['redirect_data'] . ')</script>';
                    }
                }
            }

            echo '<div class="contentRow' . $select . '" onclick="editContent(this,\'contact\',' . $recip['id'] . ')">';
            echo '<span class="recipFistName">' . $recip['lastName'] . ',</span>';
            echo '<span class="recipLastName">' . $recip['firstName'] . '</span>';
            echo '<img src="/media/icons/arrow.png" style="width:10px">';
            echo '</div>';
        }
    } else {
        echo '<div class="contentRow">';
        echo 'No Contacts';
        echo '</div>';
    }
}
exit();
