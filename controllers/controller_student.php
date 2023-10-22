<?php

require_once('models/model_student.php');
require_once 'views/view_student.php';

class Controller_Student
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
		$this->info['class_id'] = $user_info->class_id;
		$this->info['grade_id'] = $user_info->grade_id;
		$this->info['doing_exam'] = $user_info->doing_exam;
		$this->info['time_remaining'] = $user_info->time_remaining;
		$this->info['doing_practice'] = $user_info->doing_practice;
		$this->info['practice_time_remaining'] = $user_info->practice_time_remaining;
		$this->userid = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : null;
	}
	public function profiles()
	{
		$profiles = new Model_Student();
		return $profiles->get_profiles($_SESSION['username']);
	}
	// public function get_question($ID)
	// {
	// 	$answer = new Model_Student();
	// 	return $answer->get_question($ID);
	// }
	public function get_doing_exam()
	{
		return $this->info['doing_exam'];
	}
	public function update_last_login()
	{
		$info = new Model_Student();
		$info->update_last_login($this->info['ID']);
	}
	public function update_doing_exam($exam,$time)
	{
		$info = new Model_Student();
		$info->update_doing_exam($exam,$time,$this->info['ID']);
	}
	public function update_answer()
	{
		$question_id = $_POST['id'];
		$student_answer = $_POST['answer'];
		$update = new Model_Student();
		$update->update_answer($this->info['ID'], $this->info['doing_exam'], $question_id,$student_answer);
		echo $time = $_POST['min'].':'.$_POST['sec'];
		$update->update_timing($this->info['ID'], $time);
	}
	public function update_timing()
	{
		$update = new Model_Student();
		$time = $_POST['min'].':'.$_POST['sec'];
		$update->update_timing($this->info['ID'], $time);
	}
	public function reset_doing_exam()
	{
		$info = new Model_Student();
		$info->reset_doing_exam($this->info['ID']);
	}

	public function update_practice_answer()
	{
		$question_id = $_POST['id'];
		$student_answer = $_POST['answer'];
		$update = new Model_Student();
		$update->update_practice_answer($this->info['ID'], $this->info['doing_practice'], $question_id,$student_answer);
		echo $time = $_POST['min'].':'.$_POST['sec'];
		$update->update_practice_timing($this->info['ID'], $time);
	}

	public function update_practice_timing()
	{
		$update = new Model_Student();
		$time = $_POST['min'].':'.$_POST['sec'];
		$update->update_practice_timing($this->info['ID'], $time);
	}

	public function get_profiles()
	{
		$profiles = new Model_Student();
		echo json_encode($profiles->get_profiles($this->info['username']));
	}
	public function get_notifications()
	{
		$noti = new Model_Student();
		echo json_encode($noti->get_notifications($this->info['class_id']));
	}
	public function get_chats()
	{
		$chats = new Model_Student();
		echo json_encode($chats->get_chats($this->info['class_id']));
	}
	public function get_chat_all()
	{
		$chat_all = new Model_Student();
		echo json_encode($chat_all->get_chat_all($this->info['class_id']));
	}
	public function statistics()
    {
        $info = new Model_Student();
        echo json_encode($info->statistics($this->info['ID']));
    }
	
	public function subject_statistics_score()
    {
        $info = new Model_Student();
		$subject_id = isset($_POST['subject_id']) ? Htmlspecialchars(addslashes($_POST['subject_id'])) : null;
        echo json_encode($info->subject_statistics_score($this->info['ID'], $subject_id));
    }
	public function valid_email_on_profiles()
	{
		$result = array();
		$valid = new Model_Student();
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
		$info = new Model_Student();
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
	public function check_password()
	{
		$result = array();
		$model = new Model_Student();
		$test_code = isset($_POST['test_code']) ? $_POST['test_code'] : '493205';
		$password = isset($_POST['password']) ? ($_POST['password']) : 'e10adc3949ba59abbe56e057f20f883e';
		if($password != $model->get_test($test_code)->password) {
			$result['status_value'] = "Sai mật khẩu";
			$result['status'] = 0;
		} else {
			$list_quest = $model->get_quest_of_test($test_code);
			foreach ($list_quest as $quest) {
				$array = array();
				$array[0] = $quest->answer_a;
				$array[1] = $quest->answer_b;
				$array[2] = $quest->answer_c;
				$array[3] = $quest->answer_d;
				$ID = rand(1,time())+rand(100000,999999);
				$time = $model->get_test($test_code)->time_to_do.':00';
				$model->add_student_quest($this->info['ID'], $ID, $test_code, $quest->question_id, $array[0], $array[1], $array[2], $array[3]);
				$model->update_doing_exam($test_code,$time,$this->info['ID']);
			}
			$result['status_value'] = "Thành công. Chuẩn bị chuyển trang!";
			$result['status'] = 1;
		}
		echo json_encode($result);
		
	}

	public function get_practice()
	{
		$result = array();
		$model = new Model_Student();
		$practice_code = isset($_POST['practice_code']) ? $_POST['practice_code'] : '493205';
		$list_quest = $model->get_quest_of_practice($practice_code);

		foreach ($list_quest as $quest) {
			$ID = rand(1,time())+rand(100000,999999);
			$time = $model->get_practice($practice_code)->time_to_do.':00';
			$model->add_student_practice_quest($this->info['ID'], $ID, $practice_code, $quest->question_id, $quest->answer_a, $quest->answer_b, $quest->answer_c, $quest->answer_d);
			$model->update_doing_practice($practice_code,$time,$this->info['ID']);
		}
		$result['status_value'] = "Thành công. Chuẩn bị chuyển trang!";
		$result['status'] = 1;

		echo json_encode($result);
		
	}

	public function send_chat()
	{
		$result = array();
		$content = isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '';
		if(empty($content)) {
			$result['status_value'] = "Nội dung trống";
			$result['status'] = 0;
		} else {
			$m = new Model_Student();
			$m->chat($this->info['username'], $this->info['name'], $this->info['class_id'], $content);
			$result['status_value'] = "Thành công";
			$result['status'] = 1;
		}
		echo json_encode($result);
	}
	public function update_profiles($username, $name, $email, $password, $gender, $birthday)
	{
		$info = new Model_Student();
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

	public function accept_test()
	{
		$model = new Model_Student();
		$test = $model->get_result_quest($this->info['doing_exam'],$this->info['ID']);
		$test_code = $test[0]->test_code;
		$total_questions = $test[0]->total_questions;
		$correct = 0;
		$c = 10/$total_questions;
		foreach ($test as $t) {
			if(trim($t->student_answer) == trim($t->correct_answer))
				$correct++;
		}
		$score = $correct * $c;
		$score_detail = $correct.'/'.$total_questions;
		$model->insert_score($this->info['ID'],$test_code,round($score,2),$score_detail);
		$model->reset_doing_exam($this->info['ID']);
		header("Location: index.php?action=show_result&test_code=".$test_code);
	}

	public function accept_practice()
	{
		$model = new Model_Student();
		$practice = $model->get_result_practice_quest($this->info['doing_practice'],$this->info['ID']);
		$practice_code = $practice[0]->practice_code ;
		$total_questions = $practice[0]->total_questions;
		$correct = 0;
		$c = 10/$total_questions;
		foreach ($practice as $p) {
			if(trim($p->student_answer) == trim($p->correct_answer))
				$correct++;
		}
		$score = $correct * $c;
		$score_detail = $correct.'/'.$total_questions;
		$model->insert_practice_score($this->info['ID'],$practice_code,round($score,2),$score_detail);
		$model->reset_doing_practice($this->info['ID']);
		header("Location: index.php?action=show_practice_result&practice_code=".$practice_code);
	}

	public function logout()
	{
		$result = array();
		$confirm = isset($_POST['confirm']) ? $_POST['confirm'] : true;
		if ($confirm) {
			$result['status_value'] = "Đăng xuất thành công!";
			$result['status'] = 1;
			session_destroy();
		}
		echo json_encode($result);
	}
	public function show_dashboard()
	{
		$view = new View_Student();
		if($this->info['doing_exam']) {
			$model = new Model_Student();
			$test = $model->get_doing_quest($this->info['doing_exam'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_exam($test,$min,$sec);
		}
		elseif ($this->info['doing_practice']){
			$model = new Model_Student();
			$practice = $model->get_doing_practice_quest($this->info['doing_practice'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['practice_time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_practice($practice,$min,$sec);
		}
		else {
			$view->show_head_left($this->info);
			$model = new Model_Student();
			$scores = $model->get_scores($this->info['ID']);
			$tests = $model->get_list_tests();
			$view->show_dashboard($tests, $scores);
			$view->show_foot();
		}
	}
	public function show_chat()
	{
		$view = new View_Student();
		if($this->info['doing_exam']) {
			
			$model = new Model_Student();
			$test = $model->get_doing_quest($this->info['doing_exam'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_exam($test,$min,$sec);
		}
		elseif ($this->info['doing_practice']){
			$model = new Model_Student();
			$practice = $model->get_doing_practice_quest($this->info['doing_practice'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['practice_time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_practice($practice,$min,$sec);
		}
		else {
			$view->show_head_left($this->info);
			$view->show_chat();
			$view->show_foot();
		}
	}
	public function show_all_chat()
	{
		$view = new View_Student();
		$view->show_head_left($this->info);
		$view->show_all_chat();
		$view->show_foot();
	}
	public function show_notifications()
	{
		$view = new View_Student();
		$view->show_head_left($this->info);
		$view->show_notifications();
		$view->show_foot();
	}
	public function show_result()
	{
		$view = new View_Student();
		if($this->info['doing_exam'] == '') {
			$model = new Model_Student();
			$test_code = htmlspecialchars($_GET['test_code']);
			$score = $model->get_score($this->info['ID'],$test_code);
			$result = $model->get_result_quest($test_code,$this->info['ID']);
			if($score && $result)
			{
				$view->show_head_left($this->info);
				$view->show_result($score,$result);
				$view->show_foot();
			} else {
				$this->show_404();
			}
		}
		else {
			$model = new Model_Student();
			$test = $model->get_doing_quest($this->info['doing_exam'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_exam($test,$min,$sec);
		}
	}
	
	public function show_practice_result()
	{
		$view = new View_Student();
		if($this->info['doing_practice']) {
			$model = new Model_Student();
			$practice = $model->get_doing_practice_quest($this->info['doing_practice'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['practice_time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_practice($practice,$min,$sec);
		}
		else {
			$model = new Model_Student();
			$practice_code = htmlspecialchars($_GET['practice_code']);
			$score = $model->get_practice_score($this->info['ID'],$practice_code);
			$result = $model->get_result_practice_quest($practice_code,$this->info['ID']);
			if($score && $result)
			{
				$view->show_head_left($this->info);
				$view->show_practice_result($score,$result);
				$view->show_foot();
			} else {
				$this->show_404();
			}
		}
	}

	public function show_about()
	{
		$view = new View_Student();
		$view->show_head_left($this->info);
		$view->show_about();
		$view->show_foot();
	}
	public function show_profiles()
	{
		$view = new View_Student();
		$view->show_head_left($this->info);
		$view->show_profiles($this->profiles());
		$view->show_foot();
	}
	public function show_404()
	{
		$view = new View_Student();
		$view->show_head_left($this->info);
		$view->show_404();
		$view->show_foot();
	}

	public function add_practice($practice_code , $level_id, $grade_id, $subject_id, $total_questions, $time_to_do, $userid)
    {
        $practice = new Model_Student();
        return $practice->add_practice($practice_code, $level_id, $grade_id, $subject_id, $total_questions, $time_to_do, $userid);
    }

    public function get_list_practice()
    {
        $list_practice = new Model_Student();
        echo json_encode($list_practice->get_list_practice($this->userid));
    }
	public function get_list_grades()
    {
        $list_grades = new Model_Student();
        echo json_encode($list_grades->get_list_grades());
    }
    public function get_list_subjects()
    {
        $list_grades = new Model_Student();
        echo json_encode($list_grades->get_list_subjects());
    }

    public function get_list_levels()
    {
        $list_levels = new Model_Student();
        echo json_encode($list_levels->get_list_levels());
    }

    public function show_practice_panel()
    {
		$view = new View_Student();
		if($this->info['doing_exam']) {
			$model = new Model_Student();
			$test = $model->get_doing_quest($this->info['doing_exam'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_exam($test,$min,$sec);
		}
		elseif ($this->info['doing_practice']){
			$model = new Model_Student();
			$practice = $model->get_doing_practice_quest($this->info['doing_practice'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['practice_time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_practice($practice,$min,$sec);
		} else {
			$view = new View_Student();
			$view->show_head_left($this->info);
			$view->show_practice_panel();
			$view->show_foot();
		}
    }

	public function show_statistic_panel()
    {
		$view = new View_Student();
		if($this->info['doing_exam']) {
			$model = new Model_Student();
			$test = $model->get_doing_quest($this->info['doing_exam'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_exam($test,$min,$sec);
		}
		elseif ($this->info['doing_practice']){
			$model = new Model_Student();
			$practice = $model->get_doing_practice_quest($this->info['doing_practice'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['practice_time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_practice($practice,$min,$sec);
		} else {
			$view = new View_Student();
			$view->show_head_left($this->info);
			$view->show_statistic_panel();
			$view->show_foot();
		}
    }
	public function show_extension_panel()
	{
		$view = new View_Student();
		if($this->info['doing_exam']) {
			$model = new Model_Student();
			$test = $model->get_doing_quest($this->info['doing_exam'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_exam($test,$min,$sec);
		}
		elseif ($this->info['doing_practice']){
			$model = new Model_Student();
			$practice = $model->get_doing_practice_quest($this->info['doing_practice'],$this->info['ID']);
			$time_string[] = explode(":",$this->info['practice_time_remaining']);
			$min = $time_string[0][0];
			$sec = $time_string[0][1];
			$view->show_practice($practice,$min,$sec);
		} else {
			$view = new View_Student();
			$view->show_head_left($this->info);
			$view->show_statistic_panel();
			$view->show_foot();
		}
	}

    public function get_list_units()
    {
        $grade_id = $_POST['grade_id'];
        $subject_id = $_POST['subject_id'];
        $unit = new Model_Student();
        echo json_encode($unit->get_list_units($grade_id, $subject_id));
    }
    public function check_add_practice()
    {
        $result = array();
        $grade_id = isset($_POST['grade_id']) ? Htmlspecialchars(addslashes($_POST['grade_id'])) : '';
        $subject_id = isset($_POST['subject_id']) ? Htmlspecialchars(addslashes($_POST['subject_id'])) : '';
        $level_id = isset($_POST['level_id']) ? Htmlspecialchars(addslashes($_POST['level_id'])) : ''; 
        $total_questions = 30;
        $time_to_do = 30;
        $practice_code  = rand(10, 999999);
		$student_id =$this->userid;
        $student = new Model_Student();
        $total = $student->get_count_questions($subject_id, $grade_id);
        //var_dump($total->question_count); die;
		// var_dump($student_id); die();
            if($total_questions > $total->question_count){
                $result['status_value'] = "Quá số lượng câu hỏi trong kho! Vui lòng nhập lại!";
                $result['status'] = 0;
            } else {
                $add = $this->add_practice($practice_code , $level_id, $grade_id, $subject_id, $total_questions, $time_to_do, $student_id);
                
				if ($add) {
                    $result['status_value'] = "Thêm thành công!";
                    $result['status'] = 1;
					$result['practice_code'] = $practice_code;
                    //Tạo bộ câu hỏi cho đề thi
                    $model = new Model_Student();
                    // $list_unit = $model->get_list_units($grade_id, $subject_id);
                    // foreach ($list_unit as $unit) {
                    //     $limit = $_POST[$unit->unit];
                    //     $list_quest = $model->get_list_quest_by_level($grade_id, $subject_id, $level_id, $limit);
                    //     foreach ($list_quest as $quest) {
                    //         $model->add_quest_to_test($test_code, $quest->question_id);
                    //     }
                    // }
                    $limit = $this->caculator_question_level($total_questions, $level_id);
					
                    foreach($limit as $level_id => $limit_quest){
                        $list_quest = $model->get_list_quest_by_level($grade_id, $subject_id, $level_id, $limit_quest);
						
                        foreach ($list_quest as $quest) {
                            $model->add_quest_to_practice($practice_code , $quest->question_id);
                        }
                    }

                } else {
                    $result['status_value'] = "Thêm thất bại!";
                    $result['status'] = 0;
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
}
