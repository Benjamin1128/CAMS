<div class="container mt-2">
    <h1 class="text-center">Today's Attendance</h1>
    <div class="form-group">
        <label for="attendance_date">Today's Date:</label>
        <label id="attendance_date" class="date-label"></label>
    </div>
    <?php if ($this->session->flashdata('message')) : ?>
        <?php
            $message = $this->session->flashdata('message');
            $alertClass = $message['type'] === 'success' ? 'alert-success' : 'alert-danger';    
        ?>
        <div class="alert <?php echo $alertClass; ?>" role="alert">
            <?php echo htmlspecialchars($message['text']); ?>
        </div>
    <?php endif; ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Responsible Teacher</th>
                <th>Attendance Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="attendanceList">
            <?php if(!empty($classrooms)): ?>
                <?php foreach ($classrooms as $classroom): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($classroom['Class_Subject']); ?></td>
                        <td><?php echo htmlspecialchars($classroom['Teacher_Name']); ?></td>
                        <td>
                            <span class="badge <?php echo($classroom['AttendanceStatus'] === 'Complete') ? 'badge-complete' : 'badge-incomplete'; ?>">
                                <?php echo htmlspecialchars($classroom['AttendanceStatus']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="http://localhost/CAMS/index.php/takeAttendance/<?php echo urlencode($classroom['Class_ID']); ?>"  class="btn btn-primary">Take Attendance</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var today = new Date();
        var day = today.getDate();
        var month = today.getMonth() + 1;
        var year = today.getFullYear();
        var formattedDate = (day < 10 ? '0' + day : day) + '-' + (month < 10 ? '0' + month : month) + '-' + year;
        document.getElementById('attendance_date').textContent = formattedDate;
    });

    function updateTime() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const period = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
			const timeString = `${String(hours).padStart(2, '0')}:${minutes}:${seconds} ${period}`;
			document.getElementById('attendance_time').textContent = timeString;
    }
    updateTime();
    setInterval(updateTime, 1000);
</script>