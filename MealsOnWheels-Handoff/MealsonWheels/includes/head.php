<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="apple-touch-icon" sizes="180x180" href="/media/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/media/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/media/favicons/favicon-16x16.png">
    <link rel="manifest" href="/media/favicons/site.webmanifest">
    <link rel="mask-icon" href="/media/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#b91d47">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="https://use.typekit.net/wro5acz.css">
    <link rel="stylesheet" href="https://use.typekit.net/wnd8vva.css">  
    <link rel="stylesheet" href="/css/lib/jquery-ui-1.12.1.min.css">
    <script src="/js/lib/jquery-3.6.0.min.js"></script>
    <script src="/js/lib/jquery-ui-1.12.1.min.js"></script>
    <?php
    if($needsEditor){
        echo '<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>';
    }
    $version = time();
    $siteName = 'Meals On Wheels';
    $siteUrl = 'http://mow.perspektivemg.com/';
    echo '<meta name="description" content="' . $meta . '">';
    echo '<meta property="og:description" content="' . $meta . '">';
    $words = 'Car Smart, Car Rental, Wausau, Car Dealership, Cars';
    $incWords = ($kws == '') ? $words : $kws;
    echo '<meta name="keywords" content="' . $incWords . '">';
    $barrier = ' | ';
    if($pageTitle == 'Home'){$pageTitle = '';}
    $totPageTitle = ($pageTitle != '' || $pageTitle != null) ? $pageTitle . $barrier . $siteName : $siteName;
    echo '<title>' . $totPageTitle . '</title>';
    echo '<meta property="og:title" content="' . $totPageTitle . '">';
    echo '<meta property="og:url" content="' . $siteUrl . $pageUrl . '">';
    echo '<meta property="og:image" content="' . $siteUrl . '/media/logos/carSmartLogo.png">';
    echo '<link rel="stylesheet" href="/css/main.css?v=' . $version . '">';
    echo '<link rel="stylesheet" href="/css/styles.css?v=' . $version . '">';
    echo '<script src="/js/systems.js?v=' . $version . '"></script>';
    echo '<script src="/js/customMessage.js?v=' . $version . '"></script>';
    echo '<script src="/js/customDateFormat.js?v=' . $version . '"></script>';
    echo '<script src="/js/customCC.js?v=' . $version . '"></script>';
    echo '<script src="/js/customModal.js?v=' . $version . '"></script>';
    echo '<script src="/js/formControl.js?v=' . $version . '"></script>';
    ?>
    <?php
    if($user){
        include($root . '/includes/adminStyles.php');
    }
    ?>
</head>