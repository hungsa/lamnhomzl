<?php

include_once('config/database.php');

class Model_Student extends Database
{
	public function get_profiles($username)
	{
		$sql = "SELECT students.student_id as ID,students.username,students.name,students.email,students.avatar,students.class_id,students.birthday,students.last_login,genders.gender_id,genders.gender_detail,classes.grade_id,students.doing_exam,students.time_remaining,students.doing_practice,students.practice_time_remaining FROM `students`
		INNER JOIN genders ON genders.gender_id = students.gender_id
		INNER JOIN classes ON classes.class_id = students.class_id
		WHERE username = '$username'";
		$this->set_query($sql);
		return $this->load_row();
	}
	public function get_score($student_id,$test_code)
	{
		$sql = "SELECT * FROM `scores` WHERE student_id = $student_id AND test_code = $test_code";
		$this->set_query($sql);
		return $this->load_row();
	}

	public function get_practice_score($student_id,$practice_code)
	{
		$sql = "SELECT * FROM `practice_scores` WHERE student_id = $student_id AND practice_code = $practice_code";
		$this->set_query($sql);
		return $this->load_row();
	}
	public function get_scores($student_id)
	{
		$sql = "SELECT * FROM `scores` WHERE student_id = $student_id";
		$this->set_query($sql);
		return $this->load_rows();
	}
	
	public function get_notifications($class_id)
	{
		$sql = "SELECT * FROM notifications WHERE notification_id IN (SELECT notification_id FROM student_notifications WHERE class_id = '$class_id')";
		$this->set_query($sql);
		return $this->load_rows();
	}
	public function get_chats($class_id)
	{
		$sql = "SELECT * FROM `chats` WHERE class_id = '$class_id' ORDER BY ID DESC LIMIT 10";
		$this->set_query($sql);
		return $this->load_rows();
	}
	public function get_chat_all($class_id)
	{
		$sql = "SELECT * FROM `chats` WHERE class_id = '$class_id' ORDER BY ID DESC";
		$this->set_query($sql);
		return $this->load_rows();
	}
	public function chat($username, $name, $class_id, $content)
	{
		$sql = "INSERT INTO chats (username,name,class_id,chat_content,time_sent) VALUES ('$username','$name','$class_id','$content',NOW())";
		$this->set_query($sql);
		$this->execute_return_status();
	}
	public function update_last_login($ID)
	{
		$sql="UPDATE students set last_login=NOW() where student_id='$ID'";
		$this->set_query($sql);
		$this->execute_return_status();
	}
	public function update_doing_exam($test_code,$time,$ID)
	{
		$sql="UPDATE students set doing_exam= '$test_code', time_remaining = '$time',starting_time = NOW() where student_id='$ID'";
		$this->set_query($sql);
		$this->execute_return_status();
	}
	
	public function reset_doing_exam($ID)
	{
		$sql="UPDATE students set doing_exam= NULL, time_remaining = NULL, starting_time = NULL where student_id='$ID'";
		$this->set_query($sql);
		$this->execute_return_status();
	}

	public function update_doing_practice($practice_code,$time,$ID)
	{
		$sql="UPDATE students set doing_practice= '$practice_code', practice_time_remaining = '$time',practice_starting_time = NOW() where student_id='$ID'";
		$this->set_query($sql);
		$this->execute_return_status();
	}

	public function reset_doing_practice($ID)
	{
		$sql="UPDATE students set doing_practice= NULL, practice_time_remaining = NULL, practice_starting_time = NULL where student_id='$ID'";
		$this->set_query($sql);
		$this->execute_return_status();
	}
	public function valid_email_on_profiles($curren_email, $new_email)
	{
		$sql = "SELECT name FROM teachers WHERE email = '$new_email' AND email NOT IN ('$curren_email')
		UNION SELECT name FROM admins WHERE email = '$new_email' AND email NOT IN ('$curren_email')
		UNION SELECT name FROM students WHERE email = '$new_email' AND email NOT IN ('$curren_email')";
		$this->set_query($sql);
		if ($this->load_row() != '') {
			return false;
		} else {
			return true;
		}
	}
	public function update_avatar($avatar, $username)
	{
		$sql="UPDATE students set avatar='$avatar' where username='$username'";
		$this->set_query($sql);
		$this->execute_return_status();
	}
	public function update_profiles($username, $name, $email, $password, $gender, $birthday)
	{
		$sql="UPDATE students set email='$email',password='$password', name ='$name', gender_id ='$gender', birthday ='$birthday' where username='$username'";
		$this->set_query($sql);
		$this->execute_return_status();
		return true;
	}
	public function get_list_tests()
	{
		$sql = "
		SELECT tests.test_code,tests.test_name,tests.password,tests.total_questions,tests.time_to_do,tests.note,grades.detail as grade,subjects.subject_detail,statuses.status_id,statuses.detail as status FROM `tests`
		INNER JOIN grades ON grades.grade_id = tests.grade_id
		INNER JOIN subjects ON subjects.subject_id = tests.subject_id
		INNER JOIN statuses ON statuses.status_id = tests.status_id";
		$this->set_query($sql);
		return $this->load_rows();
	}
	public function get_test($test_code)
	{
		$sql = "SELECT * FROM tests WHERE test_code = '$test_code'";
		$this->set_query($sql);
		return $this->load_row();
	}

