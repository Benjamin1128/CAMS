<?php
defined("BASEPATH") OR exit("No direct script access allowed");
class Teacher extends CI_Controller
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

            $teachers = $this->TeacherModel->getTeachers($start, $length, $search, $order_column, $order_dir);
            $totalFiltered = $this->TeacherModel->getFilteredTeachersCount($search); // Count of filtered rows
            $totalTeachers = $this->TeacherModel->getTotalTeachers();

            $response = [
                'draw' => $draw,
                'recordsTotal' => $totalTeachers,
                'recordsFiltered' => $totalFiltered,
                'data' => $teachers,
            ];

            echo json_encode($response);
            exit;
        }

        $data['user_id'] = $this->session->userdata('user_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['active_page'] = 'teacher';

        $this->load->view('header', $data);
        $this->load->view('teacherView');
        $this->load->view('footer');
    }
    // public function index() 
    // {
    //     $data['user_id'] = $this->session->userdata('user_id');
    //     $data['student_id'] = $this->session->userdata('student_id');
    //     $data['teacher_id'] = $this->session->userdata('teacher_id');
    //     $data['active_page'] = 'teacher';
    //     $teachers = $this->TeacherModel->getAllTeachers();
    //     $totalTeachers = $this->TeacherModel->getTotalTeachers();
    //     $totalPages = ceil($totalTeachers / 10);
    //     if (isset($_GET['ajax'])) {
    //         header('Content-Type: application/json');
    //         echo json_encode([
    //             'teachers' => $teachers,
    //             'totalPages' => $totalPages,
    //         ]);
    //         exit;
    //     }
    //     $this->load->view('header', $data);
    //     $this->load->view('teacherView');
    //     $this->load->view('footer');
    // }

    public function newTeacher()
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'newTeacher';
        $this->load->view('header', $data);
        $this->load->view('newTeacherView');
        $this->load->view('footer');
    }

    public function insertTeacher()
    {
        $ic = $this->input->post('ic');
        $name = $this->input->post('name');
        $age = $this->input->post('age');
        $gender = $this->input->post('gender');
        $phone = $this->input->post('phone');
        $email = $this->input->post('email');
        $salary = $this->input->post('salary');
        $CheckResult = $this->ProfileModel->checkDuplicationUser($email, $name, $ic);
        if ($CheckResult == null) {
            $this->TeacherModel->insertTeacher($ic, $name, $age, $gender, $phone, $salary);
            $this->ProfileModel->createTeacherAcc($ic, $name, $email);
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
            $logmsg = 'Teacher: '.$name.' with IC: '.$ic.' added';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }
        else {
            $logtype = "Error";
            $logmsg = 'Failed to add teacher';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }

        redirect('teacher');
    }
    public function editTeacher($teacherID) 
    {
        $data['user_id'] = $this->session->userdata('user_id');
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'newTeacher';
        $data['teacherData'] = $this->TeacherModel->getTeacherById($teacherID);
        $this->load->view('header', $data);
        $this->load->view('editTeacherView');
        $this->load->view('footer');
    }

    public function updateTeacher()
    {
        $ic = $this->input->post('ic');
        $name = $this->input->post('name');
        $age = $this->input->post('age');
        $gender = $this->input->post('gender');
        $phone = $this->input->post('phone');
        $salary = $this->input->post('salary');
        $actResult = $this->TeacherModel->updateTeacher($ic, $name, $age, $gender, $phone, $salary);
        $logtype = 'Info';
        $logmsg = '';
        $loguser = $this->session->userdata('user_id');
        if ($actResult) {
            $logtype = "Info";
            $logmsg = 'Teacher IC: '.$ic.' information updated';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }
        else {
            $logtype = "Error";
            $logmsg = 'Failed to update teacher info';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }

        redirect('teacher');
    }
    public function removeTeacher($id) 
    {
        $actResult = $this->TeacherModel->removeTeacher($id);
        $logtype = 'Info';
        $logmsg = '';
        $loguser = $this->session->userdata('user_id');
        if ($actResult) {
            $logtype = "Info";
            $logmsg = 'Teacher IC: '.$id.' removed';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }
        else {
            $logtype = "Error";
            $logmsg = 'Failed to remove teacher';
            $this->LogModel->writeDownLog($logtype, $logmsg, $loguser);
        }

        redirect('teacher');
    }
}