<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Attendance extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('StudentModel');
        $this->load->model('TeacherModel');
        $this->load->model('ClassroomModel');
        $this->load->model('AttendanceModel');
        $this->load->model('LogModel');
    }
    public function index()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $tempStudId = $this->session->userdata('student_id');
        $data['active_page'] = 'attendance';
        $classrooms = $this->ClassroomModel->getAllClassrooms();
        $totalClassroom = $this->ClassroomModel->getTotalClassroom();
        foreach ($classrooms as &$classroom) 
        {
            $classId = $classroom['Class_ID'];
            $studentsWithoutAttendance = $this->AttendanceModel->get_student_without_attendance_record($classId);
            $classroom['AttendanceStatus'] = $studentsWithoutAttendance ? 'Unfinished' : 'Complete';

            if ($tempStudId !== null) {
                $studentAttendace = $this->AttendanceModel->getStudentAttendanceStatus($tempStudId, $classId);
                $classroom['StudentAttendanceStatus'] = $studentAttendace;
            }
        }
        unset($classroom);
        $totalPages = ceil($totalClassroom / 10);
        usort($classrooms, function($a, $b) {
            if ($a['AttendanceStatus'] === $b['AttendanceStatus']) {
                return 0;
            }
            return ($a['AttendanceStatus'] === 'Unfinished') ? -1 : 1;
        });
        $viewData = [
            'classrooms' => $classrooms,
            'totalPages' => $totalPages,
        ];
        $this->load->view('header', $data);
        if ($tempStudId !== null) 
        {
            $this->load->view('studentTakeAttendanceView', $viewData); 
        } else {
            $this->load->view('attendanceView', $viewData);
        }
        $this->load->view('footer');
    }
    public function takeAttendance($classId)
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata['student_id'];
        $data['teacher_id'] = $this->session->userdata['teacher_id'];
        $data['active_page'] = 'attendance';
        $StudentList = $this->AttendanceModel->get_student_by_class_id($classId);
        $classDetail = $this->AttendanceModel->get_class_and_teacher_details($classId);
        $viewData = [
            'StudentList' => $StudentList,
            'classDetail' => $classDetail,
            'classId' => $classId,
        ];
        $this->load->view('header', $data);
        $this->load->view('takeAttendanceView', $viewData);
        $this->load->view('footer');
    }
    public function insertAttendance($StudStat, $courseId, $classId)
    {
        if ($courseId != '-1' && $StudStat != '' && $classId != '')
        {
            $actResult = $this->AttendanceModel->insertAttendance($courseId, $StudStat, $classId);
            $logtype = 'Info';
            $logmsg = '';
            $loguser = $this->session->userdata('user_id');
            if ($actResult) {
                $logtype = "Info";
                $logmsg = 'Taken attendance for course id: '.$courseId.' where status is '.$StudStat;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            else {
                $logtype = "Error";
                $logmsg = 'Failed to take for course id: '.$courseId.' where status is '.$StudStat;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            redirect('takeAttendance/' . urlencode($classId));
        }
        elseif ($courseId == '-1' && $StudStat == 'Present') 
        {
            $actResult = $this->AttendanceModel->takeAllAttendance($classId, $StudStat);
            $logtype = 'Info';
            $logmsg = '';
            $loguser = $this->session->userdata('user_id');
            if ($actResult) {
                $logtype = "Info";
                $logmsg = 'Success take all student attendance status as present for class id: '.$classId;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            else {
                $logtype = "Error";
                $logmsg = 'Failed to take all student attendance status as present for class id: '.$classId;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            redirect('takeAttendance/' . urlencode($classId));
        } else {
            redirect('takeAttendance/' . urlencode($classId));
        }
    }
    public function pastAttendance()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'pastAttendance';
        $today = new DateTime();
        $yesterday = $today->modify('-1 day');
        $lastDay = $yesterday->format('Y-m-d');
        $pastDate = $this->input->get('pastdate');
        if (!isset($pastDate) || $pastDate === '') {
            $pastDate = $lastDay;
        }
        $classrooms = $this->ClassroomModel->getAllClassrooms();
        $totalClassroom = $this->ClassroomModel->getTotalClassroom();
        foreach ($classrooms as &$classroom) 
        {
            $classId = $classroom['Class_ID'];
            $studentsWithoutAttendance = $this->AttendanceModel->getStudentWithoutAttendRePast($classId, $pastDate);
            $classroom['AttendanceStatus'] = $studentsWithoutAttendance ? 'Unfinished' : 'Complete';
        }
        unset($classroom);
        $totalPages = ceil($totalClassroom / 10);
        usort($classrooms, function($a, $b) {
            if ($a['AttendanceStatus'] === $b['AttendanceStatus']) {
                return 0;
            }
            return ($a['AttendanceStatus'] === 'Unfinished') ? -1 : 1;
        });
        $viewData = [
            'classrooms' => $classrooms,
            'totalPages' => $totalPages,
            'pastDate' => $pastDate,
        ];
        $this->load->view('header', $data);
        $this->load->view('pastAttendanceView', $viewData);
        $this->load->view('footer');
    }
    public function takePastAttendance($pastDate, $classId)
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata['student_id'];
        $data['teacher_id'] = $this->session->userdata['teacher_id'];
        $data['active_page'] = 'pastAttendance';
        $today = new DateTime();
        $yesterday = $today->modify('-1 day');
        $lastDay = $yesterday->format('Y-m-d');
        if (!isset($pastDate) || $pastDate === '') {
            $pastDate = $lastDay;
        }
        $StudentList = $this->AttendanceModel->getPastStudentsByClassId($pastDate, $classId);
        $classDetail = $this->AttendanceModel->get_class_and_teacher_details($classId);
        $viewData = [
            'StudentList' => $StudentList,
            'classDetail' => $classDetail,
            'classId' => $classId,
            'pastDate' => $pastDate,
        ];
        $this->load->view('header', $data);
        $this->load->view('takePastAttendanceView', $viewData);
        $this->load->view('footer');
    }
    public function insertPastAttendance($pastDate, $StudStat, $courseId, $classId)
    {
        if ($courseId != '-1' && $StudStat != '' && $classId != '')
        {
            $actResult = $this->AttendanceModel->insertPastAttendance($courseId, $StudStat, $pastDate);
            $logtype = 'Info';
            $logmsg = '';
            $loguser = $this->session->userdata('user_id');
            if ($actResult) {
                $logtype = "Info";
                $logmsg = 'Attendance taken for course id: '.$courseId.' where status is '.$StudStat.' for the date: '.$pastDate;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            else {
                $logtype = "Error";
                $logmsg = 'Failed to take attendance for course id: '.$courseId.' where status is '.$StudStat.' for the date: '.$pastDate;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            redirect('takePastAttendance/' . urlencode($pastDate) . '/' . urlencode($classId));
        }
        elseif ($courseId == '-1' && $StudStat == 'Present') 
        {
            $actResult = $this->AttendanceModel->takeAllPastAttendance($classId, $StudStat, $pastDate);
            $logtype = 'Info';
            $logmsg = '';
            $loguser = $this->session->userdata('user_id');
            if ($actResult) {
                $logtype = "Info";
                $logmsg = 'Successfully taken all student attendance status as present for course id: '.$courseId.' on the date: '.$pastDate;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            else {
                $logtype = "Error";
                $logmsg = 'Failed to take all student attendance status as present for course id: '.$courseId.' on the date: '.$pastDate;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            redirect('takePastAttendance/' . urlencode($pastDate) . '/' .  urlencode($classId));
        } else {
            redirect('takePastAttendance/' . urlencode($pastDate) . '/' .  urlencode($classId));
        }
    }
    public function studentInsertAttendance($classId)
    {
        $studentId = $this->session->userdata('student_id');
        if ($classId != '') {
            $data = $this->ClassroomModel->getClassroomById($classId);
            $classroom = isset($data['classroom']) ? $data['classroom'] : null;
            $studentStatus = $this->AttendanceModel->CheckClassTime($classroom);
            $actResult = $this->AttendanceModel->StudentInsertAttendance($classId, $studentId, $studentStatus);
            $logtype = 'Info';
            $logmsg = '';
            $loguser = $this->session->userdata('user_id');
            if ($actResult) {
                $logtype = "Info";
                $logmsg = 'Self taken an attendance for class id: '.$classId;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            else {
                $logtype = "Error";
                $logmsg = 'Failed to self taken an attendance for class id: '.$classId;
                $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
            }
            redirect('attendance');
        }
    }
}

?>