<?php

require_once('models/model_teacher.php');
require_once 'views/view_teacher.php';
//load thư viện PhpSpreadSheet
require 'res/libs/SpreadSheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class Controller_Teacher
{
    public $userid = null;
    public $info =  array();
    public function __construct()
    {
        $user_info = $this->profiles();
        $this->info['ID'] = $user_info->ID;
        $this->update_last_login($this->info['ID']);
        $this->info['username'] = $user_info->username;
        $this->info['name'] = $user_info->name;
        $this->info['avatar'] = $user_info->avatar;
        $this->userid = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : null;
    }
    public function profiles()
    {
        $profiles = new Model_Teacher();
        return $profiles->get_profiles($_SESSION['username']);
    }
    public function update_last_login()
    {
        $info = new Model_Teacher();
        $info->update_last_login($this->info['ID']);
    }
    public function get_profiles()
    {
        $profiles = new Model_Teacher();
        echo json_encode($profiles->get_profiles($this->info['username']));
    }
    public function valid_email_on_profiles()
    {
        $result = array();
        $valid = new Model_Teacher();
        $new_email = isset($_POST['new_email']) ? htmlspecialchars($_POST['new_email']) : '';
        $curren_email = isset($_POST['curren_email']) ? htmlspecialchars($_POST['curren_email']) : '';
        if (empty($new_email)) {
            $result['status'] = 0;
        } else {
            if ($valid->valid_email_on_profiles($curren_email, $new_email)) {
                $result['status'] = 1;
            } else {
                $result['status'] = 0;
            }
        }
        echo json_encode($result);
    }
    public function update_avatar($avatar, $username)
    {
        $info = new Model_Teacher();
        return $info->update_avatar($avatar, $username);
    }
    public function submit_update_avatar()
    {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        if (isset($_FILES['file'])) {
            $duoi = explode('.', $_FILES['file']['name']);
            $duoi = $duoi[(count($duoi)-1)];
            if ($duoi === 'jpg' || $duoi === 'png') {
                if (move_uploaded_file($_FILES['file']['tmp_name'], 'res/img/avatar/'.$username.'_' . $_FILES['file']['name'])) {
                    $avatar = $username .'_' . $_FILES['file']['name'];
                    $update = $this->update_avatar($avatar, $username);
                }
            }
        }
    }
    public function update_profiles($username, $name, $email, $password, $gender, $birthday)
    {
        $info = new Model_Teacher();
        return $info->update_profiles($username, $name, $email, $password, $gender, $birthday);
    }
    public function submit_update_profiles()
    {
        $result = array();
        $name = isset($_POST['name']) ? Htmlspecialchars(addslashes($_POST['name'])) : '';
        $email = isset($_POST['email']) ? Htmlspecialchars(addslashes($_POST['email'])) : '';
        $username = isset($_POST['username']) ? Htmlspecialchars(addslashes($_POST['username'])) : '';
        $gender = isset($_POST['gender']) ? Htmlspecialchars(addslashes($_POST['gender'])) : '';
        $birthday = isset($_POST['birthday']) ? Htmlspecialchars(addslashes($_POST['birthday'])) : '';
        $password = isset($_POST['password']) ? ($_POST['password']) : '';
        if (empty($name)||empty($gender)||empty($birthday)||empty($password)||empty($email)) {
            $result['status_value'] = "Không được bỏ trống các trường nhập!";
            $result['status'] = 0;
        } else {
            $update = $this->update_profiles($username, $name, $email, $password, $gender, $birthday);
            if (!$update) {
                $result['status_value'] = "Tài khoản không tồn tại!";
                $result['status'] = 0;
            } else {
                $result = json_decode(json_encode($this->profiles($username)), true);
                $result['status_value'] = "Sửa thành công!";
                $result['status'] = 1;
            }
        }
        echo json_encode($result);
    }
    public function insert_notification($notification_title, $notification_content)
    {
        $notification = new Model_Teacher();
        return $notification->insert_notification($this->info['username'], $this->info['name'], $notification_title, $notification_content);
    }
    public function notify_class($ID, $class_id)
    {
        $send = new Model_Teacher();
        $send->notify_class($ID, $class_id);
    }
    public function send_notification()
    {
        $result = array();
        $notification_title = isset($_POST['notification_title']) ? htmlspecialchars($_POST['notification_title']) : '';
        $notification_content = isset($_POST['notification_content']) ? htmlspecialchars($_POST['notification_content']) : '';
        $class_id = isset($_POST['class_id']) ? json_decode(stripslashes($_POST['class_id'])) : array();
        if (empty($notification_title)||empty($notification_content)) {
            $result['status_value'] = "Nội dung hoặc tiêu đề trống!";
            $result['status'] = 0;
        } else {
            if (empty($class_id)) {
                $result['status_value'] = "Chưa lớp người nhận!";
                $result['status'] = 0;
            } else {
                $ID = $this->insert_notification($notification_title, $notification_content);
                if(!$ID){
                    $result['status_value'] = "Không gửi được thông báo!";
                    $result['status'] = 0;
                }else{
                    foreach ($class_id as $class_id_) {
                        $this->notify_class($ID, $class_id_);
                    }
                    $result['status_value'] = "Gửi thành công!";
                    $result['status'] = 1;
                }
            }
        }
        echo json_encode($result);
    }
    public function get_list_classes_by_teacher()
    {
        $list = new Model_Teacher();
        echo json_encode($list->get_list_classes_by_teacher($this->info['ID']));
    }
    public function get_notifications_to_student()
    {
        $list = new Model_Teacher();
        echo json_encode($list->get_notifications_to_student($this->info['ID']));
    }
    public function get_notifications_by_admin()
    {
        $list = new Model_Teacher();
        echo json_encode($list->get_notifications_by_admin($this->info['ID']));
    }
    public function get_score()
    {
        $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : '1';
        $score = new Model_Teacher();
        echo json_encode($score->get_score($student_id));
    }
    public function get_class_detail()
    {
        $ID = isset($_GET['ID']) ? $_GET['ID'] : '1';
        $class = new Model_Teacher();
        echo json_encode($class->get_class_detail($ID));
    }
    public function export_score()
    {
        $test_code = isset($_GET['test_code']) ? htmlspecialchars($_GET['test_code']) : '';

        $model = new Model_Teacher();
        $scores = $model->get_test_score($test_code);

        //Create Excel Data
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1','Danh Sách Điểm Bài Thi '.$scores[0]->test_name);
        $sheet->setCellValue('A3','STT');
        $sheet->setCellValue('B3','Tên');
        $sheet->setCellValue('C3','Tài Khoản');
        $sheet->setCellValue('D3','Lớp');
        $sheet->setCellValue('E3','Điểm');
        //var_dump($scores); die();
        for ($i = 0; $i < count($scores); $i++) {
            //var_dump($scores[$i]); die();
            $sheet->setCellValue('A'.($i+4),$i+1);
            $sheet->setCellValue('B'.($i+4),$scores[$i]->name);
            $sheet->setCellValue('C'.($i+4),$scores[$i]->username);
            $sheet->setCellValue('D'.($i+4),$scores[$i]->class_name);
            $sheet->setCellValue('E'.($i+4),$scores[$i]->score_number);
        }
        $sheet->setCellValue('B'.(count($scores)+5), 'Chữ kí giám thị 1');
        $sheet->setCellValue('E'.(count($scores)+5), 'Chữ kí giám thị 2');
        //Output File
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attactment;filename="danh-sach-diem-'.$test_code.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
    public function logout()
    {
        $result = array();
        $confirm = isset($_POST['confirm']) ? $_POST['confirm'] : false;
        if ($confirm) {
            $result['status_value'] = "Đăng xuất thành công!";
            $result['status'] = 1;
            session_destroy();
        }
        echo json_encode($result);
    }
    public function show_dashboard()
    {
        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_dashboard();
        $view->show_foot();
    }
    public function show_class_detail()
    {
        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_class_detail();
        $view->show_foot();
    }
    public function test_detail()
    {
        $view = new View_Teacher();
        $model = new Model_Teacher();
        $test_code = htmlspecialchars($_GET['test_code']);
        $view->show_head_left($this->info);
        $view->show_tests_detail($model->get_quest_of_test($test_code));
        $view->show_foot();
    }
    public function show_notifications()
    {
        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_notifications();
        $view->show_foot();
    }
    public function show_profiles()
    {
        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_profiles($this->profiles());
        $view->show_foot();
    }
    public function show_about()
    {
        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_about();
        $view->show_foot();
    }
    public function list_test()
    {
        $model = new Model_Teacher();
        $tests = $model->get_list_test($this->info['ID']);

        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_list_test($tests);
        $view->show_foot();
    }
    public function test_score()
    {
        $test_code = isset($_GET['test_code']) ? htmlspecialchars($_GET['test_code']) : '';
        $model = new Model_Teacher();
        $scores = $model->get_test_score($test_code);

        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_test_score($scores);
        $view->show_foot();
    }

    public function edit_question($question_id, $subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $suggest)
    {
        $edit = new Model_Teacher();
        return $edit->edit_question($question_id, $subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $suggest);
    }
    public function del_question($question_id)
    {
        $del = new Model_Teacher();
        return $del->del_question($question_id);
    }

    public function del_multi_question($question_ids)
    {
        $del = new Model_Teacher();
        return $del->del_multi_question($question_ids);
    }

    public function show_404()
    {
        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_404();
        $view->show_foot();
    }

    public function show_questions_panel()
    {
        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_questions_panel();
        $view->show_foot();
    }

    public function get_list_questions()
    {
        $list_questions = new Model_Teacher();
        echo json_encode($list_questions->get_list_questions($this->userid));
    }

    public function get_list_grades()
    {
        $list_grades = new Model_Teacher();
        echo json_encode($list_grades->get_list_grades());
    }
    public function get_list_subjects()
    {
        $list_grades = new Model_Teacher();
        echo json_encode($list_grades->get_list_subjects());
    }

    public function get_list_levels()
    {
        $list_levels = new Model_Teacher();
        echo json_encode($list_levels->get_list_levels());
    }
    public function get_list_statuses()
    {
        $list_status = new Model_Teacher();
        echo json_encode([]);
    }

    public function add_question($subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer,$suggest, $teacher_id)
    {
        $add_question = new Model_Teacher();
        return $add_question->add_question($subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $suggest, $teacher_id);
    }

    public function check_add_question()
    {
        $result = array();
        $question_detail = isset($_POST['question_detail']) ? $_POST['question_detail'] : '';
        $grade_id = isset($_POST['grade_id']) ? Htmlspecialchars(addslashes($_POST['grade_id'])) : '';
        $unit = isset($_POST['unit']) ? Htmlspecialchars(addslashes($_POST['unit'])) : '';
        $subject_id = isset($_POST['subject_id']) ? addslashes($_POST['subject_id']) : '';
        $level_id = isset($_POST['level_id']) ? Htmlspecialchars(addslashes($_POST['level_id'])) : '';
        $answer_a = isset($_POST['answer_a']) ? addslashes($_POST['answer_a']) : '';
        $answer_b = isset($_POST['answer_b']) ? addslashes($_POST['answer_b']) : '';
        $answer_c = isset($_POST['answer_c']) ? addslashes($_POST['answer_c']) : '';
        $answer_d = isset($_POST['answer_d']) ? addslashes($_POST['answer_d']) : '';
        $correct_answer = isset($_POST['correct_answer']) ? Htmlspecialchars(addslashes($_POST['correct_answer'])) : '';
        $suggest = isset($_POST['suggest']) ?addslashes($_POST['suggest']) : '';
        $teacher_id = $this->userid;
        if (empty($question_detail)||empty($grade_id)||empty($level_id)||empty($unit)||empty($subject_id)||empty($answer_a)||empty($answer_b)||empty($answer_c)||empty($answer_d)||empty($correct_answer)) {
            $result['status_value'] = "Không được bỏ trống các trường nhập";
            $result['status'] = 0;
        } else {
            switch ($correct_answer) {
                case "A":
                    $answer = $answer_a;
                    break;
                case "B":
                    $answer = $answer_b;
                    break;
                case "C":
                    $answer = $answer_c;
                    break;
                default:
                    $answer = $answer_d;
            }

            $res = $this->add_question($subject_id, $question_detail, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $answer,$suggest, $teacher_id);
            if ($res) {
                $result['status_value'] = "Thêm thành công!";
                $result['status'] = 1;
            } else {
                $result['status_value'] = "Thêm thất bại!";
                $result['status'] = 0;
            }
        }
        echo json_encode($result);
    }

    public function check_add_question_via_file()
    {
        $inputFileType = 'Xlsx';
        $result = array();
        $shuffle = array();
        $subject_id = isset($_POST['subject_id']) ? Htmlspecialchars(addslashes($_POST['subject_id'])) : '';
        $reader = IOFactory::createReader($inputFileType);
        move_uploaded_file($_FILES['file']['tmp_name'], $_FILES['file']['name']);
        $spreadsheet = $reader->load($_FILES['file']['name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        unlink($_FILES['file']['name']);
        $count = 0;
        $err_list = '';
        for ($i = 4; $i < count($sheetData); $i++) {
            if ($sheetData[$i]['A'] == '') {
                continue;
            }
            $stt = $sheetData[$i]['A'];
            $question_content = $sheetData[$i]['B'];
            $level_id = $sheetData[$i]['C'];
            $answer_a = $sheetData[$i]['D'];
            $answer_b = $sheetData[$i]['E'];
            $answer_c = $sheetData[$i]['F'];
            $answer_d = $sheetData[$i]['G'];
            $correct_answer = $sheetData[$i]['H'];
            $grade_id = $sheetData[$i]['I'];
            $unit = $sheetData[$i]['J'];
            $suggest = $sheetData[$i]['K'];
            $teacher_id = $this->userid;
            if (empty($question_content)||empty($grade_id)||empty($level_id)||empty($unit)||empty($answer_a)||empty($answer_b)||empty($answer_c)||empty($answer_d)||empty($correct_answer)) {
                continue;
            }

            switch ($correct_answer) {
                case "A":
                    $answer = $answer_a;
                    break;
                case "B":
                    $answer = $answer_b;
                    break;
                case "C":
                    $answer = $answer_c;
                    break;
                default:
                    $answer = $answer_d;
            }

            $add = $this->add_question($subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $answer, $suggest, $teacher_id);
            if ($add) { 
                $count++;
            } else {
                $err_list += $stt.', ';
            }
        }
        if ($err_list == '') {
            $result['status_value'] = "Thêm thành công ".$count.' câu hỏi!';
            $result['status'] = 1;
        } else {
            $result['status_value'] = "Lỗi! Không thể thêm câu hỏi có STT: ".$err_list.', vui lòng xem lại.';
            $result['status'] = 0;
        }
        echo json_encode($result);
    }

    public function delete_check_questions()
    {
        $result = array();
        $data = $_POST['list_check'];
        $res = $this->del_multi_question($data);

        if (!$res) {
            $result['status_value'] = "Xóa thất bại!";
            $result['status'] = 0;
        } else {
            $result['status_value'] = "Xóa thành công!";
            $result['status'] = 1;
        }
        echo json_encode($result);
    }
    public function check_edit_question()
    {
        $result = array();
        $question_id = isset($_POST['question_id']) ? Htmlspecialchars($_POST['question_id']) : '';
        $question_content = isset($_POST['question_content']) ? $_POST['question_content'] : '';
        $grade_id = isset($_POST['grade_id']) ? Htmlspecialchars($_POST['grade_id']) : '';
        $subject_id = isset($_POST['subject_id']) ? Htmlspecialchars($_POST['subject_id']) : '';
        $level_id = isset($_POST['level_id']) ? Htmlspecialchars($_POST['level_id']) : '';
        $unit = isset($_POST['unit']) ? Htmlspecialchars($_POST['unit']) : '';
        $answer_a = isset($_POST['answer_a']) ? Htmlspecialchars($_POST['answer_a']) : '';
        $answer_b = isset($_POST['answer_b']) ? Htmlspecialchars($_POST['answer_b']) : '';
        $answer_c = isset($_POST['answer_c']) ? Htmlspecialchars($_POST['answer_c']) : '';
        $answer_d = isset($_POST['answer_d']) ? Htmlspecialchars($_POST['answer_d']) : '';
        $correct_answer = isset($_POST['correct_answer']) ? Htmlspecialchars($_POST['correct_answer']) : '';
        $suggest = isset($_POST['suggest']) ? Htmlspecialchars($_POST['suggest']) : '';
        switch ($correct_answer) {
            case "A":
                $answer = $answer_a;
                break;
            case "B":
                $answer = $answer_b;
                break;
            case "C":
                $answer = $answer_c;
                break;
            default:
                $answer = $answer_d;
        }
    
        if (empty($question_content)||empty($grade_id)||empty($unit)||empty($level_id)||empty($answer_a)||empty($answer_b)||empty($answer_c)||empty($answer_d)||empty($correct_answer)||empty($suggest)) {
            $result['status_value'] = "Không được bỏ trống các trường nhập!";
            $result['status'] = 0;
        } else {
            $res = $this->edit_question($question_id, $subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $answer, $suggest);
            if ($res) {
                $result['status_value'] = "Sửa thành công!";
                $result['status'] = 1;
            } else {
                $result['status_value'] = "Sửa thất bại!";
                $result['status'] = 0;
            }
        }
        echo json_encode($result);
    }

    public function toggle_test_status($test_code, $status_id)
    {
        $toggle = new Model_Teacher();
        return $toggle->toggle_test_status($test_code, $status_id);
    }

    public function get_list_tests()
    {
        $list_tests = new Model_Teacher();
        echo json_encode($list_tests->get_list_tests());
    }

    public function show_tests_panel()
    {
        $view = new View_Teacher();
        $view->show_head_left($this->info);
        $view->show_tests_panel();
        $view->show_foot();
    }
    public function get_list_units()
    {
        $grade_id = $_POST['grade_id'];
        $subject_id = $_POST['subject_id'];
        $unit = new Model_Teacher();
        echo json_encode($unit->get_list_units($grade_id, $subject_id));
    }
    
    public function check_toggle_test_status()
    {
        $result = array();
        $status_id = Htmlspecialchars($_POST['status_id']);
        $test_code = Htmlspecialchars($_POST['test_code']);
        $toggle = $this->toggle_test_status($test_code, $status_id);
        if ($toggle) {
            $result['status_value'] = "Đã thay đổi trạng thái!";
            $result['status'] = 1;
        } else {
            $result['status_value'] = "Không thay đổi trạng thái!";
            $result['status'] = 0;
        }
        echo json_encode($result);
    }

    public function check_del_question()
    {
        $return = array();
        $question_id = isset($_POST['question_id']) ? Htmlspecialchars($_POST['question_id']) : '';
        $res = $this->del_question($question_id);

        if (!$res) {
            $result['status_value'] = "Xóa thất bại!";
            $result['status'] = 0;
        } else {
            $result['status_value'] = "Xóa thành công!";
            $result['status'] = 1;
        }
        $result['question_id'] = $question_id;
        echo json_encode($result);
    }

    public function caculator_question_level($total_question, $test_level) {
        $easy_question = $middle_question = $hard_question = 0;
        if($test_level == 1) {
            $easy_question = (int) round(($total_question*0.6),0,PHP_ROUND_HALF_UP);
            $middle_question = (int) round(($total_question*0.2),0,PHP_ROUND_HALF_UP);
            $hard_question = $total_question - $easy_question - $middle_question;
        } elseif($test_level == 2) {
            $middle_question = (int) round(($total_question*0.6),0,PHP_ROUND_HALF_UP);
            $easy_question = (int) round(($total_question*0.2),0,PHP_ROUND_HALF_UP);
            $hard_question = $total_question - $easy_question - $middle_question;
        } else {
            $hard_question = (int) round(($total_question*0.6),0,PHP_ROUND_HALF_UP);
            $easy_question = (int) round(($total_question*0.2),0,PHP_ROUND_HALF_UP);
            $middle_question = $total_question - $easy_question - $hard_question;
        }

        $list_question = [
            '1' => $easy_question,
            '2' => $middle_question,
            '3' => $hard_question
        ];

        return $list_question;
    }
}
