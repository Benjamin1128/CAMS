<div class="container mt-2">
    <h1>Teacher List</h1>
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
                <th>Age</th>
                <th>Gender</th>
                <th>Contact Number</th>
                <th>Salary (RM)</th>
                <th>Class Handled</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="teacherTableBody">
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let allData = [];
        let filteredData = [];
        let currentPage = 1;
        const rowsPerPage = 10;

        function fetchData() {
            fetch('<?php echo site_url('teacher'); ?>?ajax=true')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.teachers) {
                        allData = data.teachers;
                        filteredData =allData;
                        renderTable(currentPage);
                    } else {
                        console.error('Data Structure error:', data);
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                });
        }

        function renderTable(page) {
            const offset = (page - 1) * rowsPerPage;
            const pageData = filteredData.slice(offset, offset + rowsPerPage);
            const tableBody = document.getElementById('teacherTableBody');
            tableBody.innerHTML = '';
            pageData.forEach(teacher => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${teacher.Teacher_ID}</td>
                    <td>${teacher.Teacher_Name}</td>
                    <td>${teacher.Teacher_Age}</td>
                    <td>${teacher.Teacher_Gender}</td>
                    <td>${teacher.Teacher_Contact}</td>
                    <td>${teacher.Salary}</td>
                    <td>${teacher.NumberOfClasses}</td>
                    <td>
                        <a href="http://localhost/CAMS/index.php/editTeacher/${teacher.Teacher_ID}" class="btn btn-warning">Raise</a>
                        <a href="http://localhost/CAMS/index.php/removeTeacher/${teacher.Teacher_ID}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    </td>
                `;
                tableBody.appendChild(row);
            })
        }

        fetchData();
    })
</script>