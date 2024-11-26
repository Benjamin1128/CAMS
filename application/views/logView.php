<div class="container mt-2">
    <h1 class="text-center">Log Records of Classroom Attendance Portal</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-12">
                    <div class="card-header">
                        <h5 class="card-title">Filter</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo site_url('log'); ?>" method="POST">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="user_id">Select User Account</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="all" <?php echo ($userid == 'all') ? 'selected' : '';?>>All User</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?php echo $user['Acc_ID']; ?>" <?php echo ($userid == $user['Acc_ID']) ? 'selected' : ''; ?>>
                                                <?php echo $user['User_ID']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="logtype">Select Log Type</label>
                                    <select name="logtype" id="logtype" class="form-control">
                                        <option value="all" <?php echo ($logtype == 'all') ? 'selected' : '' ; ?>>All Log Type</option>
                                        <option value="info" <?php echo ($logtype == 'info') ? 'selected' : '' ; ?>>Info</option>
                                        <option value="error" <?php echo ($logtype == 'error') ? 'selected' : '' ; ?>>Error</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate)?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate)?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Filter Log</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Current Filter</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Target User Account:</strong>
                        <?php echo ($userid === 'all') ? 'All User Accounts' : htmlspecialchars($UseridName) ; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Log Type:</strong>
                        <?php echo ($logtype === 'all') ? 'All Log Type' : htmlspecialchars($logtype) ; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Start Date:</strong>
                        <?php echo $startDate ? htmlspecialchars($startDate) : 'All Past Dates' ; ?>
                    </div>
                    <div class="col-md-3">
                        <strong>End Date:</strong>
                        <?php echo $endDate ? htmlspecialchars($endDate) : 'Today' ; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Log Records</h3>
            </div>
            <div class="card-body">
            <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by name, ID, or contact number">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0)">Username</th>
                            <th onclick="sortTable(1)">Date Time</th>
                            <th onclick="sortTable(2)">Type</th>
                            <th onclick="sortTable(3)">Message</th>
                        </tr>
                    </thead>
                    <tbody id="logTableBody">
                        <?php if (!empty($logging)):?>
                            <?php foreach ($logging as $log): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['User_ID'])?></td>
                                    <td><?php echo htmlspecialchars($log['AcLog_DateTime'])?></td>
                                    <td><?php echo htmlspecialchars($log['AcLog_Type'])?></td>
                                    <td><?php echo htmlspecialchars($log['AcLog_Comment'])?></td>
                                </tr>
                                <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center">No logs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').setAttribute('max', today);
        document.getElementById('end_date').setAttribute('max', today);

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function validateDates() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            if (startDate && endDate && endDate < startDate) {
                alert("End date cannot be earlier than start date.");
                startDateInput.value = '';
            }
        }

        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);
    });

    document.getElementById('searchInput').addEventListener('input', function() {
            var searchTerm = this.value.toLowerCase();
            var rows = document.querySelectorAll('#logTableBody tr')

            rows.forEach(function(row) {
                var cells = row.getElementsByTagName('td');
                var id = cells[0].textContent.toLowerCase();
                var name = cells[1].textContent.toLowerCase();
                var contact = cells[2].textContent.toLowerCase();
                var info = cells[3].textContent.toLowerCase();

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
            if (columnIndex === 1) {
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
