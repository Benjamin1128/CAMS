<?php
defined("BASEPATH") OR exit("No direct script access allowed"); 
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class Report extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ReportModel');
        $this->load->model('StudentModel');
        $this->load->model('ClassroomModel');
    }
    public function index()
    {
        $data['student_id'] = $this->session->userdata('student_id');
        $data['teacher_id'] = $this->session->userdata('teacher_id');
        $data['active_page'] = 'report';

        $classrooms = $this->ReportModel->getClasses();
        $classIds = $this->input->post('class_id') ? $this->input->post('class_id') : ['all'];
        $students = $this->ReportModel->getStudents();
        $totalStudents = $this->StudentModel->getTotalStudents();
        $totalClassrooms = $this->ClassroomModel->getTotalClassroom();
        $studentIds = $this->input->post('student_id') ? $this->input->post('student_id') : ['all'];
        $startDate = $this->input->post('start_date') ?: null;
        $endDate = $this->input->post('end_date') ?: date('Y-m-d');
        if ($totalClassrooms != 0) {
            $reportData = $this->ReportModel->getAllReport($classIds, $studentIds, $startDate, $endDate);
        } else {
            $reportData = [];
        }

        $statusCounts = [
            'Present' => 0,
            'Absent' => 0,
            'Late' => 0,
        ];
        foreach ($reportData as $record) {
            if (isset($statusCounts[$record['Student_Status']])) {
                $statusCounts[$record['Student_Status']]++;
            }
        }
        $totalRecords = array_sum($statusCounts);
        $statusPercentages = [
            'Present' => $totalRecords ? round(($statusCounts['Present'] / $totalRecords) * 100) : 0,
            'Absent' => $totalRecords ? round(($statusCounts['Absent'] / $totalRecords) * 100) : 0,
            'Late' => $totalRecords ? round(($statusCounts['Late'] / $totalRecords) * 100) : 0
        ];		
        

        $data['classSubject'] = array_column($classrooms, 'Class_Subject', 'Class_ID');
        $data['studentNames'] = array_column($students,'Student_Name', 'Student_ID');
        $data['classrooms'] = $classrooms;
        $data['classIds'] = $classIds;
        $data['students'] = $students;
        $data['studentIds'] = $studentIds;
        $data['totalStudents'] = $totalStudents;
        $data['totalClassrooms'] = $totalClassrooms;
        $data['statusCounts'] = $statusCounts;
        $data['statusPercentages'] = $statusPercentages;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $monthlyData = [];
        foreach ($reportData as $record) {
            $date = $record['Attendance_Date'];
            $month = date('Y-m', strtotime($date));
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = ['Present' => 0, 'Absent' => 0, 'Late' => 0];
            }
            $status = $record['Student_Status'];
            if (isset($monthlyData[$month][$status])) {
                $monthlyData[$month][$status]++;
            }
        }
        $months = array_keys($monthlyData);
        $presentCounts = array_column($monthlyData, 'Present');
        $absentCounts = array_column($monthlyData, 'Absent');
        $lateCounts = array_column($monthlyData, 'Late');
        $data['monthsJson'] = json_encode($months);
        $data['presentCountsJson'] = json_encode($presentCounts);
        $data['absentCountsJson'] = json_encode($absentCounts);
        $data['lateCountsJson'] = json_encode($lateCounts);
        $this->session->set_userdata('ExcelData', $data);

        $this->load->view('header', $data);
        $this->load->view('reportView', $data);
        $this->load->view('footer');
    }
    public function createExcel() 
    {
        $data = $this->session->userdata('ExcelData');
        $tempTeachId = $this->session->userdata('teacher_id');
        if (empty($data)) {
            show_error('No report data found. Please generate the report first.');
            return;
        }
        $selectedClassIds = $data['classIds'];
        $classSubjectNames = [];
        foreach ($selectedClassIds as $classId) {
            if (isset($data['classSubject'][$classId])) {
                $classSubjectNames[] = $data['classSubject'][$classId];
            } else {
                $classSubjectNames[] = 'All Classrooms';
            }
        }
        $classSubjectString = implode(',', $classSubjectNames);;
        $selectedStudentIds = $data['studentIds'];
        $studentNames = [];
        foreach ($selectedStudentIds as $studentId) {
            if (isset($data['studentNames'][$studentId])) {
                $studentNames[] = $data['studentNames'][$studentId];
            } else {
                $studentNames[] = 'All Students';
            }
        }

        $studentString = implode(',', $studentNames);
        $startDate = !empty($data['startDate']) ? date('d M Y', strtotime($data['startDate'])) : 'All Past Dates';
        $endDate = !empty($data['endDate']) ? date('d M Y', strtotime($data['endDate'])) : 'Today';
        $months = json_decode($data['monthsJson'], true);
        $presentCounts = json_decode($data['presentCountsJson'], true);
        $absentCounts = json_decode($data['absentCountsJson'], true);
        $lateCounts = json_decode($data['lateCountsJson'], true);
        $row = 10;
        $totalPresent = 0;
        $totalLate = 0;
        $totalAbsent = 0;
        $combinedData = [];
        for ($i = 0; $i < count($months); $i++) {
            $combinedData[] = [
                'month' => $months[$i],
                'present' => $presentCounts[$i],
                'absent' => $absentCounts[$i],
                'late' => $lateCounts[$i],
            ];
        }
        usort($combinedData, function($a, $b) {
            return strtotime($a['month']) - strtotime($b['month']);
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'Classroom Attendance Management Report');
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->setCellValue('A2', 'Generated By :');
        if (!empty($tempTeachId)) {
            $sheet->setCellValue('B2', $data['teacherName']);
        } else {
            $sheet->setCellValue('B2', 'Admin');
        }
        $sheet->setCellValue('D2', 'Generated Date :');
        $sheet->setCellValue('E2', date('d M Y'));
        $borderStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'inside' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ]
        ];
        $sheet->getStyle('A4:E4')->applyFromArray($borderStyle);
        $sheet->getStyle('A5:E5')->applyFromArray($borderStyle);
        $sheet->getStyle('A6:E6')->applyFromArray($borderStyle);
        $sheet->getStyle('A7:E7')->applyFromArray($borderStyle);
        $sheet->getStyle('A8:E8')->applyFromArray($borderStyle);
        $sheet->getStyle('A9:E9')->applyFromArray($borderStyle);
        $sheet->mergeCells('A4:E4');
        $sheet->setCellValue('A4', 'Filtered Option');
        $sheet->getStyle('A4:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('B5:E5');
        $sheet->setCellValue('A5', 'Selected Classroom');
        $sheet->setCellValue('B5', $classSubjectString);
        $sheet->mergeCells('B6:E6');
        $sheet->setCellValue('A6', 'Selected Student');
        $sheet->setCellValue('B6', $studentString);
        $sheet->mergeCells('B7:E7');
        $sheet->setCellValue('A7', 'Between Date');
        $sheet->setCellValue('B7', $startDate. 'Until'. $endDate);
        $sheet->setCellValue('A9', 'Month/Year');
        $sheet->setCellValue('B9', 'Amount Present');
        $sheet->setCellValue('C9', 'Amount Late');
        $sheet->setCellValue('D9', 'Amount Absent');
        $sheet->setCellValue('E9', 'Total Amount');
        foreach ($combinedData as $data) {
            $sheet->setCellValue('A' . $row, $data['month']);
            $sheet->setCellValue('B'. $row, $data['present']);
            $sheet->setCellValue('C'. $row, $data['late']);
            $sheet->setCellValue('D'. $row, $data['absent']);
            $totalAmount = $data['present'] + $data['absent'] + $data['late'];
            $sheet->setCellValue('E'. $row, $totalAmount);
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($borderStyle);
            $sheet->getStyle('E' . $row)->getFont()->setBold(true);
            $totalPresent += $data['present'];
            $totalLate += $data['late'];
            $totalAbsent += $data['absent'];
            $row++;
        }
        $sheet->setCellValue('A' . $row, 'Total');
        $sheet->setCellValue('B'. $row, $totalPresent);
        $sheet->setCellValue('C'. $row, $totalLate);
        $sheet->setCellValue('D'. $row, $totalAbsent);
        $sheet->setCellValue('E'. $row, $totalPresent + $totalLate + $totalAbsent);
        $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->getBold(true);
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($borderStyle);

        foreach(range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $filename = 'CAMSReport_' . date('YmdHis') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheet.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}

?>