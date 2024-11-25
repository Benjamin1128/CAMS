<div class="container mt-2">
    <?php if ($this->session->flashdata('message')) : ?>
        <?php
            $message = $this->session->flashdata('message');
            $alertClass = $message['type'] === 'success' ? 'alert-success' : 'alert-danger';    
        ?>
        <div class="alert <?php echo $alertClass; ?>" role="alert">
            <?php echo htmlspecialchars($message['text']); ?>
        </div>
    <?php endif; ?>
    <h1 class="text-center">User Profile</h1>
    <div class="container-fluid">
        <div class="row ProfileContainer">
            <div class="col-lg-6 col-md-12">
                <div class="card profile-card">
                    <div class="card-header profile-card-header">
                        <i class="card-icon fas fa-user"></i>
                        <h5 class="card-title">User Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo site_url('updateProfile'); ?>" method="POST">
                            <div class="form-group">
                                <label for="ic">ID</label>
                                <input type="text" class="form-control" id="ic" name="ic" value="<?php echo !empty($teacher_id) ? ($teacher_id) : (!empty($student_id) ? ($student_id) : "No user ID available."); ?>" readonly required>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo !empty($teacherName) ? ($teacherName) : (!empty($studentName) ? ($studentName) : "No username available."); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="age">Age</label>
                                <input type="number" class="form-control" id="age" name="age" maxlength="20" min="1" max="110" value="<?php echo !empty($teacherAge) ? ($teacherAge) : (!empty($studentAge) ? ($studentAge) : "No user age available."); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <?php if ($student_id): ?>
                                <select class="form-control" name="gender" id="gender" required>
                                    <option value="Male" <?php if ($studentGender === 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if ($studentGender === 'Female') echo 'selected'; ?>>Female</option>
                                </select>
                                <?php elseif ($teacher_id): ?>
                                <select class="form-control" name="gender" id="gender" required>
                                    <option value="Male" <?php if ($teacherGender === 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if ($teacherGender === 'Female') echo 'selected'; ?>>Female</option>
                                </select>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" max-length="20" placeholder="Example: 0121238899" value="<?php echo !empty($teacherContact) ? ($teacherContact) : (!empty($studentContact) ? ($studentContact) : "No user phone number available."); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" max-length="100" placeholder="Example: abc123@gmail.com" value="<?php echo !empty($teacherEmail) ? ($teacherEmail) : (!empty($studentEmail) ? ($studentEmail) : "No user email available."); ?>" required>
                            </div>
                            <?php if ($teacher_id): ?>
                            <div class="form-group">
                                <label for="salary">Salary (RM)</label>
                                <input type="text" class="form-control" id="salary" name="salary" value="<?php echo !empty($salary) ? ($salary) : 'No salary available.'; ?>" readonly required>
                            </div>
                            <?php endif;?>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#passwordVerificationModal">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- First Modal: Verify Current Password -->
<div class="modal fade" id="passwordVerificationModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Password Verification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="verifyPasswordForm" action="<?php echo site_url('verifyPwd'); ?>" method="POST">
                <input type="hidden" class="form-control" id="passwordHolding" name="passwordHolding" value="<?php echo !empty($teacherPwd) ? ($teacherPwd) : (!empty($studentPwd) ? ($studentPwd) : "No user pwd available."); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="verifyButton">Verify</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Second Modal: Change Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    Change Password
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo site_url('updatePwd'); ?>" method="POST">
                <input type="hidden" class="form-control" id="UserID" name="UserID" value="<?php echo !empty($teacher_id) ? ($teacher_id) : (!empty($student_id) ? ($student_id) : "No user ID available."); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" minlength="6" maxlength="20" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" minlength="6" maxlength="20" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="updateButton" disabled>Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const newPasswordInput = document.getElementById('newPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const updateButton = document.getElementById('updateButton');

        function validatePasswords() {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            if (newPassword === confirmPassword && confirmPassword === newPassword) {
                updateButton.disabled = false;
            } else {
                updateButton.disabled = true;
            }
        }
        newPasswordInput.addEventListener('input', validatePasswords);
        confirmPasswordInput.addEventListener('input', validatePasswords);
    });

    document.getElementById('verifyPasswordForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const currentPassword = document.getElementById('currentPassword').value;
        fetch('<?php echo site_url('verifyPwd'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                currentPassword: currentPassword,
                passwordHolding: document.getElementById('passwordHolding').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#passwordVerificationModal').modal('hide');
                $('#changePasswordModal').modal('show');
            } else {
                alert('Current password is incorrect.');
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>