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
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by name, ID, or contact number">
    <table class="table table-striped">
        <thead>
            <tr>
                <th onclick="sortTable(0)">ID</th>
                <th onclick="sortTable(1)">Name</th>
                <th onclick="sortTable(2)">Age</th>
                <th onclick="sortTable(3)">Gender</th>
                <th onclick="sortTable(4)">Contact Number</th>
                <?php if (!$teacher_id): ?>
                <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody id="studentTableBody">
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
        filteredData = allData.filter(student => 
            student.Student_ID.toLowerCase().includes(searchTerm) ||
            student.Student_Name.toLowerCase().includes(searchTerm) ||
            student.Student_Contact.toLowerCase().includes(searchTerm)
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