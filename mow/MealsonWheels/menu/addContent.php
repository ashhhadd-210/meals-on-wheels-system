<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1){
    $content = false;
    if($_POST['type'] == 'recipient'){
        unset($_SESSION['last_recip']);
        ob_start();
        include_once('addContentForms/newRecipient.php');
        $content = ob_get_clean();
    } else if($_POST['type'] == 'contact'){
        ob_start();
        include_once('addContentForms/newContact.php');
        $content = ob_get_clean();
    } else if($_POST['type'] == 'route'){
        ob_start();
        include_once('addContentForms/newRoute.php');
        $content = ob_get_clean();
    } else if($_POST['type'] == 'diets'){
        ob_start();
        include_once('addContentForms/newDiet.php');
        $content = ob_get_clean();
    } else if($_POST['type'] == 'accommodations'){
        ob_start();
        include_once('addContentForms/newAccommodation.php');
        $content = ob_get_clean();
    } else if($_POST['type'] == 'allergies'){
        ob_start();
        include_once('addContentForms/newAllergy.php');
        $content = ob_get_clean();
    } else if($_POST['type'] == 'milks'){
        ob_start();
        include_once('addContentForms/newMilk.php');
        $content = ob_get_clean();
    }

    if(!$content){
        echo 'There was an issue finding that content';
    } else {
        echo $content;
    }
}
exit();