<?php
defined("BASEPATH") OR exit("No direct script access allowed");
class Student extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('StudentModel');
        $this->load->model('TeacherModel');
        $this->load->model('ProfileModel');
        $this->load->model('LogModel');

    }
    public function index()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'student';
		$students = $this->StudentModel->getAllStudents();
		$totalStudents = $this->StudentModel->getTotalStudents();
		$totalPages = ceil($totalStudents / 10);	
		if (isset($_GET['ajax'])) {
			header('Content-Type: application/json');
			echo json_encode([
				'students' => $students,
				'totalPages' => $totalPages,
			]);
			exit;
		}
        $this->load->view('header', $data);
        $this->load->view('studentView');
        $this->load->view('footer');
    }
    public function newStudent() 
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'newStudent';
        $this->load->view('header', $data);
        $this->load->view('newStudentView');
        $this->load->view('footer');
    }
    public function insertStudent()
    {
        
        $ic = $this->input->post('ic');
        $name = $this->input->post('name');
        $age = $this->input->post('age');
        $gender = $this->input->post('gender');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $CheckResult = $this->ProfileModel->checkDuplicationUser($ic, $name, $email);
        if ($CheckResult == null) {
            $this->StudentModel->insertStudent($ic, $name, $age, $gender, $phone);
            $this->ProfileModel->createStudentAcc($ic, $name, $email);
            $actResult = true;
        }
        else
        {
            $this->session->set_flashdata('message', [
                'type' => 'error',
                'text' => 'Duplicate email or username. Please try again.',
            ]);
            $actResult = false;
        }
        $logtype = 'Info';
        $logmsg = '';
        $loguser = $this->session->userdata('user_id');
        if ($actResult) {
            $logtype = "Info";
            $logmsg = 'Student: '.$name.' with IC: '.$ic.' added';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }
        else {
            $logtype = "Error";
            $logmsg = 'Failed to add student';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }

        redirect('student');

    }
    public function editStudent($studID)
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'newStudent';
        $data['studentData'] = $this->StudentModel->getStudentById($studID);
        $this->load->view('header', $data);
        $this->load->view('editStudentView');
        $this->load->view('footer');
    }

    public function updateStudent()
    {
        $ic = $this->input->post('ic');
        $name = $this->input->post('name');
        $age = $this->input->post('age');
        $gender = $this->input->post('gender');
        $phone = $this->input->post('phone');
        $actResult = $this->StudentModel->updateStudent($ic, $name, $age, $gender, $phone);
        $logtype = 'Info';
        $logmsg = '';
        $loguser = $this->session->userdata('user_id');
        if ($actResult) {
            $logtype = "Info";
            $logmsg = 'Student IC: '.$ic.' information updated';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }
        else {
            $logtype = "Error";
            $logmsg = 'Failed to update student info';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }

        redirect('student');
    }

    public function removeStudent($studID)
    {
        $actResult = $this->StudentModel->removeStudent($studID);
        $logtype = 'Info';
        $logmsg = '';
        $loguser = $this->session->userdata('user_id');
        if ($actResult) {
            $logtype = "Info";
            $logmsg = 'Student IC: '.$studID.' removed';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }
        else {
            $logtype = "Error";
            $logmsg = 'Failed to remove student';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }

        redirect('student');
    }
}

