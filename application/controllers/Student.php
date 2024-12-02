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
        if ($this->input->get('ajax')) {
            // Get parameters from DataTables
            $draw = intval($this->input->get('draw'));
            $start = intval($this->input->get('start')); // Starting row index
            $length = intval($this->input->get('length')); // Number of rows per page
            $search = $this->input->get('search')['value']; // Search query
            $order_column = $this->input->get('order_column'); // Column index
            $order_dir = $this->input->get('order_dir'); // Sorting direction (asc or desc)

            $students = $this->StudentModel->getStudents($start, $length, $search, $order_column, $order_dir);
            $totalFiltered = $this->StudentModel->getFilteredStudentsCount($search); // Count of filtered rows
            $totalStudents = $this->StudentModel->getTotalStudents();

            $response = [
                'draw' => $draw,
                'recordsTotal' => $totalStudents,
                'recordsFiltered' => $totalFiltered,
                'data' => $students,
            ];

            echo json_encode($response);
            exit;
        }

        $data['user_id'] = $this->session->userdata('user_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['active_page'] = 'student';

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

