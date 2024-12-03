<div class="container pt-2">
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
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by name, ID, or contact number">
    <table class="table table-striped">
        <thead>
            <tr>
                <th onclick="sortTable(0)">Class Name</th>
                <th onclick="sortTable(1)">Responsible Teacher</th>
                <th onclick="sortTable(2)">Total Student</th>
                <th onclick="sortTable(3)">Actions</th>
            </tr>
        </thead>
        <tbody id="classroomTableBody">
        </tbody>
    </table>
    <nav aria-label="Page navigation">
        <ul id="paginationControls" class="pagination justify-content-center"></ul>
    </nav>
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
                    <a href="http://localhost/CAMS/index.php/removeClassroom/${classroom.Class_ID}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this classroom?')">Delete</a>
                    <?php endif; ?>
                </td>
            `;
            tableBody.appendChild(row);
        });}
    }

    function renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / rowsPerPage);
        const paginationContainer = document.getElementById('paginationControls');
            paginationContainer.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const pageItem = document.createElement('li');
                pageItem.className = 'page-item'
                pageItem.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                paginationContainer.appendChild(pageItem);
            }
        }

        document.getElementById('click', function(event) {
            if (event.target.matches('.page-link')) {
                event.preventDefault();
                currentPage = parseInt(event.target.getAttribute('data-page'), 10);
                renderTable(currentPage);
            }
        })

        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filteredData = allData.filter(classroom => 
                classroom.Class_Subject.toLowerCase().includes(searchTerm) ||
                classroom.Teacher_Name.toLowerCase().includes(searchTerm) 
            );
            currentPage = 1;
            renderTable(currentPage);
            renderPagination(filteredData.length);
        })

        fetchData();
});

function sortTable(columnIndex) {
    const table = document.querySelector('.table');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const isAscending = table.querySelectorAll('thead th')[columnIndex].classList.toggle('asc');
    const compare = (a,b) => {
        const aText = a.children[columnIndex].textContent.trim();
        const bText = b.children[columnIndex].textContent.trim();
        if (columnIndex === 3) {
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