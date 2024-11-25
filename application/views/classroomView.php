<div class="container mt-2">
    <h1>Classroom Management</h1>
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
                <th>Total Student</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="classroomTableBody">
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
        fetch('<?php echo site_url('classroom');?>?ajax=true')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.classrooms) {
                    allData = data.classrooms;
                    filteredData = allData;
                    renderTable(currentPage);
                } else {
                    console.error('Data structure error:', data);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
    }

    function renderTable(page) {
        const offset = (page - 1) * rowsPerPage;
        const pageData = filteredData.slice(offset, offset + rowsPerPage);
        const tableBody = document.getElementById('classroomTableBody');
        tableBody.innerHTML = '';
        if (pageData.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="4" style="text-align: center;">No classroom found</td>`;
            tableBody.appendChild(row);
        } else {
        pageData.forEach(classroom => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${classroom.Class_Subject}</td>
                <td>${classroom.Teacher_Name}</td>
                <td>${classroom.Total_Student}</td>
                <td>
                    <a href="http://localhost/CAMS/index.php/editClassroom/${classroom.Class_ID}" class="btn btn-success">Edit</a>
                    <?php if (!$teacher_id): ?>
                    <a href="http://localhost/CAMS/index.php/removeClassroom/${classroom.Class_ID}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    <?php endif; ?>
                </td>
            `;
            tableBody.appendChild(row);
        });}
    }

    fetchData();
})
</script>