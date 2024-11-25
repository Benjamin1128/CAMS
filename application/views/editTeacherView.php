<div class="container mt-2">
    <h1 style="text-align: center;">Edit Teacher Information </h1>
    <form action="<?php echo site_url('updateTeacher'); ?>" method="POST">
        <div class="form-group">
            <label for="ic">Identity Number</label>
            <input type="text" class="form-control" id="ic" name="ic" maxlength="20" placeholder="Example: 000012348899" value="<?php echo ($teacherData['Teacher_ID']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo ($teacherData['Teacher_Name']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" class="form-control" id="age" name="age" maxlength="20" min="1" max="110" value="<?php echo ($teacherData['Teacher_Age']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select class="form-control" name="gender" id="gender" readonly required>
                <option value="Male" <?php if($teacherData['Teacher_Gender'] === 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if($teacherData['Teacher_Gender'] === 'Female') echo 'selected'; ?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" max-length="20" placeholder="Example: 0121238899" value="<?php echo ($teacherData['Teacher_Contact']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="salary">Salary Amount (RM)</label>
            <input type="number" class="form-control" id="salary" name="salary" min="0" step="0.01" placeholder="RM" value="<?php echo ($teacherData['Salary']); ?>" required>
            <small id="salaryHelp" class="form-text text-muted">Minimum 6 digits before decimal.</small>
        </div>
        <button type="submit" class="btn btn-primary">Update Teacher</button>
    </form>
</div>