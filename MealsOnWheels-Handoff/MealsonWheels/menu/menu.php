
<h1 class="menuTitle">Meals On Wheels<img src="/media/logos/mowLogo.png"></h1>
<div class="menuUserName"><?php echo $_SESSION['user_firstName'] . ' '. $_SESSION['user_lastName']?></div>
<div class="menuOptions">
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/menu/menuOptions.php');
    foreach ($options as $option){
        $active = (isset($_SESSION['menu']) && $_SESSION['menu'] == $option['name']) ? ' active' : '';
        echo '<div class="menuOption '.$option['type']. $active .'" onclick="selectMenu(\''.$option['name'].'\',this)">'.$option['display'].'</div>';
    }
    ?>
    <hr style="margin-top: 20px;">
    <div style="margin-top: 20px;" class="menuOption menuMainOption" onclick="selectMenu('logout',this)">Logout</div>
</div>