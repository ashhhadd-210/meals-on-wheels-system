<div class="formRow">
    <div class="inputRow">
        <label for="">Subscriber ID</label>
        <input type="text" value="<?php echo $billing['subscriber_id'] ?>" readonly>
    </div>
    <div class="inputRow">
        <label for="">Authorization Number</label>
        <input type="text" value="<?php echo $billing['authorizationNumber'] ?>" readonly>
    </div>
    <div class="inputRow">
        <label for="">Issue Date</label>
        <input type="text" value="<?php echo date('m/d/Y',strtotime($billing['issueDate'])) ?>" readonly>
    </div>
    <div class="inputRow">
        <label for="">Units Approved</label>
        <input type="text" value="<?php echo $billing['unitedApproved']?>" readonly>
    </div>
    <div class="inputRow">
        <label for="">Frequency</label>
        <input type="text" value="<?php echo $billing['frequency'] ?>" readonly>
    </div>
</div>
<div class="formRow">
    <div class="inputRow">
        <label for="">Start Date</label>
        <input type="text" value="<?php echo date('m/d/Y',strtotime($billing['startDate'])) ?>" readonly>
    </div>
</div>