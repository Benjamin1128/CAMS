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
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by class name or attendance status...">
    <table class="table table-striped">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Class Name</th>
                <th onclick="sortTable(1)">Responsible Teacher</th>
                <th onclick="sortTable(2)">Attendance Status</th>
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