<?php
include_once('config/database.php');

class Model_Admin extends Database
{
    public function get_admin_info($username)
    {
        $sql = "
        SELECT admin_id,username,avatar,email,name,last_login,birthday,permission_detail,gender_detail,genders.gender_id FROM admins
        INNER JOIN permissions ON admins.permission = permissions.permission
        INNER JOIN genders ON admins.gender_id = genders.gender_id
        WHERE username = '$username'";
        $this->set_query($sql);
        return $this->load_row();
    }
    public function get_teacher_info($username)
    {
        $sql = "
        SELECT teacher_id,username,avatar,email,name,last_login,birthday,permission_detail,gender_detail FROM teachers
        INNER JOIN permissions ON teachers.permission = permissions.permission
        INNER JOIN genders ON teachers.gender_id = genders.gender_id WHERE username = '$username'";
        $this->set_query($sql);
        return $this->load_row();
    }

    public function get_subject_head_info($username)
    {
        $sql = "
        SELECT subject_head_id,username,avatar,email,name,last_login,birthday,permission_detail,gender_detail, subject_id FROM subject_head
        INNER JOIN permissions ON subject_head.permission = permissions.permission
        INNER JOIN subjects ON subject_head.subject_id = subjects.subject_id
        INNER JOIN genders ON subject_head.gender_id = genders.gender_id WHERE username = '$username'";
        $this->set_query($sql);
        return $this->load_row();
    }

