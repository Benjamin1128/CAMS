<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Log extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LogModel');
    }
    public function index()
    {
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'log';
        $userid = $this->input->post('user_id') ? $this->input->post('user_id') : 'all';
        $logtype = $this->input->post('logtype') ? $this->input->post('logtype') : 'all';
        $startDate = $this->input->post('start_date') ? $this->input->post('start_date') : null;
        $endDate = $this->input->post('end_date') ? $this->input->post('end_date') : (new DateTime()) -> format('Y-m-d');
        $logging = $this->LogModel->getLog($userid, $logtype, $startDate, $endDate);
        $users = $this->LogModel->getAvailableUser();
        $UseridName = $this->LogModel->getUserName($userid);

        $viewData = [
            'logging' => $logging,
            'users' => $users,
            'UseridName' => $UseridName,
            'startDate' => $startDate,
            'userid' => $userid,
            'logtype' => $logtype,
            'endDate' => $endDate,
        ];

        $this->load->view('header', $data);
        $this->load->view('logView', $viewData);
        $this->load->view('footer');
    }
}

?>