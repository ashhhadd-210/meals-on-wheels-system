<?php

use Dompdf\Dompdf;
use Dompdf\Options;

require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($user['isAdmin'] != 1) {
    exit('Access Error');
}

if (!isset($_GET['reportType'])) {
    exit('Missing data: Report Type');
}

$docTitle = ucwords(str_replace('-', ' ', urldecode($_GET['reportType']))) . ' Report';


if ($_GET['reportType'] == 'individual-recipient') {
    $recip = $db->select('SELECT * FROM recipients WHERE id = ?', [$_GET['recipient_id']])[0];
    if ($recip == false) {
        exit('Cannot find this recipient.');
    }

    $addTitle = $recip['firstName'] . '_' . $recip['lastName'];
} else if ($_GET['reportType'] == 'hot-labels') {
    $addTitle = date('Y-m-d', strtotime($_GET['label_date']));
} else if ($_GET['reportType'] == 'frozen-labels'){
    $addTitle = 'frozenLabel_'.date('Y-m-d', strtotime($_GET['label_date']));
} else if ($_GET['reportType'] == 'insuranceBilling'){
    $addTitle = 'insuranceBilling_'.date('Y-m-d', strtotime($_GET['label_date']));
} else {
    // Operational reports (2026 build-out): filename = type + today
    $addTitle = preg_replace('/[^a-z0-9\-]/i', '', $_GET['reportType']) . '_' . date('Y-m-d');
}


ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');

require $root . "/dompdf/vendor/autoload.php";

$options = new Options;
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);
$options->set('dpi', 100);
$dompdf = new Dompdf($options);
$dompdf->setPaper('letter', "portrait");
$dompdf->addInfo('Title', $docTitle);


if ($_GET['reportType'] == 'individual-recipient') {
    $html = '<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="' . $domain . '/css/docStyles.css">
        </head>
        <style>
        
    </style><body';
    $html .= '<h2>' . $docTitle . ' </h2>';
    include('includes/individualReport.php');
    $reportContent = ob_get_clean();
    $html .= $reportContent;
} else if ($_GET['reportType'] == 'hot-labels') {


    $html = '<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="' . $domain . '/css/hotLabelStyles.css">
        </head>
        <style>
        
    </style><body';
    $date = new DateTime($_GET['label_date']);

    if ($_GET['route_id'] == 'all') {
        $allRoutes = $db->select('SELECT * FROM routes ORDER BY id ASC');
        foreach($allRoutes as $route){
            $recipients = $db->select('SELECT * FROM recipients WHERE route_id = ? AND clientStatus_id = ? ORDER BY routePosition ASC', [$route['id'], 1]);
            if($recipients != false){
                include('includes/hotLabels.php');
            }
        }
    } else {
        $route = $db->select('SELECT * FROM routes WHERE id = ?', [$_GET['route_id']])[0];
        $recipients = $db->select('SELECT * FROM recipients WHERE route_id = ? AND clientStatus_id = ? ORDER BY routePosition ASC', [$_GET['route_id'], 1]);
        include('includes/hotLabels.php');
    }

    
    $reportContent = ob_get_clean();
    $html .= $reportContent;



} else if ($_GET['reportType'] == 'frozen-labels') {



    $html = '<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="' . $domain . '/css/frozenLabelStyles.css">
        </head>
        <style>
        
    </style><body';
    $date = new DateTime($_GET['label_date']);

    if ($_GET['route_id'] == 'all') {
        $allRoutes = $db->select('SELECT * FROM routes ORDER BY id ASC');
        foreach($allRoutes as $route){
            $recipients = $db->select('SELECT * FROM recipients WHERE route_id = ? AND clientStatus_id = ? ORDER BY routePosition ASC', [$route['id'], 1]);
            if($recipients != false){
                include('includes/frozenLabels.php');
            }
        }
    } else {
        $route = $db->select('SELECT * FROM routes WHERE id = ?', [$_GET['route_id']])[0];
        $recipients = $db->select('SELECT * FROM recipients WHERE route_id = ? AND clientStatus_id = ? ORDER BY routePosition ASC', [$_GET['route_id'], 1]);
        include('includes/frozenLabels.php');
    }

    
    $reportContent = ob_get_clean();
    $html .= $reportContent;


} else if ($_GET['reportType'] == 'insuranceBilling') {
    $dompdf->setPaper('letter', "landscape");
    $html = '<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" type="text/css" href="' . $domain . '/css/docStyles.css">
        </head>
        <style>
        
    </style><body';
    // $html .= '<h2>' . $docTitle . ' </h2>';
    include('includes/insuranceBilling.php');
    $reportContent = ob_get_clean();
    $html .= $reportContent;
} else {
    // ---- Operational list/table reports (2026 build-out) ----
    // Self-contained: CSS inlined from a local file (no remote dependency),
    // report type whitelisted, include path hardened with basename().
    $newReports = [
        'follow-ups-needed', 'physician-cert-due', 'assessment-due', 'status-change',
        'closed-call-list', 'route-totals', 'no-donation', 'unduplicated-count',
        'under-60', 'interventions'
    ];
    $rt = $_GET['reportType'];
    if (in_array($rt, $newReports, true)) {
        $wide = ['status-change', 'route-totals', 'interventions', 'unduplicated-count'];
        if (in_array($rt, $wide, true)) {
            $dompdf->setPaper('letter', 'landscape');
        }
        $reportCss = @file_get_contents($root . '/css/reportStyles.css');
        $html = '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><style>'
            . $reportCss . '</style></head><body>';
        include('includes/reports/' . basename($rt) . '.php');
        $html .= ob_get_clean();
    } else {
        ob_get_clean();
        exit('Unknown report type');
    }
}


$html .= '</body>';


$dompdf->loadHtml($html);


$dompdf->render();

$outName = $addTitle . '_' . $_GET['reportType'] . '_' . time();


$dompdf->stream($outName, ["Attachment" => false]);
$error = error_get_last();

ob_end_flush(); // Flush the output buffer and end buffering
exit();
