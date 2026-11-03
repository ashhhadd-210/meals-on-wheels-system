<?php
$pageTitle = 'Home';
$meta = '';
$kws = '';
$pageUrl = '';
$needsEditor = true;
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
if (!isset($_SESSION['menu'])) {
    $_SESSION['menu'] = 'dashboard';
}
require_once('functions/runRouteChanges.php');
updateRouteChanges();

?>
<!DOCTYPE html>
<html lang="en">
<?php
if ($user['id'] == 1) {
    $cleancontacts = $db->select('SELECT * FROM contacts');
    foreach($cleancontacts as $c){
        $db->update('UPDATE contacts SET workPhone = ? WHERE id = ?',[cleanNumbers($c['workPhone']),$c['id']]);
    }
    // $file = fopen('files/contacts2.csv', 'r');
    // fgetcsv($file);
    // while (($row = fgetcsv($file)) !== FALSE){
    //     $db->insert('INSERT INTO contacts (firstName,lastName,homePhone,cellPhone,workPhone,addressLine1,city,state,zip) VALUES (?,?,?,?,?,?,?,?,?)',[
    //         $row[0],$row[1],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9]
    //     ]);
    // }
}
require_once($root . '/includes/head.php');
?>

<body>
    <?php
    require_once($root . '/includes/header.php');
    ?>
    <main>
        <div class="mainBody">
            <?php
            if ($_SESSION['menuToggle'] == 'true') {
                $menuToggle = ' style="display:none;"';
                $classValue = ' closed';
            } else {
                $menuToggle = '';
                $classValue = '';
            }

            ?>
            <div class="menuWrap" <?php echo $menuToggle ?>>
                <?php
                include_once($root . '/menu/menu.php');
                ?>
            </div>
            <div class="contentSection">
                <div class="contentHeader">
                    <div class="menuBurger<?php echo $classValue ?>" onclick="toggleMenu(this)">
                        <div class="BurgerBar"></div>
                        <div class="BurgerBar"></div>
                        <div class="BurgerBar"></div>
                    </div>
                    <div class="siteSearchWrap">
                        <input type="text" placeholder="Search" onchange="searchSite(this.value)">
                    </div>
                    <div class="contentVisWrap"><img src="/media/icons/invisible.png" onclick="hideContent(this)"></div>
                </div>
                <div class="contentWrap" id="contentWrap">
                    <?php
                    if (isset($_SESSION['menu'])) {
                        echo '<script>$(document).ready(function(){updateContent(\'' . $_SESSION['menu'] . '\')})</script>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
</body>
<script>
    function editContent(btn, type, id) {
        $('#contentWrap').fadeTo(10, 0.3, function() {
            $.ajax({
                type: 'POST',
                url: 'menu/editContent.php',
                data: {
                    type: type,
                    id: id
                },
                success: function(response) {
                    var wrap = $('#displayContentWrap');
                    wrap.fadeOut(100, () => {
                        wrap.html(response);
                        wrap.fadeIn(100)
                        $('#contentWrap').fadeTo(10, 1)
                    })
                    if (btn != null) {
                        var rows = $('.contentRow');
                        rows.each(function(index, element) {
                            $(element).removeClass('active')
                        })
                        $(btn).addClass('active')
                    }
                },
                error: function(error) {
                    console.log(error)
                }
            })
        })

    }

    function addContent(type) {
        $.ajax({
            type: 'POST',
            url: 'menu/addContent.php',
            data: {
                type: type
            },
            success: function(response) {
                var wrap = $('#displayContentWrap');
                wrap.fadeOut(100, () => {
                    wrap.html(response);
                    wrap.fadeIn(100)
                })
                var rows = $('.contentRow');
                rows.each(function(index, element) {
                    $(element).removeClass('active')
                })
            },
            error: function(error) {
                console.log(error)
            }
        })
    }

    function selectMenu(menu, btn) {
        if (menu == 'logout') {
            window.location.href = '/logout';
            return false;
        }
        $.ajax({
            type: 'POST',
            url: 'menu/menuSelect.php',
            data: {
                menu: menu
            },
            success: function(response) {
                if (response == 'ok') {
                    $('.menuOption').each(function(index, element) {
                        $(element).removeClass('active')
                    })
                    $(btn).addClass('active');
                    updateContent(menu);
                }
            },
            error: function(error) {
                console.log(error)
            }
        })
    }

    function deleteContent(type, id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                type: 'POST',
                url: 'menu/handlers/deleteContentHandler.php',
                data: {
                    contentType: type,
                    id: id
                },
                success: function(response) {
                    response = response.trim();
                    if(response == 'ok'){
                        window.location.reload();
                    } else {
                        customMessage(response,3,false,false)
                    }
                },
                error: function(error) {
                    console.log(error)
                }
            })
        }
    }

    function updateContent(menu) {
        $.ajax({
            type: 'POST',
            url: '/menu/menuContent.php',
            data: {
                menu: menu
            },
            success: function(response) {
                var contentWrap = $('#contentWrap');
                if (response != 'error') {
                    contentWrap.fadeOut(100, function() {
                        contentWrap.html('');
                        contentWrap.html(response);
                        contentWrap.fadeIn(200)
                    })
                } else {
                    customMessage('There was an issue fetching that menu.', 3, true, false);
                    contentWrap.html('');
                }
            },
            error: function(error) {
                console.log(error)
            }
        })
    }

    function toggleMenu(btn) {
        var menuToggle
        if ($(btn).hasClass('closed')) {
            $('.menuWrap').show(100);
            $(btn).removeClass('closed');
            menuToggle = 'false'
        } else {
            $('.menuWrap').hide(100);
            $(btn).addClass('closed');
            menuToggle = 'true'
        }
        $.ajax({
            type: 'POST',
            url: 'menuToggle.php',
            data: {
                value: menuToggle
            },
            success: function(response) {

            },
            error: function(error) {
                console.log(error)
            }
        })
    }

    function hideContent(btn) {
        if ($(btn).hasClass('hidden')) {
            $(btn).removeClass('hidden');
            $('#contentWrap').css({
                'opacity': '1',
                'filter': 'blur(0px)'
            })


            $(btn).prop('src', '/media/icons/invisible.png');
        } else {
            $(btn).addClass('hidden');
            $('#contentWrap').css({
                'opacity': '0.1',
                'filter': 'blur(3px)'
            })
            $(btn).prop('src', '/media/icons/visible.png');
        }
    }
</script>

</html>