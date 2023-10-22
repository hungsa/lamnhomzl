<?php


require_once('models/model_subject_head.php');
require_once('views/view_subject_head.php');
//load thư viện PhpSpreadSheet
require 'res/libs/SpreadSheet/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Controller_Subject_Head
{

    private $info  = array();
    public $subject_id = null;
    public $subject_head_id = null;
    public function __construct()
    {
        $user_info = $this->get_subject_head_info($_SESSION['username']);
        $this->info['subject_head_id'] = $user_info->subject_head_id;
        $this->update_last_login($this->info['subject_head_id']);
        $this->info['username'] = $user_info->username;
        $this->info['name'] = $user_info->name;
        $this->info['avatar'] = $user_info->avatar;
        $this->subject_id = (isset($_SESSION['subject_id'])) ? $_SESSION['subject_id'] : null;
        $this->subject_head_id = (isset($_SESSION['subject_head_id'])) ? $_SESSION['subject_head_id'] : null;
    }
    public function get_subject_head_info($username)
    {
        $info = new Model_Subject_Head();
        return $info->get_subject_head_info($username);
    }
   
    public function update_profiles($username, $name, $email, $password, $gender, $birthday)
    {
        $info = new Model_Subject_Head();
        return $info->update_profiles($username, $name, $email, $password, $gender, $birthday);
    }
    public function update_avatar($avatar, $username)
    {
        $info = new Model_Subject_Head();
        return $info->update_avatar($avatar, $username);
    }
   
    public function get_question_info($ID)
    {
        $info = new Model_Subject_Head();
        return $info->get_question_info($ID);
    }

    public function update_last_login()
    {
        $info = new Model_Subject_Head();
        $info->update_last_login($this->info['subject_head_id']);
    }
  
    public function get_list_grades()
    {
        $list_grades = new Model_Subject_Head();
        echo json_encode($list_grades->get_list_grades());
    }
    public function get_list_subjects()
    {
        $list_grades = new Model_Subject_Head();
        echo json_encode($list_grades->get_list_subjects());
    }

    public function get_list_levels()
    {
        $list_levels = new Model_Subject_Head();
        echo json_encode($list_levels->get_list_levels());
    }

    public function get_list_statuses()
    {
        $list_statuses = new Model_Subject_Head();
        echo json_encode($list_statuses->get_list_statuses());
    }
    public function valid_username_or_email()
    {
        $result = array();
        $valid = new Model_Subject_Head();
        $usr_or_email = isset($_GET['usr_or_email']) ? htmlspecialchars($_GET['usr_or_email']) : '';
        if (empty($usr_or_email)) {
            $result['status'] = 0;
        } else {
            if ($valid->valid_username_or_email($usr_or_email)) {
                $result['status'] = 1;
            } else {
                $result['status'] = 0;
            }
        }
        echo json_encode($result);
    }
    public function valid_email_on_profiles()
    {
        $result = array();
        $valid = new Model_Subject_Head();
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
    public function valid_class_name()
    {
        $result = array();
        $valid = new Model_Subject_Head();
        $class_name = isset($_GET['class_name']) ? htmlspecialchars($_GET['class_name']) : '';
        if (empty($class_name)) {
            $result['status'] = 0;
        } else {
            if ($valid->valid_class_name($class_name)) {
                $result['status'] = 1;
            } else {
                $result['status'] = 0;
            }
        }
        echo json_encode($result);
    }
   
    public function get_list_questions()
    {
        $list_questions = new Model_Subject_Head();
        echo json_encode($list_questions->get_list_questions($this->subject_id));
    }
    public function get_list_tests()
    {
        $list_tests = new Model_Subject_Head();
        echo json_encode($list_tests->get_list_tests($this->subject_id));
    }

    public function edit_question($question_id, $subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $suggest,$status_id)
    {
        $edit = new Model_Subject_Head();
        return $edit->edit_question($question_id, $subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $suggest,$status_id);
    }
    public function del_question($question_id)
    {
        $del = new Model_Subject_Head();
        return $del->del_question($question_id);
    }
    public function del_multi_question($question_ids)
    {
        $del = new Model_Subject_Head();
        return $del->del_multi_question($question_ids);
    }

    public function update_status_question($question_id, $status_id)
    {
        $del = new Model_Subject_Head();
        return $del->update_status_question($question_id, $status_id);
    }

    public function add_question($subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer,$suggest, $teacher_id)
    {
        $add_question = new Model_Subject_Head();
        return $add_question->add_question($subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer,$suggest, $teacher_id, '4');
    }
    public function edit_subject($subject_id, $subject_detail)
    {
        $edit = new Model_Subject_Head();
        return $edit->edit_subject($subject_id, $subject_detail);
    }
    public function del_subject($subject_id)
    {
        $del = new Model_Subject_Head();
        return $del->del_subject($subject_id);
    }
    public function add_subject($subject_detail)
    {
        $add = new Model_Subject_Head();
        return $add->add_subject($subject_detail);
    }
    public function add_test($test_code, $test_name, $level_test ,$password, $grade_id, $subject_id, $total_questions, $time_to_do, $note)
    {
        $test = new Model_Subject_Head();
        return $test->add_test($test_code, $test_name, $level_test ,$password, $grade_id, $subject_id, $total_questions, $time_to_do, $note);
    }
    public function toggle_test_status($test_code, $status_id)
    {
        $toggle = new Model_Subject_Head();
        return $toggle->toggle_test_status($test_code, $status_id);
    }
    public function get_list_units()
    {
        $grade_id = $_POST['grade_id'];
        $subject_id = $_POST['subject_id'];
        $unit = new Model_Subject_Head();
        echo json_encode($unit->get_list_units($grade_id, $subject_id));
    }

    public function get_list_classes_by_subject_head()
    {
        $list = new Model_Subject_Head();
        echo json_encode($list->get_list_classes_by_subject_head($this->info['ID']));
    }

    public function get_dashboard_info()
    {
        $get_total = new Model_Subject_Head();
        $question = new stdclass();
        $question->count = $get_total->get_total_question();
        $question->name = "Câu Hỏi";
        $question->icon = "fa-question";
        $question->actionlink = "show_questions_panel";
        $test = new stdclass();
        $test->count = $get_total->get_total_test();
        $test->name = "Bài Thi";
        $test->icon = "fa-edit";
        $test->actionlink = "show_tests_panel";
        $total = array($question,$test);
        return $total;
    }
    public function check_add_question()
    {
        $result = array();
        $question_detail = isset($_POST['question_detail']) ? $_POST['question_detail'] : '';
        $grade_id = isset($_POST['grade_id']) ? Htmlspecialchars(addslashes($_POST['grade_id'])) : '';
        $unit = isset($_POST['unit']) ? Htmlspecialchars(addslashes($_POST['unit'])) : '';
        $subject_id = $this->subject_id;
        $level_id = isset($_POST['level_id']) ? Htmlspecialchars(addslashes($_POST['level_id'])) : '';
        $answer_a = isset($_POST['answer_a']) ? addslashes($_POST['answer_a']) : '';
        $answer_b = isset($_POST['answer_b']) ? addslashes($_POST['answer_b']) : '';
        $answer_c = isset($_POST['answer_c']) ? addslashes($_POST['answer_c']) : '';
        $answer_d = isset($_POST['answer_d']) ? addslashes($_POST['answer_d']) : '';
        $correct_answer = isset($_POST['correct_answer']) ? Htmlspecialchars(addslashes($_POST['correct_answer'])) : '';
        $suggest = isset($_POST['suggest']) ? addslashes($_POST['suggest']) : '';
        $teacher_id = null;
        if (empty($question_detail)||empty($grade_id)||empty($level_id)||empty($unit)||empty($answer_a)||empty($answer_b)||empty($answer_c)||empty($answer_d)||empty($correct_answer)) {
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

            $res = $this->add_question($subject_id, $question_detail, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $answer, $suggest, $teacher_id);
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
        $subject_id = $this->subject_id;
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
            $answers = [];
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
            $teacher_id = null;

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

            $add = $this->add_question($subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $answer,$suggest, $teacher_id);
            if ($add) {
                $count++;
            } else {
                $err_list += $stt.' ';
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
    public function check_del_question()
    {
        $return = array();
        $question_id = isset($_POST['question_id']) ? Htmlspecialchars($_POST['question_id']) : '';
        $this->del_question($question_id);
        $result['status_value'] = "Xóa thành công!";
        $result['status'] = 1;
        $result['question_id'] = $question_id;
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

    public function update_status_check_questions()
    {
        $result = array();
        $data = explode(',', $_POST['list_check']);
        $status_id = $_POST['status_id'];
        $count = 0;
        $err_list = '';
        foreach($data as $question_id){
            $res = $this->update_status_question($question_id, $status_id);
            if ($res) { 
                $count++;
            } else {
                $err_list += $question_id.', ';
            }
        }
        

        if ($err_list == '') {
            $result['status_value'] = "Cập nhật thành công trang thái ".$count.' câu hỏi!';
            $result['status'] = 1;
        } else {
            $result['status_value'] = "Lỗi! Không thể cập nhật câu hỏi có ID: ".$err_list.', vui lòng xem lại.';
            $result['status'] = 0;
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
        $suggest = isset($_POST['suggest']) ? Htmlspecialchars($_POST['suggest']) : '';
        $status_id = isset($_POST['status_id']) ? Htmlspecialchars($_POST['status_id']) : '';
        $correct_answer = isset($_POST['correct_answer']) ? Htmlspecialchars($_POST['correct_answer']) : '';

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
            $res = $this->edit_question($question_id,$subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $suggest, $status_id);
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
                $result = json_decode(json_encode($this->get_subject_head_info($username)), true);
                $result['status_value'] = "Sửa thành công!";
                $result['status'] = 1;
            }
        }
        echo json_encode($result);
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
  
    public function check_add_test()
    {
        $result = array();
        $test_name = isset($_POST['test_name']) ? Htmlspecialchars(addslashes($_POST['test_name'])) : '';
        $password = isset($_POST['password']) ? ($_POST['password']) : '';
        $grade_id = isset($_POST['grade_id']) ? Htmlspecialchars(addslashes($_POST['grade_id'])) : '';
        $subject_id = $this->subject_id ? $this->subject_id : '';
        $level_test = isset($_POST['level_id']) ? Htmlspecialchars(addslashes($_POST['level_id'])) : ''; 
        $total_questions = isset($_POST['total_questions']) ? Htmlspecialchars(addslashes($_POST['total_questions'])) : '';
        $question_easy = isset($_POST['question_easy']) ? Htmlspecialchars(addslashes($_POST['question_easy'])) : '';
        $question_average = isset($_POST['question_average']) ? Htmlspecialchars(addslashes($_POST['question_average'])) : '';
        $question_difficult = isset($_POST['question_difficult']) ? Htmlspecialchars(addslashes($_POST['question_difficult'])) : '';
        //$total_question_normal = $question_easy + $question_average + $question_difficult;
        $time_to_do = isset($_POST['time_to_do']) ? Htmlspecialchars(addslashes($_POST['time_to_do'])) : '';
        $note = isset($_POST['note']) ? Htmlspecialchars(addslashes($_POST['note'])) : '';
        $test_code = rand(100000, 999999);
        $teacher = new Model_Subject_Head();
        $total = $teacher->get_count_questions($subject_id, $grade_id);
        if (empty($test_name)||empty($time_to_do)||empty($password)) {
            $result['status_value'] = "Không được bỏ trống các trường nhập!";
            $result['status'] = 0;
        } 
        else {
            if($total_questions != null) {
                if($total_questions > $total->question_count) {
                    $result['status_value'] = "Số lượng câu hỏi môn ".$total->subject_detail." ".$total->grade_detail." không đủ! Vui lòng nhập số lượng tối đa ".$total->question_count." câu hỏi!";
                    $result['status'] = 0;
                    if($total->question_count == 0) {
                        $result['status_value'] = "Không có câu hỏi nào trong ngân hàng câu hỏi cho môn ".$total->subject_detail." ".$total->grade_detail."!";
                    }
                } else {
                    $add = $this->add_test($test_code, $test_name, $level_test, $password, $grade_id, $subject_id, $total_questions, $time_to_do, $note);
                    if ($add) {
                        $result['status_value'] = "Thêm thành công!";
                        $result['status'] = 1;
                        //Tạo bộ câu hỏi cho đề thi
                        $model = new Model_Subject_Head();
                        $limit = $this->caculator_question_level($total_questions, $level_test);
                        foreach($limit as $level_id => $limit_quest) {
                            $list_quest = $model->get_list_quest_by_level($grade_id, $subject_id, $level_id, $limit_quest);
                            foreach ($list_quest as $quest) {
                                $model->add_quest_to_test($test_code, $quest->question_id);
                            }
                        }

                    } else {
                        $result['status_value'] = "Thêm thất bại!";
                        $result['status'] = 0;
                    }
                }
            } else {
                $total_questions = $question_easy + $question_average + $question_difficult;
                if($total_questions > $total->question_count) {
                    $result['status_value'] = "Số lượng câu hỏi môn ".$total->subject_detail." ".$total->grade_detail." không đủ! Vui lòng nhập số lượng tối đa ".$total->question_count." câu hỏi!";
                    $result['status'] = 0;
                    if($total->question_count == 0) {
                        $result['status_value'] = "Không có câu hỏi nào trong ngân hàng câu hỏi cho môn ".$total->subject_detail." ".$total->grade_detail."!";
                    }
                } else {
                    $add = $this->add_test($test_code, $test_name, $level_test, $password, $grade_id, $subject_id, $total_questions, $time_to_do, $note);
                    if ($add) {
                        $result['status_value'] = "Thêm thành công!";
                        $result['status'] = 1;
                        //Tạo bộ câu hỏi cho đề thi
                        $model = new Model_Subject_Head();
                        $limit = $this->caculator_question_normal($question_easy, $question_average, $question_difficult);
                        foreach($limit as $level_id => $limit_quest) {
                            $list_quest = $model->get_list_quest_by_level($grade_id, $subject_id, $level_id, $limit_quest);
                            foreach ($list_quest as $quest) {
                                $model->add_quest_to_test($test_code, $quest->question_id);
                            }
                        }

                    } else {
                        $result['status_value'] = "Thêm thất bại!";
                        $result['status'] = 0;
                    }
                }
            }
        }
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

    public function caculator_question_normal($question_easy, $question_average, $question_difficult) {
        $list_question = [
            '1' => $question_easy,
            '2' => $question_average,
            '3' => $question_difficult
        ];
        return $list_question;
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
    public function export_score()
    {
        $test_code = isset($_GET['test_code']) ? htmlspecialchars($_GET['test_code']) : '';
        $model = new Model_Subject_Head();
        $scores = $model->get_test_score($test_code);
        //Create Excel Data
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Danh Sách Điểm Bài Thi '.$scores[0]->test_name);
        $sheet->setCellValue('A3', 'STT');
        $sheet->setCellValue('B3', 'Tên');
        $sheet->setCellValue('C3', 'Tài Khoản');
        $sheet->setCellValue('D3', 'Lớp');
        $sheet->setCellValue('E3', 'Điểm');
        
        for ($i = 0; $i < count($scores); $i++) {
            $sheet->setCellValue('A'.($i+4), $i+1);
            $sheet->setCellValue('B'.($i+4), $scores[$i]->name);
            $sheet->setCellValue('C'.($i+4), $scores[$i]->username);
            $sheet->setCellValue('D'.($i+4), $scores[$i]->class_name);
            $sheet->setCellValue('E'.($i+4), $scores[$i]->score_number);
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
        $view = new View_Subject_Head();
        $view->show_head_left($this->info);
        $view->show_dashboard($this->get_dashboard_info());
        $view->show_foot();
    }
    
   
    public function show_questions_panel()
    {
        $view = new View_Subject_Head();
        $view->show_head_left($this->info);
        $view->show_questions_panel();
        $view->show_foot();
    }
    public function show_tests_panel()
    {
        $view = new View_Subject_Head();
        $view->show_head_left($this->info);
        $view->show_tests_panel();
        $view->show_foot();
    }
    public function test_detail()
    {
        $view = new View_Subject_Head();
        $model = new Model_Subject_Head();
        $test_code = htmlspecialchars($_GET['test_code']);
        $view->show_head_left($this->info);
        $view->show_tests_detail($model->get_quest_of_test($test_code));
        $view->show_foot();
    }
    public function test_score()
    {
        $view = new View_Subject_Head();
        $model = new Model_Subject_Head();
        $test_code = htmlspecialchars($_GET['test_code']);
        $view->show_head_left($this->info);
        $view->show_test_score($model->get_test_score($test_code));
        $view->show_foot();
    }
    public function show_subjects_panel()
    {
        $view = new View_Subject_Head();
        $view->show_head_left($this->info);
        $view->show_subjects_panel();
        $view->show_foot();
    }
   
    public function show_about()
    {
        $view = new View_Subject_Head();
        $view->show_head_left($this->info);
        $view->show_about();
        $view->show_foot();
    }
    public function show_profiles()
    {
        $view = new View_Subject_Head();
        $view->show_head_left($this->info);
        $view->show_profiles($this->get_subject_head_info($this->info['username']));
        $view->show_foot();
    }
    public function show_404()
    {
        $view = new View_Subject_Head();
        $view->show_head_left($this->info);
        $view->show_404();
        $view->show_foot();
    }
}
?>