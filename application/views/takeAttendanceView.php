<div class="container mt-2">
    <h1 class="text-center">Take Daily Attendance</h1>
    <div class="form-row">
        <div class="form-group">
            <label for="attendance_date">Today's Date:</label>
            <label id="attendance_date" class="date-label"></label>
        </div>
        <div class="form-group">
            <label for="class_name">Subject:</label>
            <label id="class_name"><?php echo htmlspecialchars($classDetail['Class_Subject']); ?></label>
        </div>
        <div class="form-group">
            <label for="teacher_name">Teacher:</label>
            <label id="teacher_name"><?php echo htmlspecialchars($classDetail['Teacher_Name'])?></label>
        </div> 
        
    </div>
    <div class="d-flex justify-content-end" style="margin-bottom: 2%; margin-right: 2%;">
        <a href="http://localhost/CAMS/index.php/insertAttendance/Present/-1/<?php echo ($classId); ?>" class="btn btn-primary">Take All Present</a>
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
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Attendance Status</th>
                <th>Actions </th>
            </tr>
        </thead>
        <tbody id="attendanceList">
            <?php if(!empty($StudentList)): ?>
                <?php foreach ($StudentList as $attendance): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($attendance['Student_ID']); ?></td>
                        <td><?php echo htmlspecialchars($attendance['Student_Name']); ?></td>
                        <td><?php echo htmlspecialchars($attendance['Student_Contact'])?></td>
                        <td>
                            <span class="badge <?php 
                                    if ($attendance['Attendance_Status'] === 'Present') {
                                        echo 'badge-present';
                                    } elseif ($attendance['Attendance_Status'] === 'Late') {
                                        echo 'badge-late';
                                    } elseif ($attendance['Attendance_Status'] === 'Absent') {
                                        echo 'badge-absent'; 
                                    } else {
                                        echo 'badge-other';
                                    } 
                                    ?>">
                                <?php echo htmlspecialchars($attendance['Attendance_Status'])?>
                            </span>
                        </td>
                        <td>
                            <a href="http://localhost/CAMS/index.php/insertAttendance/Present/<?php echo urlencode($attendance['Course_ID'])?>/<?php echo ($classId); ?>"  class="btn btn-success">Present</a>
                            <a href="http://localhost/CAMS/index.php/insertAttendance/Late/<?php echo urlencode($attendance['Course_ID']); ?>/<?php echo ($classId); ?>"  class="btn btn-warning">Late</a>
                            <a href="http://localhost/CAMS/index.php/insertAttendance/Absent/<?php echo urlencode($attendance['Course_ID']); ?>/<?php echo ($classId); ?>"  class="btn btn-danger">Absent</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No students found on this class.</td>
                </tr>
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