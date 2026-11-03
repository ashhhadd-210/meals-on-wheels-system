<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $user['isAdmin'] != 1) {
    exit('Access Error');
}

switch ($_POST['id']) {
    case 1:
        include('individualRecipientReport.php');
        break;
    case 6:
        include('hotLabels.php');
        break;
    case 18:
        include('frozenLabels.php');
        break;
    case 14:
        include('insuranceBilling.php');
        break;

    // ---- Operational reports (added 2026 build-out) ----
    case 7:
        include('forms/follow-ups-needed.php');
        break;
    case 8:
        include('forms/physician-cert-due.php');
        break;
    case 9:
        include('forms/assessment-due.php');
        break;
    case 12:
        include('forms/status-change.php');
        break;
    case 10:
        include('forms/closed-call-list.php');
        break;
    case 15:
        include('forms/route-totals.php');
        break;
    case 3:
        include('forms/no-donation.php');
        break;
    case 4:
        include('forms/unduplicated-count.php');
        break;
    case 17:
        include('forms/under-60.php');
        break;
    case 5:
        include('forms/interventions.php');
        break;

    default:
        echo 'No Report Found';
        break;
}
exit();