<?php
$date = new DateTime($_POST['date']);
if($_POST['action'] == 'up'){
    $date->modify('+1 day');
} else if ($_POST['action'] == 'down'){
    $date->modify('-1 day');
}
exit($date->format('Y-m-d'));