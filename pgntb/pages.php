<?php

require_once '../dbcon.php';

function counts($tbname, $search)
{
    $sql = "";
    if ($tbname == "transactiontable") {
        $sql = "SELECT
     COUNT(*)
      FROM
        `stud_req` sr
        join `students` st ON
        sr.stud_id = st.Student_idno
        join `transaction` tr ON
        sr.transaction_id = tr.idtransaction
        left join `enrolledstudents` es ON
        sr.cog = es.Enrolled_student_id
        WHERE CONCAT(st.Student_id) like concat('%$search%')
      order by sr.transaction_date desc ";
    }
    if ($tbname == "programstb") {
        $sql = "SELECT
        COUNT(*)
        FROM
        `program_list` pl WHERE CONCAT(pl.Program_code,pl.Program_desc) LIKE concat('%$search%') ";
    }
    if ($tbname == "subjectstb") {
        $sql = "SELECT
        COUNT(*)
        FROM
        `subjects` s WHERE CONCAT(s.Subject_code,s.Subject_title) LIKE concat('%$search%') ";
    }
    if ($tbname == "feestb") {
        $sql = "SELECT
            COUNT(*)
            FROM
            `assessment_fees` af
            WHERE CONCAT(af.payment_name) LIKE concat('%$search%')";
    }
      if ($tbname == "comptb") {
        $sql = "SELECT
            COUNT(*)
            FROM
            `computation_fees` af
            WHERE CONCAT(af.payment_name) LIKE concat('%$search%')";
    }
    if ($tbname == "instructorstb") {
        $sql = "SELECT
        COUNT(*)
        FROM
        `instructors` i  WHERE CONCAT(i.Instructor_lastname,i.Instructor_firstname,i.Instructor_idno,i.Instructor_email) LIKE concat('%$search%') ";
    }
    if ($tbname == "studentstb") {
        $sql = "SELECT
        COUNT(*)
        FROM
        `students` s WHERE CONCAT(s.Student_idno,s.Student_firstname,s.Student_middlename,s.Student_lastname,s.Student_email) LIKE concat('%$search%')";
    }
    if ($tbname == "gdumptable") {
        $sql = "SELECT
        COUNT(*)
    FROM
        `gradesdump` g
    WHERE 
        CONCAT(g.std_id, g.std_name,g.section,g.prof_name,g.prof_id,g.subcode,g.subtitle,g.type) LIKE concat('%$search%')";
    }

    if ($tbname == "advtb") {
        $sql = "SELECT
       COUNT(*)
    FROM
        `enrolledstudents` es
    JOIN `students` st ON
        es.Student_id = st.Student_idno
    JOIN `enrolled_subjects` esub ON
        esub.Enrolled_student_id = es.Enrolled_student_id
        JOIN `program_year` py ON
        py.Program_year_id = es.Program_year
        JOIN `year_level` yl ON
        yl.year_level_id = py.Year_level_id
        join `studtype` typ ON
        typ.studType_id = es.student_type
    WHERE
        es.`Program_year` = ? and esub.Remarks = 'FAILED' AND es.Advising_stat IS NULL 
        AND CONCAT(st.Student_lastname, st.Student_firstname, st.Student_middlename, st.Student_idno) LIKE concat('%$search%')
      ";
    }
    return $sql;
}

if (isset($_POST['pages'])) {
    $search = $_POST['s'];
    $entries = $_POST['e'];
    $tbname = $_POST['t'];
    /*   $arr = array();

    if ($tbname == "advtb") {
        array_push($arr, $_POST['yr']);
        $type = $_POST['type'] ?? "";
        if ($type != "") {
            array_push($arr, $type);
        }
    }
    array_push($arr, $search); */

    $count = counts($tbname, $search);
    pgs($count, $entries);
}
