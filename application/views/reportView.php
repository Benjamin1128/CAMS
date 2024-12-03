<div class="container pt-2">
    <h1 class="text-center">Report of Student Attendance Management System</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-12">
                    <div class="card-header">
                        <h5 class="card-title">Filter</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo site_url('report'); ?>" method="POST">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="class_id">Select Classroom</label>
                                    <select name="class_id[]" id="class_id" class="form-control" multiple>
                                        <option value="all" <?php echo (in_array('all', $classIds)) ? 'selected' : '';?>>All Classrooms</option>
                                        <?php foreach ($classrooms as $class): ?>
                                            <option value="<?php echo htmlspecialchars($class['Class_ID']); ?>" <?php echo (in_array($class['Class_ID'], $classIds)) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($class['Class_Subject']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="class_id">Select Student</label>
                                    <select name="student_id[]" id="student_id" class="form-control" multiple>
                                        <option value="all" <?php echo (in_array('all', $studentIds)) ? 'selected' : '';?>>All Students</option>
                                        <?php foreach ($students as $student): ?>
                                            <option value="<?php echo htmlspecialchars($student['Student_ID']); ?>" <?php echo (in_array($student['Student_ID'], $studentIds)) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($student['Student_Name']); ?>
                                            </option>
                                        <?php endforeach; ?>
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
                            <button type="submit" class="btn btn-primary">Filter Report</button>
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
                        <strong>Classroom:</strong>
                        <?php if (in_array('all', $classIds)) {
                            echo 'All Classrooms';
                        } else {
                            $classNames = [];
                            foreach($classIds as $id)
                            {
                                if (isset($classSubject[$id])) {
                                    $classNames[] = htmlspecialchars($classSubject[$id]);
                                } else {
                                    $classNames[] = htmlspecialchars($id);
                                }
                            } 
                            echo implode(',', $classNames);
                        }
                        ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Student:</strong>
                        <?php if (in_array('all', $studentIds)) {
                            echo 'All Students';      
                        } else {
                            $studentNamesList = [];
                            foreach($studentIds as $id) {
                                if (isset($studentNames[$id])) {
                                    $studentNamesList[] = htmlspecialchars($studentNames[$id]);
                                } else {
                                    $studentNamesList[] = htmlspecialchars($id);
                                }
                            }
                            echo implode(',', $studentNamesList);
                        }    
                        ?>
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
            <div class="reportBody">
                <div class="text-center row">
                    <!-- Total Present Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="card-icon fas fa-check-circle"></i>
                                <h5 class="card-title">Total Present</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?php echo htmlspecialchars($statusCounts['Present']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Late Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="card-icon fas fa-clock"></i>
                                <h5 class="card-title">Total Late</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?php echo htmlspecialchars($statusCounts['Late']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Absent Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="card-icon fas fa-clock"></i>
                                <h5 class="card-title">Total Absent</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?php echo htmlspecialchars($statusCounts['Absent']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Print Report Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="card-icon fas fa-print"></i>
                                <h5 class="card-title">Print Report</h5>
                            </div>
                            <div class="card-body">
                                <button id="downloadPDF" class="btn btn-primary mb-2">Generate Report <i class="fas fa-file-pdf"></i></button>
                                <button id="downloadExcel" class="btn btn-primary mb-2" onclick="window.location.href='<?php echo site_url('createExcel'); ?>'">Generate Excel <i class="fas fa-file-excel"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Attendance Report</h5>
            </div>
            <div class="card-body">
                <div class="VisualizeInfo">
                    <h3>Student Attendance Percentage</h3>
                    <div class="chartBigContainer">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                    <div style="margin-bottom: 5%;"></div>
                    <h3>Stduent Attedance Records</h3>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/plugins/chart.js/Chart.min.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        const attendanceChartCtx = document.getElementById('attendanceChart').getContext('2d');
        new Chart(attendanceChartCtx, {
            type: 'pie',
            data: {
                labels: ['Present', 'Absent', 'Late'],
                datasets: [{
                    label: 'Attendance Status',
                    data: [
                        <?php echo $statusPercentages['Present']; ?>,
                        <?php echo $statusPercentages['Absent']; ?>,
                        <?php echo $statusPercentages['Late']; ?>
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 159, 64, 1)',
                    ],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                            }
                        }
                    }
                }
            }
        });

        const barChartCtx = document.getElementById('barChart').getContext('2d');
        new Chart (barChartCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $monthsJson; ?>,
                datasets: [
                    {
                        label: 'Present',
                        data: <?php echo $presentCountsJson; ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Absent',
                        data: <?php echo $absentCountsJson; ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Late',
                        data: <?php echo $lateCountsJson; ?>,
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: { display: true, text: 'Month' },
                    },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Count' },
                        ticks: { min: 0 },
                    },
                },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Students : ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        document.getElementById('downloadPDF').addEventListener('click', function() {
            Promise.all([
                html2canvas(document.getElementById('attendanceChart')),
                html2canvas(document.getElementById('barChart')),
            ]).then(canvases => {
                const imgData1 = canvases[0].toDataURL('image/png');
                const imgData2 = canvases[1].toDataURL('image/png');

                const docDefinition = {
                    content: [
                        { text: 'Attendance Report', style: 'header'},
                        { text: 'User Info', style: 'sectionTitle', margin: [0, 10], color: 'grey'},
                        {
                            style: 'tableExample',
                            table: {
                                widths: ['*', '*'],
                                body: [
                                    [
                                        { text: 'Generated By:', style: 'label'},
                                        { text: '<?php echo !empty($teacherName) ? htmlspecialchars($teacherName) : 'Admin'; ?>', style: 'value'},
                                    ],
                                    [
                                        { text: 'Generated Date:', style: 'label'},
                                        { text: '<?php echo date('F, j, Y'); ?>', style: 'value'},
                                    ],
                                    [
                                        { text: 'Total Handling Students:', style: 'label'},
                                        { text: '<?php echo !empty($totalStudents) ? htmlspecialchars($totalStudents) : 'Zero' ; ?>', style: 'value'},
                                    ],
                                    [
                                        { text: 'Total Handling Classrooms:', style: 'label'},
                                        { text: '<?php echo !empty($totalClassrooms) ? htmlspecialchars($totalClassrooms) : 'Zero' ; ?>', style: 'value'},
                                    ]
                                ]
                            },
                            layout: 'noBorders'
                        },
                        { text: 'Target Report', style: 'sectionTitle', margin: [0, 10], color: 'grey'},
                        {
                            style: 'tableExample',
                            table: {
                                widths: ['*', '*'],
                                body: [
                                    [
                                        { text: 'Classroom:', style:'label'},
                                        { text: '<?php 
                                            function decodeHtmlEntities($text) {
                                                return html_entity_decode($text, ENT_QUOTES,'UTF-8');
                                            }
                                            if (in_array('all', $classIds)) {
                                                echo 'All Classrooms';
                                            } else {
                                                $classNames = [];
                                                foreach ($classIds as $id) 
                                                {
                                                    if (isset($classSubject[$id])) {
                                                        $classNames[] = decodeHtmlEntities($classSubject[$id]);
                                                    } else {
                                                        $classNames[] = decodeHtmlEntities($id);
                                                    }
                                                }
                                                echo implode(',', $classNames);
                                            }
                                        ?>', style: 'value'}
                                    ],
                                    [
                                        { text: 'Student:', style:'label'},
                                        { text: '<?php 
                                            if (in_array('all', $studentIds)) {
                                                echo 'All Students';
                                            } else {
                                                $studentSubject = [];
                                                foreach ($studentIds as $id) 
                                                {
                                                    if (isset($studentNames[$id])) {
                                                        $studentSubject[] = decodeHtmlEntities($studentNames[$id]);
                                                    } else {
                                                        $studentSubject[] = decodeHtmlEntities($id);
                                                    }
                                                }
                                                echo implode(',', $studentSubject);
                                            }
                                        ?>', style: 'value'}
                                    ],
                                    [
                                        { text: 'Between Date:', style: 'label'},
                                        { text: '<?php echo $startDate ? htmlspecialchars($startDate): 'All Past Dates' ;?> until <?php echo $endDate ? htmlspecialchars($endDate) : 'Today' ; ?>', style: 'value' },
                                    ]
                                ]
                            },
                            layout: 'noBorders'
                        },
                        { text: 'Summarize Info', style: 'sectionTitle', margin: [0, 10], color: 'grey'},
                        {
                            style: 'tableExample',
                            table: {
                                widths: ['*', '*'],
                                body: [
                                    [
                                        { text: 'Total Present:', style: 'label'},
                                        { text: '<?php echo htmlspecialchars($statusCounts['Present']); ?> (<?php echo htmlspecialchars($statusPercentages['Present']) ; ?>%)', style: 'value'},
                                    ],
                                    [
                                        { text: 'Total Absent:', style: 'label'},
                                        { text: '<?php echo htmlspecialchars($statusCounts['Absent']); ?> (<?php echo htmlspecialchars($statusPercentages['Absent']) ; ?>%)', style: 'value'},
                                    ],
                                    [
                                        { text: 'Total Late:', style: 'label'},
                                        { text: '<?php echo htmlspecialchars($statusCounts['Late']); ?> (<?php echo htmlspecialchars($statusPercentages['Late']) ; ?>%)', style: 'value'},       
                                    ],
                                ]
                            },
                            layout: 'noBorders'
                        },
                        { text: 'Visualize Info', style: 'sectionTitle', margin: [0, 10], color: 'grey', pageBreak: 'before'},
                        { text: 'Student Attendance Percentage', style: 'sectionTitle', margin: [0, 10]},
                        { image: imgData1, width: 300, margin: [0, 20], alignment: 'center'},
                        { text: 'Student Attendance Records', style: 'sectionTitle', margin: [0, 10]},
                        { image: imgData2, width: 500, margin: [0, 20], alignment: 'center'},
                    ],
                    styles: {
                        header: { fontSize: 22, bold: true, margin: [0, 20] },
                        sectionTitle: { fontSize: 16, bold: true, margin: [0, 5] },
                        label: { fontSize: 16, bold: true },
                        value: { fontSize: 16 },
                        tableExample: { margin: [0, 20] },
                    }
                };
                pdfMake.createPdf(docDefinition).download('Attendance-Report.pdf');
            }).catch(err => console.error('Error capturing canvas:', err));
        });
    }); 
</script>
