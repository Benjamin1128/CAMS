<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Classroom Attendance Portal</title>
  <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/adminlte.min.css');?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/fontawesome-free/css/all.min.css');?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css');?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .toggle-sidebar {
      cursor: pointer;
    }
    .checkbox-container {
      max-height: 300px;
      overflow-y: auto;
      border: 1px solid #ddd;
      padding: 15px
    }
    .checkbox-list {
      display: flex;
      flex-wrap: wrap;
    }
    .checkbox-item {
      width: 50%;
      box-sizing: border-box;
      padding: 5px;
    }
    .card {
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      margin-bottom: 20px;
    }
    .homecard {
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      margin-bottom: 50px;
      margin-top: 50px;
    }
    .card-header {
      background-color: #f4f6f9;
      border-bottom: 1px solid #e3e6f0;
    }
    .card-body {
      padding: 20px;
    }
    .card-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0;
      margin-right: 2%;
    }
    .card-text {
      font-size: 1.5rem;
      font-weight: 700;
      color: #007bff;
    }
    .card-icon {
      font-size: 2rem;
      color: #007bff;
    }
    .ProfileContainer {
      padding: 2%;
      align-items: center;
      justify-content: center;
    }
    .form-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 2%;
    }
    .form-row > div {
      margin-right: 20px;
    }
    .form-row > label {
      margin-bottom: 0;
    }
    th {cursor: pointer}
    .badge {
      display: inline-block;
      padding: 0.5em 1em;
      border-radius: 0.5em;
      font-size: 0.875em;
      color: white;
      text-align: center;
    }
    .badge-complete{
      background-color: green;
    }
    .badge-incomplete {
      background-color: red;
    }
    .badge-present {
      background-color: green;
    }
    .badge-late {
      background-color: #D5B60A;
    }
    .badge-absent {
      background-color: red;
    }
    .badge-other {
      background-color: grey;
    }
    .reportBody {
      padding: 2%;
      align-items: center;
      justify-content: center;
    }
    .chartBigContainer {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .VisualizeInfo {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    .pagination {
      display: flex;
      justify-content: center;
      padding-left: 0;
      list-style: none;
    }
    .page-item {
      margin: 0 2px;
    }
    .page-link {
      display: block;
      padding: 0.5rem 0.75rem;
      margin: 0;
      line-height: 1.25;
      text-align: center;
      text-decoration: none;
      background-color: #fff;
      border: 1px solid #dee2d6;
      color: #007bff;
    }
    .page-link {
      background-color: #007bff;
      border-color: #007bff;
      color: #fff;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
   <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link toggle-sidebar">
            <i class="fas fa-bars"></i>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($active_page == 'home') ? 'active' : ''; ?>" href="<?php echo site_url('home'); ?>">Home</a>
        </li>
        <?php if (!$student_id && !$teacher_id): ?>
        <li class="nav-item">
          <a class="nav-link <?php echo ($active_page == 'newStudent') ? 'active' : ''; ?>" href="<?php echo site_url('newStudent'); ?>">New Student</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($active_page == 'newTeacher') ? 'active' : ''; ?>" href="<?php echo site_url('newTeacher'); ?>">New Teacher</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($active_page == 'newClassroom') ? 'active' : ''; ?>" href="<?php echo site_url('newClassroom'); ?>">New Classroom</a>
        </li>
        <?php endif; ?>
      </ul>
    </nav>

  <!-- Side Bar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo base_url('assets/dist/img/AdminLTELogo.png'); ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <?php if ($student_id): ?>
            <a href="<?php echo site_url('profile');?>" class="d-block"><?php echo ($student_id); ?></a>
            <?php elseif ($teacher_id): ?>
              <a href="<?php echo site_url('profile');?>" class="d-block"><?php echo ($teacher_id); ?></a>
          <?php else: ?>
            <span class="d-block text-white">Admin</span>
          <?php endif; ?>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Student -->  
          <?php if (!$student_id): ?>
          <li class="nav-item">
            <a href="<?php echo site_url('student'); ?>" class="nav-link <?php echo ($active_page == 'student') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-graduation-cap"></i>
              <p>Students</p>
            </a>
          </li>

          <!-- Teacher -->
          <?php if (!$teacher_id): ?>
          <li class="nav-item">
            <a href="<?php echo site_url('teacher'); ?>" class="nav-link <?php echo ($active_page == 'teacher') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-user"></i>
              <p>Teachers</p>
            </a>
          </li>
          <?php endif; ?>

          <!-- Courses -->
          <li class="nav-item">
            <a href="<?php echo site_url('classroom'); ?>" class="nav-link <?php echo ($active_page == 'classroom') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-chalkboard"></i>
              <p>Classroom</p>
            </a>
          </li>
          <?php endif; ?>

          <!-- Attendance -->
          <?php if ($student_id || $teacher_id): ?>
            <li class="nav-item">
            <a href="<?php echo site_url('attendance'); ?>" class="nav-link <?php echo ($active_page == 'attendance') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-calendar-check"></i>
              <p>Attendance</p>
            </a>
          </li>
          <?php if ($teacher_id): ?>
            <li class="nav-item">
            <a href="<?php echo site_url('pastAttendance'); ?>" class="nav-link <?php echo ($active_page == 'pastAttendance') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-calendar-day"></i>
              <p>Past Attendance</p>
            </a>
          </li>
          <?php endif;?>
          <li class="nav-item">
            <a href="<?php echo site_url('profile'); ?>" class="nav-link <?php echo ($active_page == 'profile') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-user"></i>
              <p>Profile</p>
            </a>
          </li>
          <?php endif; ?>
            
          <?php if (!$student_id): ?>
          <li class="nav-item">
            <a href="<?php echo site_url('report'); ?>" class="nav-link <?php echo ($active_page == 'report') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>Report</p>
            </a>
          </li>
          <?php endif; ?>

          <?php if (!$student_id && !$teacher_id): ?>
          <li class="nav-item">
            <a href="<?php echo site_url('log'); ?>" class="nav-link <?php echo ($active_page == 'log') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>Log</p>
            </a>
          </li>
          <?php endif; ?>

          <li class="nav-item">
            <a href="<?php echo site_url('userLogout'); ?>" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
  <!-- Content Wrapper -->
  <div class="content-wrapper">
    