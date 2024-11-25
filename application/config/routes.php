<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'home/showLoginView';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['userLogin'] = 'home/userLogin';
$route['userLogout'] = 'home/userLogout';

$route['student'] = 'student/index';
$route['newStudent'] = 'student/newStudent';
$route['insertStudent'] = 'student/insertStudent';
$route['editStudent/(:any)'] = 'student/editStudent/$1';
$route['updateStudent'] = 'student/updateStudent';
$route['removeStudent/(:any)'] = 'student/removeStudent/$1';

$route['teacher'] = 'teacher/index';
$route['newTeacher'] = 'teacher/newTeacher';
$route['insertTeacher'] = 'teacher/insertTeacher';
$route['editTeacher/(:any)'] = 'teacher/editTeacher/$1';
$route['updateTeacher'] = 'teacher/updateTeacher';
$route['removeTeacher/(:any)'] = 'teacher/removeTeacher/$1';

$route['classroom'] = 'classroom/index';
$route['newClassroom'] = 'classroom/newClassroom';
$route['insertClassroom'] = 'classroom/insertClassroom';
$route['editClassroom/(:any)'] = 'classroom/editClassroom/$1';
$route['updateClassroom'] = 'classroom/updateClassroom';
$route['removeClassroom/(:any)'] = 'classroom/removeClassroom/$1';

$route['profile'] = 'profile/index';
$route['updateProfile'] = 'profile/updateProfile';
$route['verifyPwd'] = 'profile/verifyPwd';
$route['updatePwd'] = 'profile/updatePwd';

$route['attendance'] = 'attendance/index';
$route['takeAttendance/(:any)'] = 'attendance/takeAttendance/$1';
$route['insertAttendance/(:any)/(:any)/(:any)'] = 'attendance/insertAttendance/$1/$2/$3';
$route['pastAttendance'] = 'attendance/pastAttendance';
$route['takePastAttendance/(:any)/(:any)'] = 'attendance/takePastAttendance/$1/$2';
$route['insertPastAttendance/(:any)/(:any)/(:any)/(:any)'] = 'attendance/insertPastAttendance/$1/$2/$3/$4';
$route['studentInsertAttendance/(:any)'] = 'attendance/studentInsertAttendance/$1';

$route['report'] = 'report/index';
$route['createExcel'] = 'report/createExcel';

$route['log'] = 'log/index';