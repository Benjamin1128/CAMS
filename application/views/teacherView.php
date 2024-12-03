<div class="container pt-2">
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

    <div class="row" style="margin-top: 2%;">
        <div class="col-sm-12">
            <table id="teacherTable" class="table table-striped table-bordered" style="width:100%;">
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
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $('#teacherTable').DataTable({
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    processing: true,
    serverSide: true,
    order: [[0, 'asc']], // Default order by first column
    ajax: {
        url: '<?php echo site_url('teacher'); ?>?ajax=true',
        type: 'GET',
        data: function(d) {
            if (d.order && d.order.length > 0) {
                d.order_column = d.order[0].column;
                d.order_dir = d.order[0].dir;
            } else {
                d.order_column = 0;
                d.order_dir = 'asc';
            }
        }
    },
    columns: [
        { data: 'Teacher_ID' },
        { data: 'Teacher_Name' },
        { data: 'Teacher_Age' },
        { data: 'Teacher_Gender' },
        { data: 'Teacher_Contact' },
        { data: 'Salary' },
        { data: 'NumberOfClasses' },
        {
            data: null,
            render: function(data, type, row) {
                return `
                    <a href="http://localhost/CAMS/index.php/editTeacher/${row.Teacher_ID}" class="btn btn-warning">Raise</a>
                    <a href="http://localhost/CAMS/index.php/removeTeacher/${row.Teacher_ID}" onclick="return confirm('Are you sure you want to delete this teacher?')" class="btn btn-danger">Delete</a>
                `;
            }
        }
    ],
    columnDefs: [
        { targets: [7], orderable: false }
    ]
});

</script>