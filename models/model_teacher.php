<?php


include_once('config/database.php');

class Model_Teacher extends Database
{
    public function get_profiles($username)
    {
        $sql = "SELECT teachers.teacher_id as ID,teachers.username,teachers.name,teachers.email,teachers.avatar,teachers.birthday,teachers.last_login,genders.gender_id,genders.gender_detail FROM `teachers`
        INNER JOIN genders ON genders.gender_id = teachers.gender_id
        WHERE username = '$username'";
        $this->set_query($sql);
        return $this->load_row();
    }
    public function update_last_login($ID)
    {
        $sql="UPDATE teachers set last_login=NOW() where teacher_id='$ID'";
        $this->set_query($sql);
        $this->execute_return_status();
    }
    public function valid_email_on_profiles($curren_email, $new_email)
    {
        $sql = "SELECT name FROM students WHERE email = '$new_email' AND email NOT IN ('$curren_email')
        UNION SELECT name FROM admins WHERE email = '$new_email' AND email NOT IN ('$curren_email')
        UNION SELECT name FROM teachers WHERE email = '$new_email' AND email NOT IN ('$curren_email')";
        $this->set_query($sql);
        if ($this->load_row() != '') {
            return false;
        } else {
            return true;
        }
    }
    public function get_list_test($teacher_id)
    {
        $sql = "SELECT tests.test_code,tests.test_name,tests.total_questions,tests.time_to_do,tests.note,grades.detail as grade,subjects.subject_detail FROM `tests`
        INNER JOIN grades ON grades.grade_id = tests.grade_id
        INNER JOIN subjects ON subjects.subject_id = tests.subject_id
        WHERE `test_code` IN (SELECT DISTINCT test_code FROM `scores`
        INNER JOIN students ON scores.student_id = students.student_id
        WHERE students.class_id IN (SELECT DISTINCT class_id FROM classes WHERE classes.teacher_id = '$teacher_id'))";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function get_test_score($test_code)
    {
        $sql = "SELECT * FROM `scores` INNER JOIN students ON scores.student_id = students.student_id 
        INNER JOIN classes ON students.class_id = classes.class_id
        WHERE test_code = '$test_code'";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function update_avatar($avatar, $username)
    {
        $sql="UPDATE teachers set avatar='$avatar' where username='$username'";
        $this->set_query($sql);
        $this->execute_return_status();
    }
    public function update_profiles($username, $name, $email, $password, $gender, $birthday)
    {
        $sql="UPDATE teachers set email='$email',password='$password', name ='$name', gender_id ='$gender', birthday ='$birthday' where username='$username'";
        $this->set_query($sql);
        $this->execute_return_status();
        return true;
    }
    public function get_list_classes_by_teacher($teacher_id)
    {
        $sql = "SELECT classes.class_id,classes.class_name,grades.detail as grade FROM classes
        INNER JOIN grades ON grades.grade_id = classes.grade_id
        WHERE teacher_id = '$teacher_id'";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function get_class_detail($class_id)
    {
        $sql = "SELECT students.student_id,students.avatar,students.username,students.name,students.birthday,genders.gender_detail,students.last_login,class_name FROM students
        INNER JOIN genders ON genders.gender_id = students.gender_id
        INNER JOIN classes ON students.class_id =classes.class_id
        WHERE students.class_id = '$class_id'";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function get_notifications_to_student($teacher_id)
    {
        $sql = "SELECT * FROM notifications WHERE notification_id IN (SELECT notification_id FROM student_notifications WHERE student_notifications.class_id IN (SELECT classes.class_id FROM classes WHERE teacher_id = '$teacher_id'))";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function get_notifications_by_admin($teacher_id)
    {
        $sql = "SELECT * FROM notifications WHERE notification_id IN (SELECT notification_id FROM teacher_notifications WHERE teacher_id = '$teacher_id')";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function insert_notification($username, $name, $notification_title, $notification_content)
    {
        $sql="INSERT INTO notifications (username,name,notification_title,notification_content,time_sent) VALUES ('$username','$name','$notification_title','$notification_content',NOW())";
        //var_dump($ID); die();
        $this->set_query($sql);
        $row = $this->execute_return_status();// 
        if($row) {
            $query = "select LAST_INSERT_ID()";
            $this->set_query($query);
            $ID = $this->load_row();
            return $ID->{'LAST_INSERT_ID()'};
        }
        return false;
    }
    public function notify_class($ID, $class_id)
    {
        $sql="INSERT INTO student_notifications (notification_id,class_id) VALUES ('$ID','$class_id')";
        $this->set_query($sql);
        $this->execute_return_status();
    }
    public function get_score($student_id)
    {
        $sql = "SELECT * FROM `scores`
        WHERE `student_id` = $student_id";
        $this->set_query($sql);
        return $this->load_rows();
    }

    public function edit_question($question_id,$subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $suggest)
    {
        $sql="UPDATE questions set question_content='$question_content',level_id='$level_id', grade_id='$grade_id', unit ='$unit',answer_a ='$answer_a',answer_b ='$answer_b',answer_c ='$answer_c',answer_d ='$answer_d',correct_answer ='$correct_answer',suggest ='$suggest',subject_id='$subject_id' where question_id = '$question_id'";
        $this->set_query($sql);
        return $this->execute_return_status();
    }

    public function del_question($question_id)
    {
        $sql="DELETE FROM questions where question_id='$question_id'";
        $this->set_query($sql);
        return $this->execute_return_status();
    }

    public function del_multi_question($question_ids)
    {
        $sql="DELETE FROM questions where question_id IN ($question_ids)";
        $this->set_query($sql);
        return $this->execute_return_status();
    }

    public function add_question($subject_id,$question_detail, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer,$suggest, $teacher_id)
    {
        $sql="INSERT INTO questions (subject_id,grade_id,unit,question_content,level_id,answer_a,answer_b,answer_c,answer_d,correct_answer, suggest, teacher_id, status_id) VALUES ('$subject_id','$grade_id','$unit','$question_detail','$level_id','$answer_a','$answer_b','$answer_c','$answer_d','$correct_answer','$suggest', '$teacher_id', '3')";
        $this->set_query($sql);
        return $this->execute_return_status();
    }

    public function add_quest_to_test($test_code, $question_id)
    {
        $sql="INSERT INTO quest_of_test (test_code,question_id) VALUES ('$test_code','$question_id')";
        $this->set_query($sql);
        return $this->execute_return_status();
    }

    public function get_list_questions($teacher_id)
    {
        $sql = "
        SELECT questions.question_id,questions.question_content,levels.level_id,questions.unit,questions.grade_id,grades.detail as grade_detail, questions.answer_a,questions.answer_b,questions.answer_c,
        questions.answer_d,questions.correct_answer, questions.suggest, questions.subject_id,subjects.subject_detail as subject_detail,teachers.name as name_teacher, 
        questions.status_id, statuses.detail as status FROM `questions`
        INNER JOIN grades ON grades.grade_id = questions.grade_id
        INNER JOIN subjects ON subjects.subject_id = questions.subject_id
        INNER JOIN levels ON levels.level_id = questions.level_id
        LEFT JOIN statuses ON statuses.status_id = questions.status_id
        LEFT JOIN teachers ON teachers.teacher_id = questions.teacher_id
        WHERE questions.teacher_id = '$teacher_id'ORDER BY questions.question_id ASC";
        //var_dump($sql); die;
        $this->set_query($sql);
        return $this->load_rows();
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

    public function toggle_test_status($test_code, $status_id)
    {
        $sql="UPDATE tests set status_id='$status_id' where test_code ='$test_code'";
        $this->set_query($sql);
        return $this->execute_return_status();
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

    public function get_list_units($grade_id,$subject_id)
    {
        $sql = "SELECT DISTINCT unit, COUNT(unit) as total FROM questions WHERE subject_id = $subject_id and grade_id = $grade_id GROUP BY unit";
        $this->set_query($sql);
        return $this->load_rows();
    }

    public function list_quest_of_unit($grade_id,$subject_id,$unit,$limit)
    {
        $sql = "SELECT * FROM `questions` WHERE `grade_id` = $grade_id and `subject_id` = $subject_id and `unit` = $unit ORDER BY RAND() LIMIT $limit";
        $this->set_query($sql);
        return $this->load_rows();
    }

    public function get_quest_of_test($test_code)
    {
        $sql = "SELECT * FROM `quest_of_test`
        INNER JOIN questions ON quest_of_test.question_id = questions.question_id
        WHERE test_code = $test_code";
        $this->set_query($sql);
        return $this->load_rows();
    }
    
    public function get_list_quest_by_level($grade_id,$subject_id, $level_id,$limit)
    {
        $sql = "SELECT * FROM `questions` WHERE `grade_id` = $grade_id and `subject_id` = $subject_id and `level_id` = $level_id ORDER BY RAND() LIMIT $limit";
        $this->set_query($sql);
        return $this->load_rows();
    }

    public function get_count_questions()
    {
        $sql = "SELECT COUNT(question_id) as question_count FROM `questions`";
        $this->set_query($sql);
        return $this->load_row();
    }
}