	public function get_practice($practice_code)
	{
		$sql = "SELECT * FROM practice WHERE practice_code = '$practice_code'";
		$this->set_query($sql);
		return $this->load_row();
	}

	public function get_quest_of_practice($practice_code)
	{
		$sql = "SELECT * FROM quest_of_practice
		INNER JOIN questions ON questions.question_id = quest_of_practice.question_id
		WHERE practice_code = $practice_code ORDER BY RAND()";
		$this->set_query($sql);
		return $this->load_rows();
	}
	public function add_student_quest($student_id, $ID, $test_code, $question_id, $answer_a, $answer_b, $answer_c, $answer_d)
	{
		$sql = "INSERT INTO `student_test_detail` (`student_id`,`ID`,`test_code`, `question_id`, `answer_a`, `answer_b`, `answer_c`, `answer_d`) VALUES ($student_id, $ID, $test_code, $question_id, '$answer_a', '$answer_b', '$answer_c', '$answer_d');";
		$this->set_query($sql);
		return $this->execute_return_status();
	}
	public function add_student_practice_quest($student_id, $ID, $practice_code, $question_id, $answer_a, $answer_b, $answer_c, $answer_d)
	{
		$sql = "INSERT INTO `student_practice_detail` (`student_id`,`ID`,`practice_code`, `question_id`, `answer_a`, `answer_b`, `answer_c`, `answer_d`) VALUES ($student_id, $ID, $practice_code, $question_id, '$answer_a', '$answer_b', '$answer_c', '$answer_d');";
		$this->set_query($sql);
		return $this->execute_return_status();
	}
	
	public function get_doing_quest($test_code,$student_id)
	{
		$sql = "SELECT *,questions.question_content FROM student_test_detail
		INNER JOIN questions ON student_test_detail.question_id = questions.question_id
		WHERE test_code = $test_code AND student_id = $student_id ORDER BY ID";
		$this->set_query($sql);
		return $this->load_rows();
	}

	public function get_doing_practice_quest($practice_code,$student_id)
	{
		$sql = "SELECT *,questions.question_content,questions.suggest FROM student_practice_detail
		INNER JOIN questions ON student_practice_detail.question_id = questions.question_id
		WHERE practice_code = $practice_code AND student_id = $student_id ORDER BY ID";
		$this->set_query($sql);
		return $this->load_rows();
	}

	public function get_result_quest($test_code,$student_id)
	{
		$sql = "SELECT * FROM student_test_detail
		INNER JOIN questions ON student_test_detail.question_id = questions.question_id
		INNER JOIN tests ON student_test_detail.test_code = tests.test_code
		WHERE student_test_detail.test_code = $test_code AND student_id = $student_id ORDER BY ID";
		$this->set_query($sql);
		return $this->load_rows();
	}
	public function update_answer($student_id, $test_code, $question_id,$student_answer)
	{
		$sql="UPDATE student_test_detail set student_answer='$student_answer' where student_id=$student_id AND test_code=$test_code AND question_id=$question_id";
		$this->set_query($sql);
		$this->execute_return_status();
	}
	public function update_timing($student_id, $time)
	{
		$sql="UPDATE students set time_remaining='$time' where student_id=$student_id";
		$this->set_query($sql);
		$this->execute_return_status();
	}
	public function insert_score($student_id,$test_code,$score,$score_detail)
	{
		$sql = "INSERT INTO `scores` (`student_id`, `test_code`, `score_number`, `score_detail`, completion_time) VALUES ('$student_id', '$test_code', '$score', '$score_detail', NOW())";
		$this->set_query($sql);
		return $this->execute_return_status();
	}

	public function get_result_practice_quest($practice_code,$student_id)
	{
		$sql = "SELECT * FROM student_practice_detail
		INNER JOIN questions ON student_practice_detail.question_id = questions.question_id
		INNER JOIN practice ON student_practice_detail.practice_code = practice.practice_code
		WHERE student_practice_detail.practice_code = $practice_code AND student_practice_detail.student_id = $student_id ORDER BY ID";
		$this->set_query($sql);
		return $this->load_rows();
	}

	public function update_practice_answer($student_id, $practice_code, $question_id,$student_answer)
	{
		$sql="UPDATE student_practice_detail set student_answer='$student_answer' where student_id=$student_id AND practice_code=$practice_code AND question_id=$question_id";
		$this->set_query($sql);
		$this->execute_return_status();
	}

	public function update_practice_timing($student_id, $time)
	{
		$sql="UPDATE students set practice_time_remaining='$time' where student_id=$student_id";
		$this->set_query($sql);
		$this->execute_return_status();
	}

