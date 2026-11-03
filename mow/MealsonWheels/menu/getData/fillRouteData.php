<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['isAdmin'] == 1) {
    function sortRoutes($a, $b)
    {
        $numa = (int) extractNumber($a['name']);
        $numb = (int) extractNumber($b['name']);
        return $numa - $numb;
    }
    $routes = $db->select("SELECT * FROM routes ORDER BY name ASC");
    if ($routes != false) {

        usort($routes, 'sortRoutes');
        foreach ($routes as $route) {
            $select = '';


            echo '<div class="contentRow' . $select . '" onclick="editContent(this,\'route\',' . $route['id'] . ')">';
            echo '<span class="routeName">' . $route['name'] . '</span>';
            $color = (empty($route['color']) ? 'black' : $route['color']);
            echo '<span style="background-color:' . $color . '" class="routeColor"></span>';
            echo '<img src="/media/icons/arrow.png" style="width:10px">';
            echo '</div>';
        }
    } else {
        echo '<div class="contentRow">';
        echo 'No Routes';
        echo '</div>';
    }
}
exit();
