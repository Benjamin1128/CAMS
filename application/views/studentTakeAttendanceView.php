<div class="container pt-2">
    <h1 class="text-center">Today's Attendance</h1>
    <div class="form-group">
        <label for="attendance_date">Today's Date:</label>
        <label id="attendance_date" class="date-label"></label>
        <label for="attendance_time"  style="margin-left: 650px">Current Time:</label>
        <label id="attendance_time" class="date-label"></label>
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

    

    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by class name or attendance status...">
    <table class="table table-striped">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Class Name</th>
                <th onclick="sortTable(1)">Responsible Teacher</th>
                <th onclick="sortTable(2)">Class Begin Time</th>
                <th onclick="sortTable(3)">Class End Time</th>
                <th onclick="sortTable(4)">Attendance Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="attendanceList">
            <?php if(!empty($classrooms)): ?>
                <?php foreach ($classrooms as $classroom): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($classroom['Class_Subject']); ?></td>
                        <td><?php echo htmlspecialchars($classroom['Teacher_Name']); ?></td>
                        <?php
							date_default_timezone_set('Asia/Kuala_Lumpur');
							$currentTime = new DateTime();
							$startTime = !empty($classroom['Class_StartTime']) ? new DateTime($classroom['Class_StartTime']) : null;
							$endTime = !empty($classroom['Class_EndTime']) ? new DateTime($classroom['Class_EndTime']) : null;
							$formattedStartTime = $startTime ? $startTime->format('g:i A') : 'No Time Provided';
							$formattedEndTime = $endTime ? $endTime->format('g:i A') : 'No Time Provided';
							$isButtonDisabled = false;
							if ($startTime && $endTime) {
								$earlyOrLateInterval = new DateInterval('PT10M');
								$earlyTime = clone $startTime;
								$earlyTime->sub($earlyOrLateInterval);
								if ($currentTime < $earlyTime || $currentTime > $endTime) {
									$isButtonDisabled = true;
								}
							}
							?>
                        <td><?php echo htmlspecialchars($formattedStartTime); ?></td>
                        <td><?php echo htmlspecialchars($formattedEndTime); ?></td>
                        <td>
                            <span class="badge <?php 
                                    if ($classroom['StudentAttendanceStatus'] === 'Present') {
                                        $isButtonDisabled = true;
                                        echo 'badge-present';
                                    } elseif ($classroom['StudentAttendanceStatus'] === 'Late') {
                                        $isButtonDisabled = true;
                                        echo 'badge-late';
                                    } elseif ($classroom['StudentAttendanceStatus'] === 'Absent') {
                                        $isButtonDisabled = true;
                                        echo 'badge-absent'; 
                                    } else {
                                        echo 'badge-other';
                                    } 
                                    ?>">
                                <?php echo htmlspecialchars($classroom['StudentAttendanceStatus']); ?>
                            </span>
                        </td>
                        <td>
                        <button
                            onclick="window.location.href='<?php echo 'http://localhost/CAMS/index.php/studentInsertAttendance/' . urlencode($classroom['Class_ID']); ?>'" 
                            class="btn btn-primary" 
                            <?php echo $isButtonDisabled ? 'disabled' : ''; ?>>
                                Take Attendance
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No classrooms found.</td>
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

    document.getElementById('searchInput').addEventListener('input', function() {
        var searchTerm = this.value.toLowerCase();
        var rows = document.querySelectorAll('#attendanceList tr')

        rows.forEach(function(row) {
            var cells = row.getElementsByTagName('td');
            var id = cells[0].textContent.toLowerCase();
            var name = cells[1].textContent.toLowerCase();


            if (id.includes(searchTerm) || name.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function sortTable(columnIndex) {
        const table = document.querySelector('.table');
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const isAscending = table.querySelectorAll('thead th')[columnIndex].classList.toggle('asc');
        const parseTime = (timeText => {
            const [time, modifier] = timeText.split(' ');
            let [hours, minutes] = time.split(':').map(Number);
            if (modifier === 'PM' && hours < 12) hours += 12;
            if (modifier === 'AM' && hours === 12) hours = 0;
            return hours * 60 + minutes;
        })
        const compare = (a,b) => {
            const aText = a.children[columnIndex].textContent.trim();
            const bText = b.children[columnIndex].textContent.trim();
            if (columnIndex === 5) {
                const aTime = parseTime(aText);
                const bTime = parseTime(bText);
                return isAscending ? aTime - bTime : bTime - aTime;
            } else {
                return isAscending ? aText.localeCompare(bText) : bText.localeCompare(aText);
            }
        };
        rows.sort(compare);
        table.querySelector('tbody').append(...rows);
    }

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