<?php


include_once('config/database.php');

class Model_Subject_Head extends Database
{
    public function get_subject_head_info($username)
    {
        $sql = "
        SELECT subject_head_id,username,avatar,email,name,last_login,birthday,permission_detail,gender_detail,genders.gender_id FROM subject_head
        INNER JOIN permissions ON subject_head.permission = permissions.permission
        INNER JOIN genders ON subject_head.gender_id = genders.gender_id
        WHERE username = '$username'";
        $this->set_query($sql);
        return $this->load_row();
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
    
    public function get_list_subjects()
    {
        $sql = "SELECT * FROM subjects";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function update_last_login($subject_head_id)
    {
        $sql="UPDATE subject_head set last_login=NOW() where subject_head_id='$subject_head_id'";
        $this->set_query($sql);
        $this->execute_return_status();
    }
    public function valid_username_or_email($data)
    {
        $sql = "SELECT name FROM students WHERE username = '$data' OR email = '$data'
        UNION
        SELECT name FROM teachers WHERE username = '$data' OR email = '$data'
        UNION
        SELECT name FROM subject_head WHERE username = '$data' OR email = '$data'
        UNION
        SELECT name FROM admins WHERE username = '$data' OR email = '$data'";
        $this->set_query($sql);
        if ($this->load_row() != '') {
            return false;
        } else {
            return true;
        }
    }
    public function valid_class_name($class_name)
    {
        $sql = "SELECT class_id FROM classes WHERE class_name = '$class_name'";
        $this->set_query($sql);
        if ($this->load_row() != '') {
            return false;
        } else {
            return true;
        }
    }
    public function valid_email_on_profiles($curren_email, $new_email)
    {
        $sql = "SELECT name FROM students WHERE email = '$new_email' AND email NOT IN ('$curren_email')
        UNION SELECT name FROM admins WHERE email = '$new_email' AND email NOT IN ('$curren_email')
        UNION SELECT name FROM subject_head WHERE email = '$new_email' AND email NOT IN ('$curren_email')
        UNION SELECT name FROM teachers WHERE email = '$new_email' AND email NOT IN ('$curren_email')";
        $this->set_query($sql);
        if ($this->load_row() != '') {
            return false;
        } else {
            return true;
        }
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
    
    public function toggle_test_status($test_code, $status_id)
    {
        $sql="UPDATE tests set status_id='$status_id' where test_code ='$test_code'";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
    public function del_class($class_id)
    {
        $sql="DELETE FROM chats where class_id='$class_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="DELETE FROM student_notifications where class_id='$class_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="DELETE FROM classes where class_id='$class_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql = "SELECT class_name FROM classes WHERE class_id = '$class_id'";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        return true;
    }
    
    public function add_quest_to_test($test_code, $question_id)
    {
        $sql="INSERT INTO quest_of_test (test_code,question_id) VALUES ('$test_code','$question_id')";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
    public function get_list_questions($subject_id)
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
        WHERE questions.subject_id = '$subject_id'ORDER BY questions.question_id ASC";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function get_list_tests($subject_id)
    {
        $sql = "
        SELECT tests.test_code,tests.test_name,tests.password,tests.total_questions,tests.time_to_do,tests.note,grades.detail as grade,subjects.subject_detail,statuses.status_id,statuses.detail as status FROM `tests`
        INNER JOIN grades ON grades.grade_id = tests.grade_id
        INNER JOIN subjects ON subjects.subject_id = tests.subject_id
        INNER JOIN statuses ON statuses.status_id = tests.status_id
        WHERE tests.subject_id = '$subject_id'";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function get_question_info($ID)
    {
        $sql = "
        SELECT questions.ID,questions.question_detail,questions.level_id,grades.detail as grade_detail, questions.answer_a,questions.answer_b,questions.answer_c,questions.answer_d,questions.correct_answer, questions.suggest FROM `questions`
        INNER JOIN grades ON grades.grade_id = questions.grade_id";
        $this->set_query($sql);
        return $this->load_row();
    }
    public function get_list_statuses()
    {
        $sql = "
        SELECT * FROM `statuses`";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function edit_question($question_id,$subject_id, $question_content, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $suggest, $status_id)
    {
        $sql="UPDATE questions set question_content='$question_content',level_id='$level_id', grade_id='$grade_id', unit ='$unit',answer_a ='$answer_a',answer_b ='$answer_b',answer_c ='$answer_c',answer_d ='$answer_d',correct_answer ='$correct_answer',suggest ='$suggest',subject_id='$subject_id',status_id='$status_id' where question_id = '$question_id'";
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

    public function update_status_question($question_id, $status_id)
    {
        $sql="UPDATE questions set status_id='$status_id' where question_id = '$question_id'";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
    

    public function add_question($subject_id, $question_detail, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer,$suggest, $teacher_id)
    {
        $sql="INSERT INTO questions (subject_id,grade_id,unit,question_content,level_id,answer_a,answer_b,answer_c,answer_d,correct_answer, suggest, teacher_id, status_id) VALUES ('$subject_id','$grade_id','$unit','$question_detail','$level_id','$answer_a','$answer_b','$answer_c','$answer_d','$correct_answer','$suggest', '$teacher_id', '4')";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
    public function add_test($test_code,$test_name, $level_test, $password, $grade_id, $subject_id, $total_questions, $time_to_do, $note)
    {
        $sql="INSERT INTO tests (test_code,test_name,level_id,password,grade_id,subject_id,total_questions,time_to_do,note,status_id) VALUES ($test_code,'$test_name', $level_test ,'$password', $grade_id, $subject_id, $total_questions, $time_to_do, '$note', 1)";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
    public function get_test_score($test_code)
    {
        $sql = "SELECT * FROM `scores`, tests, classes, students WHERE scores.test_code = tests.test_code and 
        scores.student_id = students.student_id and students.class_id = classes.class_id and  
        scores.test_code ='$test_code'";
        $this->set_query($sql);
        return $this->load_rows();
    }
    
    public function update_profiles($username, $name, $email, $password, $gender, $birthday)
    {
        $sql="UPDATE subject_head set email='$email',password='$password', name ='$name', gender_id ='$gender', birthday ='$birthday' where username='$username'";
        $this->set_query($sql);
        $this->execute_return_status();
        return true;
    }
    public function update_avatar($avatar, $username)
    {
        $sql="UPDATE subject_head set avatar='$avatar' where username='$username'";
        $this->set_query($sql);
        $this->execute_return_status();
    }
    public function get_total_student()
    {
        $sql = "SELECT COUNT(student_id) as total FROM students";
        $this->set_query($sql);
        return $this->load_row()->total;
    }
    public function get_total_admin()
    {
        $sql = "SELECT COUNT(admin_id) as total FROM admins";
        $this->set_query($sql);
        return $this->load_row()->total;
    }
    public function get_total_teacher()
    {
        $sql = "SELECT COUNT(teacher_id) as total FROM teachers";
        $this->set_query($sql);
        return $this->load_row()->total;
    }
    public function get_total_class()
    {
        $sql = "SELECT COUNT(class_id) as total FROM classes";
        $this->set_query($sql);
        return $this->load_row()->total;
    }
    public function get_total_subject()
    {
        $sql = "SELECT COUNT(subject_id) as total FROM subjects";
        $this->set_query($sql);
        return $this->load_row()->total;
    }
    public function get_total_question()
    {
        $sql = "SELECT COUNT(question_id) as total FROM questions";
        $this->set_query($sql);
        return $this->load_row()->total;
    }
    public function get_total_grade()
    {
        $sql = "SELECT COUNT(grade_id) as total FROM grades";
        $this->set_query($sql);
        return $this->load_row()->total;
    }
    public function get_total_test()
    {
        $sql = "SELECT COUNT(test_code) as total FROM tests";
        $this->set_query($sql);
        return $this->load_row()->total;
    }
    public function edit_subject($subject_id, $subject_detail)
    {
        $sql = "SELECT subject_detail FROM subjects WHERE subject_id = '$subject_id'";
        $this->set_query($sql);
        if ($this->load_row()=='') {
            return false;
        }
        $sql="UPDATE subjects set subject_detail='$subject_detail' where subject_id='$subject_id'";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
    public function del_subject($subject_id)
    {
        $sql="DELETE FROM subjects where subject_id='$subject_id'";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
    public function add_subject($subject_detail)
    {
        $sql="INSERT INTO subjects (subject_detail) VALUES ('$subject_detail')";
        $this->set_query($sql);
        return $this->execute_return_status();
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
        $sql = "SELECT * FROM `questions` WHERE `grade_id` = $grade_id and `subject_id` = $subject_id and `level_id` = $level_id and `status_id` = 4 ORDER BY RAND() LIMIT $limit";
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
    
    public function get_list_classes_by_subject_head($subject_head_id)
    {
        $sql = "SELECT classes.class_id,classes.class_name,grades.detail as grade FROM classes
        INNER JOIN grades ON grades.grade_id = classes.grade_id
        WHERE subject_head_id = '$subject_head_id'";
        $this->set_query($sql);
        return $this->load_rows();
    }
}
