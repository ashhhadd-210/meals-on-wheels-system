<?php
$pageTitle = 'Login';
$meta = '';
$kws = '';
$pageUrl = '';
$needsEditor = false;
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
if (isset($_SESSION['user'])) {
    header('location: /');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
require_once($root . '/includes/head.php');
?>

<body>
    <?php
    require_once($root . '/includes/header.php');
    ?>
    <main>
        <form action="login.php" method="post" enctype="multipart/form-data" class="loginForm" onsubmit="return sendLogin()">
            <img src="/media/logos/mowLogo.png" class="loginLogo" alt="">
            <div class="formRow">
                <div class="inputRow">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
            </div>
            <div class="formRow">
                <div class="inputRow">
                    <label for="pswd">Password</label>
                    <input type="password" name="pswd" id="pswd" required>
                </div>
            </div>
            <div class="formRow">
                <div class="inputRow">
                    <button type="submit">Login</button>
                </div>
            </div>
        </form>
    </main>
</body>
<script>
    function sendLogin() {
        $.ajax({
            url: 'login.php',
            type: 'POST',
            data: {
                email: $('#email').val(),
                pswd: $('#pswd').val()
            },
            success: function(response) {
                if (response == 'ok') {
                    window.location.reload();
                } else {
                    customMessage(response, 3, false, true)
                }
            },
            error: function(error) {
                console.log(error)
            }
        })
        return false;
    }
</script>

</html>