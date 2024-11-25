<div class="container mt-2">
    <h1 class="text-center">Past's Attendance</h1>
    <div class="form-group">
        <label for="past_date">Select a Date:</label>
        <input type="date" name="past_date" id="past_date" value="<?php echo htmlspecialchars($pastDate); ?>">
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
                            <a href="http://localhost/CAMS/index.php/takePastAttendance/<?php echo htmlspecialchars($pastDate)?>/<?php echo urlencode($classroom['Class_ID']); ?>"  class="btn btn-primary">Take Attendance</a>
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
        var yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1);
        var year = yesterday.getFullYear();
        var month = (yesterday.getMonth() + 1).toString().padStart(2, '0');
        var day = yesterday.getDate().toString().padStart(2, '0');
        var formattedDate = `${year}-${month}-${day}`;
        var dateInput = document.getElementById('past_date');
        dateInput.setAttribute('max',formattedDate);
        dateInput.addEventListener('change', function() {
            var selectedDate = dateInput.value;
            var url = 'http://localhost/CAMS/index.php/pastAttendance?pastdate=' + encodeURIComponent(selectedDate);
            window.location.href = url;
        });
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