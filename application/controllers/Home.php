<?php
defined("BASEPATH") OR exit("No direct script access allowed");
class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('StudentModel');
        $this->load->model('TeacherModel');
        $this->load->model('ClassroomModel');
        $this->load->model('ProfileModel');
    }
    public function index()
    {
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'home';
        $data['totalStudent'] = $this->StudentModel->getTotalStudents();
        $data['totalTeacher'] = $this->TeacherModel->getTotalTeachers();
        $data['totalClassroom'] = $this->ClassroomModel->getTotalClassroom();
        $data['totalSalary'] = $this->TeacherModel->getTotalSalary();
        $this->load->view('header', $data);
        $this->load->view('homeView');
        $this->load->view('footer');
    }

    public function userLogin()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $user = $this->ProfileModel->get_user_by_username($username);
        if ($user && password_verify($password, $user['User_Pwd'])) {
            $this->session->set_flashdata([
                'loggedin' => true,
                'user_id' => $user['Acc_ID'],
                'teacher_id' => $user['Teacher_ID'],
                'student_id' => $user['Student_ID'],
            ]);
            redirect('home');
        } else {
            $data['error_message'] = 'Invalid username or password.';
            $this->load->view('loginView', $data);
        }
    }
    public function showLoginView() 
    {
        $this->load->view('loginView');
    }
    public function userLogout()
    {
        $this->session->sess_destroy();
        $this->ShowLoginView();
    }
}

