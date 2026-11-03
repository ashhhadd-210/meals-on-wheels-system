<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/functionSetup.php');
?>
<div class="leadgerHeader">
    <h2>Meal Ledger</h2>
    <!-- <button style="margin-left: auto;" class="addConentBtn" type="button">Lock Day</button> -->
    <!-- <span>Next Auto Lock: <br>Today - 11:55 PM</span> -->
</div>
<div id="ledgerControls">
    <input type="text" value="<?= date('Y-m') ?>" class="monthSelector" id="ledgerMonthInput">
    <!-- <select id="ledgerType">
        <option value="program" selected>Program</option>
        <option value="route">By Route</option>
    </select> -->
    <!-- <select id="ledgerRoute">
        <option value="" selected hidden>Select</option>
        <?php
        // $routes = $db->select('SELECT * FROM routes ORDER BY name ASC');
        // function sortRoutes($a, $b)
        // {
        //     $numa = (int) extractNumber($a['name']);
        //     $numb = (int) extractNumber($b['name']);
        //     return $numa - $numb;
        // }
        // usort($routes, 'sortRoutes');
        // foreach ($routes as $r) {
        //     echo '<option value="' . $r['id'] . '">' . $r['name'] . '</option>';
        // }
        ?>
    </select> -->
    <button type="button" class="addConentBtn" onclick="getLedger()">Go</button>
</div>
<div id="ledgerContent"></div>

<script>
    monthInputs();
    $(document).ready(() => {
        getLedger();
    })

    function drillProgramView(btn, route, date) {
        if ($('#ledgerTable').hasClass('locked')) {
            $(btn).css({
                'color': 'red'
            });
            $('.ledgerLockIcon.locked').first().css({
                'color': 'red'
            });
            setTimeout(() => {
                $(btn).css({
                    'color': 'inherit'
                });
                $('.ledgerLockIcon.locked').first().css({
                    'color': 'inherit'
                });
            }, 200);
            return false;
        }
        $.ajax({
            url: 'menu/functions/ledger/programLedgerDrillView.php',
            type: 'POST',
            data: {
                route_id: route,
                date: date
            },
            success: (re) => {
                newModal(re);
            },
            error: (xhr, status, error) => {
                console.error("Error:", status, error);
            }
        });
    }

    function getLedger() {
        $('#ledgerContent').html('loading...');
        $.ajax({
            url: 'menu/functions/ledger/getLedger.php',
            type: 'POST',
            data: {
                type: 'program',
                // type: $('#ledgerType').val(),
                date: $('#ledgerMonthInput').val(),
                // route: $('#ledgerRoute').val()
            },
            success: (re) => {
                $('#ledgerContent').html(re)
            },
            error: (xhr, status, error) => {
                console.error("Error:", status, error);
                $('#ledgerContent').html("Error:", status, error);
            }
        });
    }


    function toggleLedgerLock() {
        var table = $('#ledgerTable');
        var lockIcon = $('.ledgerLockIcon.locked');
        var unlockIcon = $('.ledgerLockIcon.unlocked');

        if ($(table).hasClass('locked')) {
            $(table).removeClass('locked');
            $(lockIcon).hide();
            $(unlockIcon).show();
        } else {
            $(table).addClass('locked');
            $(lockIcon).show();
            $(unlockIcon).hide();

        }
    }
</script>

<style>
    .leadgerHeader {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .ledgerControlArrow {
        width: 25px;
        cursor: pointer;
    }

    #ledgerControls {
        background-color: #eee;
        border-radius: 5px;
        padding: 10px;
        display: flex;
        align-items: center;
        gap: 20px;
        justify-content: center;
    }

    #ledgerTable {
        border-collapse: collapse;
        width: 100%;
        font-size: 15px;
        min-width: 1200px;
    }

    #ledgerTable.locked {
        opacity: 0.5;
    }

    #ledgerTable,
    #ledgerTable th,
    #ledgerTable td {
        border: 1px solid #ccc;
    }

    #ledgerTable th,
    #ledgerTable td {
        padding: 2px 5px;
        text-align: left;
    }

    #ledgerTable td {
        cursor: pointer;
    }

    #ledgerTable td.hasLedger {
        background-color: #9abd9a;
    }

    #ledgerTable td:hover {
        background-color: #eee;
    }

    .routeRowTitle {
        width: 170px;
        overflow: hidden;
    }

    .ledgerInput {
        width: 70px;
    }

    .ledgerLockIcon {
        color: #c6c6c6;
        width: 25px;
        cursor: pointer;
    }

    #ledgerTableWrap {
        max-width: 100%;
        overflow: scroll;
    }

    .recipDrillWrap {
        display: flex;
        background-color: #eee;
        padding: 10px 20px;
        gap: 20px;
    }

    .recipDrillWrap.hasLedger {
        background-color: #9abd9a !important;
    }

    .recipDrillWrap h3 {
        width: 230px;
    }

    .recipDrillTotalWrap {
        display: flex;
        gap: 20px;
        margin: 0px auto;
        padding: 15px 10px;
        border-radius: 5px;
    }

    .recipDrillTotalWrap.hasLedger {
        background-color: #9abd9a !important;
    }

    .recipDrillTotal p {
        margin: 7px 0px;
    }

    .recipDrillTotal span {
        background-color: white;
        padding: 4px 30px;
    }
</style>