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
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by name, ID, or contact number">
    <table class="table table-striped">
        <thead>
            <tr>
                <th onclick="sortTable(0)">ID</th>
                <th onclick="sortTable(1)">Name</th>
                <th onclick="sortTable(2)">Age</th>
                <th onclick="sortTable(3)">Gender</th>
                <th onclick="sortTable(4)">Contact Number</th>
                <th onclick="sortTable(5)">Salary (RM)</th>
                <th onclick="sortTable(6)">Class Handled</th>
                <th onclick="sortTable(7)">Actions</th>
            </tr>
        </thead>
        <tbody id="teacherTableBody">
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
            filteredData = allData.filter(teacher => 
                teacher.Teacher_ID.toLowerCase().includes(searchTerm) ||
                teacher.Teacher_Name.toLowerCase().includes(searchTerm) ||
                teacher.Teacher_Contact.toLowerCase().includes(searchTerm)
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
        if (columnIndex === 7) {
            const aDate = new Date(aText);
            const bDate = new Date(bText);
            return isAscending ? aDate - bDate : bDate - aDate;
        } else if (columnIndex === 5) {
            const aNumber = parseFloat(aText.replace(/[^0-9.-]+/g, ''));
            const bNumber = parseFloat(bText.replace(/[^0-9.-]+/g, ''));
            return isAscending ? aNumber - bNumber : bNumber - aNumber;
        } else {
            return isAscending ? aText.localeCompare(bText) : bText.localeCompare(aText);
        }
    };
    rows.sort(compare);
    table.querySelector('tbody').append(...rows);
}
</script>