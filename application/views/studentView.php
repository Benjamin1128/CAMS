<div class="container mt-2">
    <h1>Student List</h1>
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
                <?php if (!$teacher_id): ?>
                <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody id="studentTableBody">
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
        fetch('<?php echo site_url('student');?>?ajax=true')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.students) {
                    allData = data.students;
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
        const tableBody = document.getElementById('studentTableBody');
        tableBody.innerHTML = '';
        if (pageData.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="6" style="text-align: center;">No student found</td>`;
            tableBody.appendChild(row);
        } else {
        pageData.forEach(student => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${student.Student_ID}</td>
                <td>${student.Student_Name}</td>
                <td>${student.Student_Age}</td>
                <td>${student.Student_Gender}</td>
                <td>${student.Student_Contact}</td>
                <?php if (!$teacher_id): ?>
                <td>
                    <a href="http://localhost/CAMS/index.php/editStudent/${student.Student_ID}" class="btn btn-success">Edit</a>
                    <a href="http://localhost/CAMS/index.php/removeStudent/${student.Student_ID}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                </td>
                <?php endif; ?>
            `;
            tableBody.appendChild(row);
        });}
    }

    fetchData();
})
</script>