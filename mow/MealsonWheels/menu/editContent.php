<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1){
    $content = false;
    if(ctype_digit($_POST['id'])){
        if($_POST['type'] == 'recipient'){
            ob_start();
            include_once('editContentForms/editRecipient.php');
            $content = ob_get_clean();
        } else if($_POST['type'] == 'contact'){
            ob_start();
            include_once('editContentForms/editContact.php');
            $content = ob_get_clean();
        } else if($_POST['type'] == 'route'){
            ob_start();
            include_once('editContentForms/editRoute.php');
            $content = ob_get_clean();
        } else if($_POST['type'] == 'diets'){
            ob_start();
            if($user['permissionLevel'] < 3){
                echo 'You do not have permission to access this.';
            } else {
                include_once('editContentForms/editDiet.php');
            }
            $content = ob_get_clean();
        } else if($_POST['type'] == 'accommodations'){
            ob_start();
            if($user['permissionLevel'] < 3){
                echo 'You do not have permission to access this.';
            } else {
                include_once('editContentForms/editAccommodation.php');
            }
            $content = ob_get_clean();
        } else if($_POST['type'] == 'allergies'){
            ob_start();
            if($user['permissionLevel'] < 3){
                echo 'You do not have permission to access this.';
            } else {
                include_once('editContentForms/editAllergy.php');
            }
            $content = ob_get_clean();
        } else if($_POST['type'] == 'milks'){
            ob_start();
            if($user['permissionLevel'] < 3){
                echo 'You do not have permission to access this.';
            } else {
                include_once('editContentForms/editMilk.php');
            }
            $content = ob_get_clean();
        } else if($_POST['type'] == 'assessment'){
            ob_start();
            if($user['permissionLevel'] < 3){
                echo 'You do not have permission to access this.';
            } else {
                include_once('editContentForms/editAssessment.php');
            }
            $content = ob_get_clean();
        }
    
        if(!$content){
            echo 'There was an issue finding that content';
        } else {
            echo $content;
        }
    }
}
exit();