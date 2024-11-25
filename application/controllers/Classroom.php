<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Classroom extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('StudentModel');
        $this->load->model('TeacherModel');
        $this->load->model('ClassroomModel');
        $this->load->model('LogModel');
    }
    public function index()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'classroom';
        $classrooms = $this->ClassroomModel->getAllClassrooms();
        $totalClassrooms = $this->ClassroomModel->getTotalClassroom();
        $totalPages = ceil($totalClassrooms / 10);
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'classrooms' => $classrooms,
                'totalPages' => $totalPages,
            ]);
            exit;
        }
        $this->load->view('header', $data);
        $this->load->view('classroomView');
        $this->load->view('footer');
    }
    public function newClassroom()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'newClassroom';
        $data['teacherCL'] = $this->TeacherModel->getTeacherWithClassCount();
        $data['studentCL'] = $this->StudentModel->getStudentWithClassCount();
        $this->load->view('header', $data);
        $this->load->view('newClassroomView');
        $this->load->view('footer');
    }
    public function insertClassroom()
    {
        $subjectName = $this->input->post('class_subject');
        $teacherId = $this->input->post('teacher_id');
        $studentsList = $this->input->post('students');
        $classStartTime = $this->input->post('class_StartTime');
        $classEndTime = $this->input->post('class_EndTime');
        $actResult = $this->ClassroomModel->insertClassroom($subjectName, $teacherId, $studentsList, $classStartTime, $classEndTime);
        $logtype = 'Info';
        $logmsg = '';
        $loguser = $this->session->userdata('user_id');
        if ($actResult) {
            $logtype = "Info";
            $logmsg = 'Classroom subject: '.$subjectName.' added';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }
        else {
            $logtype = "Error";
            $logmsg = 'Failed to add classroom subject: '.$subjectName;
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }

        redirect('classroom');
    }
    public function editClassroom($classId) 
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'newClassroom';
        $data['teacherCL'] = $this->TeacherModel->getTeacherWithClassCount();
        $data['studentCL'] = $this->StudentModel->getStudentWithClassCount();
        $data['classroomData'] = $this->ClassroomModel->getClassroomById($classId);
        $data['classRoomId'] = $classId;
        $this->load->view('header', $data);
        $this->load->view('editClassroomView');
        $this->load->view('footer');
    }
    public function updateClassroom()
    {
        $subjectName = $this->input->post('class_subject', TRUE);
        $teacherId = $this->input->post('teacher_id', TRUE);
        $studentsList = $this->input->post('students', []);
        $classId = $this->input->post('class_id', TRUE);
		$classStartTime = $this->input->post('class_StartTime',TRUE);
		$classEndTime = $this->input->post('class_EndTime',TRUE);
        $actResult = $this->ClassroomModel->updateClassroom($subjectName, $teacherId, $studentsList, $classId,$classStartTime,$classEndTime);
        $logtype = 'Info';
        $logmsg = '';
        $loguser = $this->session->userdata('user_id');
        if ($actResult) {
            $logtype = "Info";
            $logmsg = 'Classroom id: '.$classId.' information updated';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }
        else {
            $logtype = "Error";
            $logmsg = 'Failed to update classroom info';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }

        redirect('classroom');
    }
    public function removeClassroom($classId)
    {
        $actResult = $this->ClassroomModel->removeClassroom($classId);
        $logtype = 'Info';
        $logmsg = '';
        $loguser = $this->session->userdata('user_id');
        if ($actResult) {
            $logtype = "Info";
            $logmsg = 'Classroom id: '.$classId.' removed';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }
        else {
            $logtype = "Error";
            $logmsg = 'Failed to remove classroom';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }

        redirect('classroom');
    }
}


?>