    public function get_student_info($username)
    {
        $sql = "
        SELECT student_id,username,name,email,avatar,birthday,last_login,gender_detail,class_name FROM `students`
        INNER JOIN classes ON students.class_id = classes.class_id
        INNER JOIN genders ON students.gender_id = genders.gender_id WHERE username = '$username'";
        $this->set_query($sql);
        return $this->load_row();
    }
    public function get_class_info($class_name)
    {
        $sql = "
        SELECT class_id,class_name,name as teacher_name, detail as grade_detail FROM classes
        INNER JOIN grades ON classes.grade_id = .grade_id
        INNER JOIN teachers ON classes.teacher_id = teachers.teacher_id
        WHERE class_name = '$class_name'";
        $this->set_query($sql);
        return $this->load_row();
    }
    public function get_list_admins()
    {
        $sql = "SELECT admin_id,username,avatar,email,name,last_login,birthday,permission_detail,gender_detail FROM admins
        INNER JOIN permissions ON admins.permission = permissions.permission
        INNER JOIN genders ON admins.gender_id = genders.gender_id";
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
    
    public function get_list_subjects()
    {
        $sql = "SELECT * FROM subjects";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function update_last_login($admin_id)
    {
        $sql="UPDATE admins set last_login=NOW() where admin_id='$admin_id'";
        $this->set_query($sql);
        $this->execute_return_status();
    }
    public function valid_username_or_email($data)
    {
        $sql = "SELECT name FROM students WHERE username = '$data' OR email = '$data'
        UNION
        SELECT name FROM teachers WHERE username = '$data' OR email = '$data'
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
        UNION SELECT name FROM teachers WHERE email = '$new_email' AND email NOT IN ('$curren_email')";
        $this->set_query($sql);
        if ($this->load_row() != '') {
            return false;
        } else {
            return true;
        }
    }
    public function edit_admin($admin_id, $password, $name, $gender_id, $birthday)
    {
        $sql = "SELECT username FROM admins WHERE admin_id = '$admin_id'";
        $this->set_query($sql);
        if ($this->load_row()=='') {
            return false;
        }
        $sql="UPDATE admins set password='$password', name ='$name', gender_id ='$gender_id', birthday ='$birthday' where admin_id='$admin_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        return true;
    }
    public function del_admin($admin_id)
    {
        $sql="DELETE FROM admins where admin_id='$admin_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql = "SELECT username FROM admins WHERE admin_id = '$admin_id'";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        return true;
    }
    public function add_admin($name, $username, $password, $email, $birthday, $gender)
    {
        $sql = "SELECT admin_id FROM admins WHERE username = '$username' OR email = '$email'";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        //reset AUTO_INCREMENT
        $sql = "ALTER TABLE `admins` AUTO_INCREMENT=1";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="INSERT INTO admins (name, username, password, email, birthday, gender_id) VALUES ('$name', '$username', '$password', '$email', '$birthday', '$gender')";
        $this->set_query($sql);
        return $this->execute_return_status();
        // return true;
    }
    public function get_list_teachers()
    {
        $sql = "SELECT teacher_id,username,avatar,email,name,last_login,birthday,permission_detail,gender_detail FROM teachers
        INNER JOIN permissions ON teachers.permission = permissions.permission
        INNER JOIN genders ON teachers.gender_id = genders.gender_id";
        $this->set_query($sql);
        return $this->load_rows();
    }

    public function get_list_subject_head()
    {
        $sql = "SELECT subject_head_id,username,avatar,email,name,last_login,birthday,permission_detail,gender_detail,subject_head.subject_id, subjects.subject_detail as subject_detail FROM subject_head
        INNER JOIN permissions ON subject_head.permission = permissions.permission
        INNER JOIN subjects ON subject_head.subject_id = subjects.subject_id
        INNER JOIN genders ON subject_head.gender_id = genders.gender_id";
        $this->set_query($sql);
        //var_dump($sql);die();
        return $this->load_rows();
    }
    public function edit_teacher($teacher_id, $password, $name, $gender_id, $birthday)
    {
        $sql = "SELECT username FROM teachers WHERE teacher_id = '$teacher_id'";
        $this->set_query($sql);
        if ($this->load_row()=='') {
            return false;
        }
        $sql="UPDATE teachers set password='$password', name ='$name', gender_id ='$gender_id', birthday ='$birthday' where teacher_id='$teacher_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        return true;
    }
    public function del_teacher($teacher_id)
    {
        $sql="DELETE FROM teacher_notifications where teacher_id='$teacher_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="DELETE FROM teachers where teacher_id='$teacher_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql = "SELECT username FROM teachers WHERE teacher_id = '$teacher_id'";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        return true;
    }
    public function add_teacher($name, $username, $password, $email, $birthday, $gender)
    {
        $sql = "SELECT teacher_id FROM teachers WHERE username = '$username' or email = '$email'";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        //reset AUTO_INCREMENT
        $sql = "ALTER TABLE `teachers` AUTO_INCREMENT=1";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="INSERT INTO teachers (username,password,name,email,birthday,gender_id) VALUES ('$username','$password','$name','$email','$birthday','$gender')";
        $this->set_query($sql);
        return $this->execute_return_status();
        // return true;
    }
    public function edit_subject_head($subject_head_id, $password, $name, $gender_id, $birthday, $subject_id)
    {
        $sql = "SELECT username FROM subject_head WHERE subject_head_id = '$subject_head_id'";
        $this->set_query($sql);
        if ($this->load_row()=='') {
            return false;
        }
        if ($password == "") {
            $sql="UPDATE subject_head set name ='$name', gender_id ='$gender_id', birthday ='$birthday', subject_id = '$subject_id' where subject_head_id='$subject_head_id'";
        } else {
            $sql="UPDATE subject_head set password='$password', name ='$name', gender_id ='$gender_id', birthday ='$birthday', subject_id = '$subject_id' where subject_head_id='$subject_head_id'";
        }
        $this->set_query($sql);
        $this->execute_return_status();
        return true;
    }
    public function del_subject_head($subject_head_id)
    {
        $sql="DELETE FROM subject_head where subject_head_id='$subject_head_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql = "SELECT username FROM subject_head WHERE subject_head_id = '$subject_head_id'";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        return true;
    }
    public function add_subject_head($name, $username, $password, $email, $birthday, $gender, $subject_id)
    {
        $sql = "SELECT subject_head_id FROM subject_head WHERE username = '$username' or email = '$email'";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        //reset AUTO_INCREMENT
        $sql = "ALTER TABLE `subject_head` AUTO_INCREMENT=1";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="INSERT INTO subject_head (username,password,name,email,birthday,gender_id, subject_id) VALUES ('$username','$password','$name','$email','$birthday','$gender','$subject_id')";
        $this->set_query($sql);
        return $this->execute_return_status();
        // return true;
    }
    public function get_list_students()
    {
        $sql = "
        SELECT student_id,username,name,email,avatar,birthday,last_login,gender_detail,class_name FROM `students`
        INNER JOIN classes ON students.class_id = classes.class_id
        INNER JOIN genders ON students.gender_id = genders.gender_id";
        $this->set_query($sql);
        return $this->load_rows();
    }
    public function edit_student($student_id, $birthday, $password, $name, $class_id, $gender)
    {
        $sql="UPDATE students set birthday='$birthday', password='$password', name ='$name', class_id ='$class_id', gender_id = '$gender' where student_id='$student_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="UPDATE scores set class_id ='$class_id' where student_id='$student_id'";
        $this->set_query($sql);
        $this->execute_return_status();
    }
    public function del_student($student_id)
    {
        $sql="DELETE FROM scores where student_id='$student_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="DELETE FROM students where student_id='$student_id'";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql = "SELECT username FROM students WHERE student_id = '$student_id'";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        return true;
    }
    public function add_student($username, $password, $name, $class_id, $email, $birthday, $gender)
    {
        $sql = "SELECT student_id FROM students WHERE username = '$username' OR email = '$email";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        //reset AUTO_INCREMENT
        $sql = "ALTER TABLE `students` AUTO_INCREMENT=1";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="INSERT INTO students (username,password,name,class_id,email,birthday,gender_id) VALUES ('$username','$password','$name','$class_id','$email','$birthday','$gender')";
        $this->set_query($sql);
        return $this->execute_return_status();
        // return true;
    }
    public function get_list_classes()
    {
        $sql = "
        SELECT class_id,class_name,name as teacher_name, detail as grade_detail FROM classes
        INNER JOIN grades ON classes.grade_id = grades.grade_id
        INNER JOIN teachers ON classes.teacher_id = teachers.teacher_id";
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
    public function edit_class($class_id, $grade_id, $class_name, $teacher_id)
    {
        $sql="UPDATE classes set grade_id='$grade_id', class_name='$class_name', teacher_id ='$teacher_id'  where class_id ='$class_id'";
        $this->set_query($sql);
        $this->execute_return_status();
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
    public function add_class($grade_id, $class_name, $teacher_id)
    {
        $sql = "SELECT class_id FROM classes WHERE class_name = '$class_name'";
        $this->set_query($sql);
        if ($this->load_row()!='') {
            return false;
        }
        //reset AUTO_INCREMENT
        $sql = "ALTER TABLE `classes` AUTO_INCREMENT=1";
        $this->set_query($sql);
        $this->execute_return_status();
        $sql="INSERT INTO classes (grade_id,class_name,teacher_id) VALUES ('$grade_id','$class_name','$teacher_id')";
        $this->set_query($sql);
        return $this->execute_return_status();
        // return true;
    }
    public function add_quest_to_test($test_code, $question_id)
    {
        $sql="INSERT INTO quest_of_test (test_code,question_id) VALUES ('$test_code','$question_id')";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
    public function get_list_questions()
    {
        $sql = "
        SELECT questions.question_id,questions.question_content,levels.level_id,questions.unit,questions.grade_id,grades.detail as grade_detail, questions.answer_a,questions.answer_b,questions.answer_c,
        questions.answer_d,questions.correct_answer, questions.suggest, questions.subject_id,subjects.subject_detail as subject_detail,teachers.name as name_teacher, 
        questions.status_id, statuses.detail as status FROM `questions`
        INNER JOIN grades ON grades.grade_id = questions.grade_id
        INNER JOIN subjects ON subjects.subject_id = questions.subject_id
        INNER JOIN levels ON levels.level_id = questions.level_id
        LEFT JOIN statuses ON statuses.status_id = questions.status_id
        LEFT JOIN teachers ON teachers.teacher_id = questions.teacher_id ORDER BY questions.question_id ASC";
        $this->set_query($sql);
        return $this->load_rows();
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
    public function get_question_info($ID)
    {
        $sql = "
        SELECT questions.ID,questions.question_detail,questions.level_id,grades.detail as grade_detail, questions.answer_a,questions.answer_b,questions.answer_c,questions.answer_d,questions.correct_answer,questions.suggest FROM `questions`
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
        $sql="UPDATE questions set question_content='$question_content',level_id='$level_id', grade_id='$grade_id', unit ='$unit',answer_a ='$answer_a',answer_b ='$answer_b',answer_c ='$answer_c',answer_d ='$answer_d',correct_answer ='$correct_answer', suggest='$suggest',subject_id='$subject_id',status_id='$status_id' where question_id = '$question_id'";
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

    public function add_question($subject_id,$question_detail, $level_id, $grade_id, $unit, $answer_a, $answer_b, $answer_c, $answer_d, $correct_answer, $teacher_id= null)
    {
        $sql="INSERT INTO questions (subject_id,grade_id,unit,question_content,level_id,answer_a,answer_b,answer_c,answer_d,correct_answer, teacher_id, status_id) VALUES ('$subject_id','$grade_id','$unit','$question_detail','$level_id','$answer_a','$answer_b','$answer_c','$answer_d','$correct_answer', '$teacher_id', '3')";
        $this->set_query($sql);
        return $this->execute_return_status();
    }
    public function add_test($test_code,$test_name, $level_test, $password, $grade_id, $subject_id, $total_questions, $time_to_do, $note)
    {
        $sql="INSERT INTO tests (test_code,test_name,level_id,password,grade_id,subject_id,total_questions,time_to_do,note,status_id) VALUES ($test_code,'$test_name', $level_test ,'$password', $grade_id, $subject_id, $total_questions, $time_to_do, '$note', 1)";
        $this->set_query($sql);
        return $this->execute_return_status();
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
    public function notify_teacher($ID, $teacher_id)
    {
        $sql="INSERT INTO teacher_notifications (notification_id,teacher_id) VALUES ('$ID','$teacher_id')";
        //var_dump($sql);die();
        $this->set_query($sql);
        $this->execute_return_status();
    }
    public function notify_class($ID, $class_id)
    {
        $sql="INSERT INTO student_notifications (notification_id,class_id) VALUES ('$ID','$class_id')";
        $this->set_query($sql);
        $this->execute_return_status();
    }
    public function get_teacher_notifications()
    {
        $sql = "
        SELECT notifications.notification_id, notifications.notification_title, notifications.notification_content, notifications.username,notifications.name,teachers.name as receive_name,teachers.username as receive_username,notifications.time_sent FROM teacher_notifications
        INNER JOIN notifications ON notifications.notification_id = teacher_notifications.notification_id
        INNER JOIN teachers ON teachers.teacher_id = teacher_notifications.teacher_id";
        $this->set_query($sql);
        return $this->load_rows();
    }
    
    public function get_student_notifications()
    {
        $sql = "
        SELECT notifications.notification_id, notifications.notification_title, notifications.notification_content, notifications.username,notifications.name,classes.class_name,notifications.time_sent FROM student_notifications
        INNER JOIN notifications ON notifications.notification_id = student_notifications.notification_id
        INNER JOIN classes ON classes.class_id = student_notifications.class_id";
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
    public function update_profiles($username, $name, $email, $password, $gender, $birthday)
    {
        $sql="UPDATE admins set email='$email',password='$password', name ='$name', gender_id ='$gender', birthday ='$birthday' where username='$username'";
        $this->set_query($sql);
        $this->execute_return_status();
        return true;
    }
    public function update_avatar($avatar, $username)
    {
        $sql="UPDATE admins set avatar='$avatar' where username='$username'";
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
    public function get_total_subject_head()
    {
        $sql = "SELECT COUNT(subject_head_id) as total FROM subject_head";
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
    public function get_total_statistic()
    {
        $sql = "SELECT COUNT(question_id) as total FROM questions";
        $this->set_query($sql);
        // return $this->load_row()->total;
        return 1;
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
        $sql = "SELECT * FROM `questions` WHERE `grade_id` = $grade_id and `subject_id` = $subject_id and `level_id` = $level_id ORDER BY RAND() LIMIT $limit";
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

    
    public function statistics ($grade_id) {
        if($grade_id) {
            $sql = "SELECT subject_detail, subject_id, SUM(test_existed) AS tested_time FROM 
            (SELECT subjects.subject_detail AS subject_detail, subjects.subject_id AS subject_id, 
            IF(scores.test_code IS NOT NULL AND tests.grade_id='$grade_id',1,0) AS test_existed  from subjects 
            LEFT JOIN tests ON subjects.subject_id = tests.subject_id 
            LEFT JOIN scores ON tests.test_code = scores.test_code) AS total 
            GROUP BY subject_detail";
        } else {
            $sql = "SELECT subject_detail, subject_id, SUM(test_existed) AS tested_time FROM 
            (SELECT subjects.subject_detail AS subject_detail, subjects.subject_id AS subject_id, 
            IF(scores.test_code IS NULL,0,1) AS test_existed  from subjects 
            LEFT JOIN tests ON subjects.subject_id = tests.subject_id 
            LEFT JOIN scores ON tests.test_code = scores.test_code) AS total 
            GROUP BY subject_detail";
        }
        // var_dump($sql); die;
        $this->set_query($sql);
        return $this->load_rows();
    }

    public function statistics_score ($grade_id) {
        if($grade_id) {
            $sql = "SELECT SUM(bad_score) AS bad, SUM(complete_score) AS complete, SUM(good_score) AS good, 
            SUM(excellent_score) AS excellent FROM (select tests.grade_id,scores.score_number, IF(scores.score_number<5 ,1,0) 
            AS bad_score, IF( scores.score_number >= 5 AND scores.score_number < 6.5 ,1,0) AS complete_score, 
            IF( scores.score_number >= 6.5 AND scores.score_number < 8 ,1,0) AS good_score, 
            IF( scores.score_number >= 8 ,1,0) AS excellent_score FROM scores
	        INNER JOIN tests ON scores.test_code = tests.test_code WHERE tests.grade_id = '$grade_id') as total_score";
        } else {
            $sql = "SELECT SUM(bad_score) AS bad, SUM(complete_score) AS complete, SUM(good_score) AS good, 
            SUM(excellent_score) AS excellent FROM (select scores.score_number, IF(scores.score_number<5 ,1,0) 
            AS bad_score, IF( scores.score_number >= 5 AND scores.score_number < 6.5 ,1,0) AS complete_score, 
            IF( scores.score_number >= 6.5 AND scores.score_number < 8 ,1,0) AS good_score, 
            IF( scores.score_number >= 8 ,1,0) AS excellent_score FROM scores) as total_score";
        }
        // var_dump($sql); die;
        $this->set_query($sql);
        return $this->load_rows();
    }
}
