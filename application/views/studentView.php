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

    <div class="row" style="margin-top: 2%;">
        <div class="col-sm-12">
            <table id="studentTable" class="table table-striped table-bordered" style="width:100%;">
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
                <tbody>
            
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$('#studentTable').DataTable({
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
        url: '<?php echo site_url('student'); ?>?ajax=true',
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
        { data: 'Student_ID' },
        { data: 'Student_Name' },
        { data: 'Student_Age' },
        { data: 'Student_Gender' },
        { data: 'Student_Contact' },
        <?php if (!$teacher_id): ?>
        {
            data: null,
            render: function(data, type, row) {
                return `
                    <a href="http://localhost/CAMS/index.php/editStudent/${row.Student_ID}" class="btn btn-success">Edit</a>
                    <a href="http://localhost/CAMS/index.php/removeStudent/${row.Student_ID}" class="btn btn-danger">Delete</a>
                `;
            }
        }
        <?php endif; ?>
    ],
    <?php if (!$teacher_id): ?>
    columnDefs: [
        { targets: [5], orderable: false }
    ]
    <?php endif; ?>
});


</script>