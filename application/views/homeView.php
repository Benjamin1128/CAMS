    <div class="container pt-2">
        <h1 class="text-center">Classroom Attendance Management System</h1>
        <div class="container-fluid">
            <div class="row">
                <!-- Total Students Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="homecard">
                        <div class="card-header">
                            <i class="card-icon fas fa-users"></i>
                            <h5 class="card-title">
                            <?php if ($student_id): ?>    
                                Total Other Students
                            <?php elseif ($teacher_id): ?>
                                Your Handling Students
                            <?php else: ?>    
                                Current Total Student
                            <?php endif; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo ($totalStudent); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Classroom Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="homecard">
                        <div class="card-header">
                            <i class="card-icon fas fa-chalkboard"></i>
                            <h5 class="card-title">
                            <?php if ($student_id): ?>
                                Your Attending Classroom
                            <?php elseif ($teacher_id): ?>
                                Your Handling Classroom
                            <?php else: ?>
                                Current Total Classroom
                            <?php endif; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo ($totalClassroom); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Teacher Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="homecard">
                        <div class="card-header">
                            <i class="card-icon fas fa-user-tie"></i>
                            <h5 class="card-title">
                            <?php if ($student_id): ?>
                                Your Total Teachers
                            <?php elseif ($teacher_id): ?>
                                Total Other Teachers
                            <?php else: ?>
                                Current Total Teacher
                            <?php endif; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo ($totalTeacher); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Salary Pay In Month Card -->
                <?php if (!$student_id && !$teacher_id): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="homecard">
                        <div class="card-header">
                            <i class="card-icon fas fa-dollar-sign"></i>
                            <h5 class="card-title">Total Salary Pay In Month</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">RM<?php echo ($totalSalary); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Current Time Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="homecard">
                        <div class="card-header">
                            <i class="card-icon fas fa-clock"></i>
                            <h5 class="card-title">Current Time</h5>
                        </div>
                        <div class="card-body">
                            <p id="current-time" class="card-text">Loading...</p>
                        </div>
                    </div>
                </div>

                <!-- Today's Date Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="homecard">
                        <div class="card-header">
                            <i class="card-icon fas fa-calendar-day"></i>
                            <h5 class="card-title">Today's Date</h5>
                        </div>
                        <div class="card-body">
                            <p id="current-date" class="card-text">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

<script>
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeString = `${hours}:${minutes}:${seconds}`;
        document.getElementById('current-time').textContent = timeString;
    }

    function updateDate() {
        const now = new Date();
        const options = { year: 'numeric', month: 'long', day: 'numeric'};
        const dateString = now.toLocaleDateString(undefined, options);
        document.getElementById('current-date').textContent = dateString;
    }

    updateTime();
    updateDate();
    setInterval(updateTime, 1000);
</script>


