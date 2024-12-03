<div class="container pt-2">
    <h1 style="text-align: center;">Add New Student</h1>
    <form action="<?php echo site_url('insertStudent'); ?>" method="POST">
        <div class="form-group">
            <label for="ic">Identity Number</label>
            <input type="text" class="form-control" id="ic" name="ic" maxlength="20" placeholder="Example: 000012348899" required>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" class="form-control" id="age" name="age" maxlength="20" min="1" max="110" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select class="form-control" name="gender" id="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" max-length="20" placeholder="Example: 0121238899" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" max-length="100" placeholder="Example: abc123@gmail.com" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Student</button>
    </form>
</div>