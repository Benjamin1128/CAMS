<div class="container mt-2">
    <h1 class="text-center">Update Daily Attendance</h1>
    <div class="form-row">
        <div class="form-group">
            <label for="attendance_date">Selected Date:</label>
            <label id="attendance_date" class="date-label"><?php echo htmlspecialchars($pastDate); ?></label>
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
        <a href="http://localhost/CAMS/index.php/insertPastAttendance/<?php echo htmlspecialchars($pastDate)?>/Present/-1/<?php echo ($classId); ?>" class="btn btn-primary">Take All Present</a>
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
                <th onclick="sortTable(0)">ID</th>
                <th onclick="sortTable(1)">Name</th>
                <th onclick="sortTable(2)">Contact</th>
                <th onclick="sortTable(3)">Attendance Status</th>
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
                                <?php echo htmlspecialchars($attendance['Attendance_Status']) ? htmlspecialchars($attendance['Attendance_Status'])  : 'No Status'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="http://localhost/CAMS/index.php/insertPastAttendance/<?php echo htmlspecialchars($pastDate)?>/Present/<?php echo urlencode($attendance['Course_ID']); ?>/<?php echo htmlspecialchars ($classId); ?>"  class="btn btn-success">Present</a>
                            <a href="http://localhost/CAMS/index.php/insertPastAttendance/<?php echo htmlspecialchars($pastDate)?>/Late/<?php echo urlencode($attendance['Course_ID']); ?>/<?php echo htmlspecialchars ($classId); ?>"  class="btn btn-warning">Late</a>
                            <a href="http://localhost/CAMS/index.php/insertPastAttendance/<?php echo htmlspecialchars($pastDate)?>/Absent/<?php echo urlencode($attendance['Course_ID']); ?>/<?php echo htmlspecialchars ($classId); ?>"  class="btn btn-danger">Absent</a>
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
    function sortTable(columnIndex) {
        const table = document.querySelector('.table');
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const isAscending = table.querySelectorAll('thead th')[columnIndex].classList.toggle('asc');
        const compare = (a,b) => {
            const aText = a.children[columnIndex].textContent.trim();
            const bText = b.children[columnIndex].textContent.trim();
            if (columnIndex === 5) {
                const aDate = new Date(aText);
                const bDate = new Date(bText);
                return isAscending ? aDate - bDate : bDate - aDate;
            } else {
                return isAscending ? aText.localeCompare(bText) : bText.localeCompare(aText);
            }
        };
        rows.sort(compare);
        table.querySelector('tbody').append(...rows);
    }
</script>