	public function insert_practice_score($student_id,$practice_code,$score,$score_detail)
	{
		$sql = "INSERT INTO `practice_scores` (`student_id`, `practice_code`, `score_number`, `score_detail`, completion_time) VALUES ('$student_id', '$practice_code', '$score', '$score_detail', NOW())";
		$this->set_query($sql);
		return $this->execute_return_status();
	}

	public function add_quest_to_practice($practice_code, $question_id)
    {
        $sql="INSERT INTO quest_of_practice (practice_code,question_id) VALUES ('$practice_code','$question_id')";
        $this->set_query($sql);
        return $this->execute_return_status();
    }

	public function get_list_grades()
    {
        $sql = "SELECT * FROM grades";
        $this->set_query($sql);
        return $this->load_rows();
    }

    public function get_list_levels()
    {
        $sql = "SELECT * FROM levels";
        $this->set_query($sql);
        return $this->load_rows();
    }

    public function get_list_statuses()
    {
        $sql = "SELECT * FROM statuses";
        $this->set_query($sql);
        return $this->load_rows();
    }
    
    public function get_list_subjects()
    {
        $sql = "SELECT * FROM subjects";
        $this->set_query($sql);
        return $this->load_rows();
    }

	public function add_practice($practice_code , $level_id, $grade_id, $subject_id, $total_questions, $time_to_do, $student_id)
    {
        $sql="INSERT INTO practice (practice_code, level_id, grade_id, subject_id, total_questions, time_to_do, student_id) VALUES ('$practice_code ','$level_id', '$grade_id', '$subject_id', '$total_questions', '$time_to_do', $student_id)";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
	public function get_list_quest_by_level($grade_id,$subject_id, $level_id,$limit)
    {
        $sql = "SELECT * FROM `questions` WHERE `grade_id` = $grade_id and `subject_id` = $subject_id and `level_id` = $level_id ORDER BY RAND() LIMIT $limit";
        $this->set_query($sql);
        return $this->load_rows();
    }

	public function get_quest_of_test($test_code)
	{
		$sql = "SELECT * FROM quest_of_test
		INNER JOIN questions ON questions.question_id = quest_of_test.question_id
		WHERE test_code = $test_code ORDER BY RAND()";
		$this->set_query($sql);
		return $this->load_rows();
	}
	
	public function get_list_practice($student_id)
    {
        $sql = "SELECT practice_scores.practice_code, practice.total_questions,practice.time_to_do,practice.level_id,grades.detail as grade,subjects.subject_detail FROM `practice`
        INNER JOIN grades ON grades.grade_id = practice.grade_id
        INNER JOIN subjects ON subjects.subject_id = practice.subject_id
		INNER JOIN practice_scores ON practice_scores.practice_code = practice.practice_code
		WHERE practice.student_id = '$student_id'";
		$this->set_query($sql);
        return $this->load_rows();
    }
	public function get_list_units($grade_id,$subject_id)
    {
        $sql = "SELECT DISTINCT unit, COUNT(unit) as total FROM questions WHERE subject_id = $subject_id and grade_id = $grade_id GROUP BY unit";
        $this->set_query($sql);
        return $this->load_rows();
    }

    public function get_count_questions($subject_id, $grade_id)
    {
        $sql = "SELECT COUNT(question_id) as question_count, subjects.subject_detail as subject_detail, grades.detail as grade_detail FROM `questions`
        RIGHT JOIN subjects ON subjects.subject_id = questions.subject_id
        RIGHT JOIN grades ON grades.grade_id = questions.grade_id
        WHERE subjects.subject_id = '$subject_id' AND grades.grade_id = '$grade_id' AND status_id = '4'
        ";
        $this->set_query($sql);
        return $this->load_row();
    }

	public function statistics ($student_id) {
        
		$sql = "SELECT subject_detail, subject_id, SUM(test_existed) AS tested_time FROM 
		(SELECT subjects.subject_detail AS subject_detail, subjects.subject_id AS subject_id, 
		IF(practice_scores.practice_code IS NOT NULL AND practice.student_id='$student_id',1,0) AS test_existed  from subjects 
		LEFT JOIN practice ON subjects.subject_id = practice.subject_id 
		LEFT JOIN practice_scores ON practice.practice_code = practice_scores.practice_code) AS total 
		GROUP BY subject_detail";
        
        // var_dump($sql); die;
        $this->set_query($sql);
        return $this->load_rows();
    }

	public function subject_statistics_score ($student_id, $subject_id) {
		$sql = "SELECT practice_scores.score_number AS score, practice_scores.completion_time as day
		from practice_scores 
	   LEFT JOIN practice ON practice_scores.practice_code  = practice.practice_code  
	   LEFT JOIN subjects ON practice.subject_id = subjects.subject_id
	  WHERE practice.student_id = '$student_id' AND practice.subject_id = '$subject_id'
	  ORDER BY practice_scores.completion_time LIMIT 10";
        
        // var_dump($sql); die;
        $this->set_query($sql);
        return $this->load_rows();
	}
}
