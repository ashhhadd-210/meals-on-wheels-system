<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
$date = (new DateTime($_POST['date'] . '-01'))->setTime(0, 0, 0, 0);
$today = (new DateTime())->setTime(0, 0, 0, 0);

if ((int)$date->format('ym') > (int)$today->format('ym')) {
    exit('Future months cannot be edited.');
}


$weekDayNumber = $date->format('N');
$daysInMonth = (int)$date->format('t');

$types = ['program' => 'programLedger.php', 'route' => 'routeLedger.php'];
$ledgerType = $types[$_POST['type']];
include_once($ledgerType);
