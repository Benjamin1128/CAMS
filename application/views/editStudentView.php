<div class="container mt-2">
    <h1 style="text-align: center;">Edit Student Information</h1>
    <form action="<?php echo site_url('updateStudent'); ?>" method="POST">
        <div class="form-group">
            <label for="ic">Identity Number</label>
            <input type="text" class="form-control" id="ic" name="ic" maxlength="20" placeholder="Example: 000012348899" value="<?php echo ($studentData['Student_ID']); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo ($studentData['Student_Name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" class="form-control" id="age" name="age" maxlength="20" min="1" max="110" value="<?php echo ($studentData['Student_Age']); ?>" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select class="form-control" name="gender" id="gender" required>
                <option value="Male" <?php if ($studentData['Student_Gender'] === 'Male') echo 'selected' ?>>Male</option>
                <option value="Female" <?php if ($studentData['Student_Gender'] === 'Female') echo 'selected' ?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" max-length="20" placeholder="Example: 0121238899" value="<?php echo ($studentData['Student_Contact']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Student</button>
    </form>
</div>