<div class="formRow">
    <div class="inputRow">
        <label for="subscriber_id">Subscriber ID</label>
        <input type="text" name="subscriber_id" id="subscriber_id">
    </div>
    <div class="inputRow">
        <label for="authorizationNumber">Authorization Number</label>
        <input type="text" name="authorizationNumber" id="authorizationNumber">
    </div>
    <div class="inputRow">
        <label for="startDate">Start Date *</label>
        <input type="date" name="startDate" id="startDate" required>
    </div>
    <div class="inputRow">
        <label for="endDate">End Date</label>
        <input type="date" name="endDate" id="endDate">
    </div>
    <div class="inputRow">
        <label for="issueDate">Issue Date</label>
        <input type="date" name="issueDate" id="issueDate">
    </div>
    <div class="inputRow">
        <label for="unitedApproved">Units Approved</label>
        <input style="max-width: 150px;" type="number" name="unitedApproved" id="unitedApproved" min="0" step="1">
    </div>
    <div class="inputRow">
        <label for="frequency">Frequency *</label>
        <select name="frequency" id="frequency" required>
            <option value="" selected hidden>Select</option>
            <option value="Monthly">Monthly</option>
            <option value="Annually">Annually</option>
        </select>
    </div>
</div>
<div class="formRow">
    <div class="inputRow">
        <button type="submit">Add</button>
    </div>
</div>