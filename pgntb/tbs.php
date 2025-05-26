<?php
require_once '../dbcon.php';

if (isset($_POST['tb'])) {
    $search = $_POST['s'];
    $page = $_POST['p'];
    $entries = $_POST['e'];
    $tbname = $_POST['t'];
    if ($tbname == "programstb") {
        tb(sqls($tbname, $page, $entries, $search), $tbname, "6");
    } else if ($tbname == "subjectstb") {
        tb(sqls($tbname, $page, $entries, $search), $tbname, "9");
    } else if ($tbname == "instructorstb" ||  $tbname == "studentstb" || $tbname == "gdumptable") {
        tb(sqls($tbname, $page, $entries, $search), $tbname, "8");    
    } else if ($tbname == "feestb") {
        tb(sqls($tbname, $page, $entries, $search), $tbname, "6");
    } else if ($tbname == "comptb") {
        tb(sqls($tbname, $page, $entries, $search), $tbname, "6");    
    } else {
        tb(sqls($tbname, $page, $entries, $search), $tbname, "9");
    }
}


function sqls($tbname, $page, $entries, $search)
{
    if ($page == "" || $page == "0") {
        $page = '0';
    } else {
        $page = ceil($page * $entries);
    }
    $arr = array();
    $sql = "";
    $res = "";
    array_push($arr, $search);
    if ($tbname == "transactiontable") {
        $sql = "SELECT
        sr.idstud_req,
        st.Student_idno,
        concat(st.Student_lastname, ', ', st.Student_firstname, ' ', ifnull(st.Student_middlename, 'N/A')) as fn,
        concat(tr.transaction_name, if(sr.cog != '', concat(' (',es.School_year, ' - ', es.Semester,' Semester)'), '')) as tname,
        concat('₱ ',tr.amount) as amt,
        sr.transaction_date,
        sr.status,
        ifnull(sr.payment, 'Not Paid') as paidstat,
        ifnull(sr.payment, 'Not Paid') as foraction,
        sr.cog
      FROM
        `stud_req` sr
        join `students` st ON
        sr.stud_id = st.Student_idno
        join `transaction` tr ON
        sr.transaction_id = tr.idtransaction
        left join `enrolledstudents` es ON
        sr.cog = es.Enrolled_student_id
        WHERE CONCAT(st.Student_idno,st.Student_lastname,sr.idstud_req) like CONCAT ('%', ?, '%')
      order by sr.transaction_date desc 
        LIMIT $page,$entries";
    }
    if ($tbname == "programstb") {
        $sql = "SELECT
        *,
	    (SELECT COUNT(*) FROM curriculums c WHERE c.Subject_id=s.Subject_id) as `checker`,
        (SELECT COUNT(*) FROM program_year py WHERE pl.Program_list_id=py.Program_list_id)
        FROM
        `program_list` pl 
          LEFT JOIN program_year py 
            ON py.Program_list_id=pl.Program_list_id
            LEFT JOIN curriculums c
            ON c.Program_year_id=py.Program_year_id
            LEFT JOIN subjects s ON
            c.Subject_id=s.Subject_id 
        WHERE CONCAT(pl.Program_code,pl.Program_desc)  LIKE CONCAT ('%', ?, '%') 
             GROUP BY pl.Program_list_id
        ORDER BY pl.Program_list_id DESC LIMIT $page,$entries";
        /* echo $sql ; exit();  */
    }
    if ($tbname == "subjectstb") {
        $sql = "SELECT
        *,	(SELECT COUNT(*) FROM curriculums c WHERE c.Subject_id=s.Subject_id) as `checker`
        FROM
        `subjects` s

 WHERE CONCAT(s.Subject_code,s.Subject_title)   LIKE CONCAT ('%', ?, '%')  
        ORDER BY s.Subject_id DESC LIMIT $page,$entries";
    }
    if ($tbname == "instructorstb") {
        $sql = "SELECT i.Instructor_id,i.Instructor_idno,i.Instructor_firstname,i.Instructor_middlename,i.Instructor_lastname,i.Instructor_email, i.password, (SELECT COUNT(*) FROM `class_schedules` WHERE `Instructor_id` = i.Instructor_id)
        FROM `instructors` i WHERE CONCAT(i.Instructor_lastname,i.Instructor_firstname,i.Instructor_idno,i.Instructor_email) LIKE CONCAT ('%', ?, '%') 
        ORDER BY i.Instructor_id DESC LIMIT $page,$entries ";
    }
    if ($tbname == "studentstb") {

        /*  $sql = "SELECT * FROM `students` s WHERE CONCAT(s.Student_idno,s.Student_firstname,s.Student_middlename,s.Student_lastname,s.Student_email) LIKE CONCAT ('%', ?, '%')
        ORDER BY s.Student_id DESC LIMIT $page,$entries "; */
        $sql = "SELECT *, 
            IF(EXISTS(SELECT * FROM `enrolledstudents` es WHERE es.Student_id = s.Student_idno) = 1, 
            '<button class=\"btn btn-warning btn-sm\" onclick=\"displayData(this);\">Display Data</button>', 
            '<button class=\"btn btn-warning btn-sm\" onclick=\"displayData(this);\">Display Data</button>
            <button class=\"btn btn-danger btn-sm\" onclick=\"deleteStudent(this);\"><i class=\"bi bi-trash\"></i></button>') as `checkdata`
        FROM `students` s
        where
        CONCAT(s.Student_idno,s.Student_firstname,s.Student_middlename,s.Student_lastname,s.Student_email) LIKE CONCAT ('%', ?, '%')
         ORDER BY s.Student_id DESC  LIMIT $page,$entries ";
    }
    if ($tbname == "gdumptable") {
        $sql = "SELECT
        *
    FROM
        `gradesdump` g
    WHERE 
        CONCAT(g.std_id, g.std_name,g.section,g.prof_name,g.prof_id,g.subcode,g.subtitle,g.type,g.gradesdump_id) LIKE CONCAT ('%', ?, '%') ORDER By g.gradesdump_id DESC LIMIT $page, $entries";
        /* $sql = "SELECT
        *
    FROM
        `gradesdump` g
    WHERE 
        CONCAT(g.std_id, g.std_name,g.section,g.prof_name,g.prof_id,g.subcode,g.subtitle,g.type,g.gradesdump_id) LIKE CONCAT ('%', ?, '%') and g.status is null ORDER By g.status ASC LIMIT $page, $entries"; */
    }
     if ($tbname == "feestb") {
        $sql = "SELECT 
        id, 
        payment_name,
        paying_new,
        paying_old,
        unifast_new,
        unifast_old,
        executive_new,
        executive_old
        FROM 
            assessment_fees 
        WHERE 
            CONCAT(payment_name, IFNULL(paying_new,''), IFNULL(paying_old,''), IFNULL(unifast_new,''), IFNULL(unifast_old,''), IFNULL(executive_new,''), IFNULL(executive_old,''), IFNULL(status,'')) 
            LIKE CONCAT('%', ?, '%') 
        ORDER BY id ASC 
        LIMIT $page, $entries";
    }

    if ($tbname == "comptb") {
        $sql = "SELECT 
            id, 
            program,
            unit_new,
            unit_old,
            lab_new,
            lab_old,
            comp_new,
            comp_old,
            nstp_old,
            nstp_new,
            status
        FROM 
            computation_fees 
        WHERE 
            CONCAT(program, IFNULL(unit_new,''), IFNULL(unit_old,''), IFNULL(lab_new,''), IFNULL(lab_old,''), IFNULL(comp_new,''), IFNULL(comp_old,''), IFNULL(nstp_old,''), IFNULL(nstp_new,''), IFNULL(status,'')) 
            LIKE CONCAT('%', ?, '%') 
        ORDER BY program ASC 
        LIMIT $page, $entries";
    }


    $res = queryOneParam($sql, $arr);

    return $res;
}


function tb($res, $t, $cspan)
{
    $newsearch = $_POST['s'] ?? '';
    if ($newsearch !== "") {
        addlogs("Search a data in table " . $t . ": " . $newsearch);
    }

    require_once '../Transaction/functr.php';
    $data = '';
    if ($t == "programstb") {
        $data .= '<tr>
        <td colspan="' . $cspan . '">
            <button class="btn  btn-primary" onclick="setaddprogram(this);"> <span>Add Program </span><i class="bi bi-journal-plus"></i></button>
        </td>
    </tr>';
    }
    if ($t == "subjectstb") {
        $data .= '<tr>
        <td colspan="' . $cspan . '">
            <button class="btn  btn-primary" onclick="setaddsubject(this);"> <span>Add Subject </span><i class="bi bi-journal-plus"></i></button>
        </td>
    </tr>';
    }
    if ($t == "instructorstb") {
        $data .= '<tr>
        <td colspan="' . $cspan . '">
            <button class="btn  btn-primary" onclick="setaddinstructor(this);"> <span>Add Instructor </span><i class="bi bi-person-plus-fill"></i></button>
        </td>
    </tr>';
    }
    if ($t == "studentstb") {
        $data .= '<tr class="hideelem">
        <td colspan="' . $cspan . '"">
            <button class="btn  btn-primary" onclick="setaddstudent(this);"> <span>Add Student </span><i class="bi bi-person-plus-fill"></i></button>
        </td>
    </tr>';
    }
     if ($t == "feestb") {
        $data .= '<tr>
        <td colspan="100%">
            <button class="btn btn-primary" onclick="setaddprogram(this);"><span>Add Fee </span><i class="bi bi-cash"></i></button>
        </td>
    </tr>';
    }
    if ($t == "comptb") {
        $data .= '<tr>
        <td colspan="100%">
            <button class="btn btn-primary" onclick="setaddprogram(this);"><span>Add Computation </span><i class="bi bi-cash"></i></button>
        </td>
    </tr>';
    }

    $c = 0;
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_array($res)) {
            $c++;
            if ($t == "transactiontable") {
                $timestamp = strtotime($row[5]);
                $data .= '
                <tr>
                    <td><span id="rn' . $row[0] . '">' . $row[0] . '</span></td>
                    <td><span id="sn' . $row[0] . '">' . $row[1] . '</span></td>
                    <td><span id="sf' . $row[0] . '">' . $row[2] . '</span></td>
                    <td><span id="tn' . $row[0] . '">' . $row[3] . '</span></td>
                    <td><span id="am' . $row[0] . '">' . $row[4] . '</span></td>
                    <td><span id="dr' . $row[0] . '">' . date("M d, Y h:i A", $timestamp) . '</span></td>
                    <td><span id="ss' . $row[0] . '">' . $row[6] . '</span></td>
                    <td><span id="ps' . $row[0] . '">' . checkpayment($row[8], $row[0], "Status") . '</span></td>
                    <td><span id = "p' . $row[0] . '">' . checkpayment($row[8], $row[0], "Action") . '</span></td>
                  </tr>
                ';
            }
            if ($t == "programstb") {
                $data .= ' <tr>
                <td>' . $c . '</td>
                <td>' . $row[1] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $row[3] . '</td>
                <td>' . $row[4] . '</td>
                <td>
                <button class="btn btn-sm btn-success" onclick="setupprogram(this);" value="' . $row[0] . '">Update</button>
                ' . ((!$row[5]) ? '<button class="btn btn-sm btn-danger" onclick="delprog(this);" value="' . $row[0] . '">Delete</button>' : '') . '</td>
            </tr>';
            }
            if ($t == "subjectstb") {
                $data .= ' <tr>
                <td>' . $row[0] . '</td>
                <td>' . $row[1] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $row[3] . '</td>
                <td>' . $row[4] . '</td>
                <td>' . $row[7] . '</td>
                <td>' . $row[5] . '</td>
                <td>' . $row[6] . '</td>
                <td>
                <button class="btn btn-sm btn-success" onclick="setupsubject(this);" value="' . $row[0] . '">Update</button>
                '
                    . ($_SESSION['usertype'] === "superadmin" ? ($row['checker'] <= 0 ? '<button class="btn btn-sm btn-danger" value="' . $row[0] . '" onclick="removesubject(this)">Delete</button>' : '') : ''
                    )
                    .
                    '
                
                </td>
            </tr>';
            }
              if ($t == "comptb") {
                $data .= ' <tr>
                <td>' . $c . '</td>
                <td>' . $row[1] . '</td> <!-- program -->
                <td>' . $row[3] . '</td> <!-- unit_old -->
                <td>' . $row[2] . '</td> <!-- unit_new -->
                <td>' . $row[5] . '</td> <!-- lab_old -->
                <td>' . $row[4] . '</td> <!-- lab_new -->
                <td>' . $row[7] . '</td> <!-- comp_old -->
                <td>' . $row[6] . '</td> <!-- comp_new -->
                <td>' . $row[8] . '</td> <!-- nstp_old -->
                <td>' . $row[9] . '</td> <!-- nstp_new -->
                <td>
                <button class="btn btn-sm btn-success" onclick="setupprogram(this);" value="' . $row[0] . '">Update</button>
                </td>
            </tr>';
            }
            if ($t == "feestb") {
                $data .= ' <tr>
                <td>' . $c . '</td>
                <td>' . $row[1] . '</td>
                <td>' . $row[3] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $row[5] . '</td>
                <td>' . $row[4] . '</td>
                 <td>' . $row[7] . '</td>
                <td>' . $row[6] . '</td>

                <td>
                <button class="btn btn-sm btn-success" onclick="setupprogram(this);" value="' . $row[0] . '">Update</button>
                </td>
            </tr>';
            }
            if ($t == "instructorstb") {
                $data .= '
                <tr>
                    <td>' . $row[0] . '</td>
                    <td>' . $row[2] . '</td>
                    <td>' . $row[3] . '</td>
                    <td>' . $row[4] . '</td>
                    <td>' . $row[5] . '</td>
                    <td ss=' . $row[6] . '>
                        <button class="btn btn-sm btn-success" onclick="setupinstructor(this);" value="' . $row[0] . '">Update</button>
                        ';
                $data .= ((int)$row[7] > 0 ? ' <button class="btn btn-sm btn-warning ms-1" onclick="dpscheds(this);" value="' . $row[0] . '">Display Schedules</button>' : '');
                if ($_SESSION['usertype'] === "superadmin") {
                    $data .= ((int)$row[7] > 0 ? '' : '<button class="btn btn-sm btn-danger" value="' . $row[0] . '" df="' . $row[6] . '" onclick="removeprof(this)">Delete</button>');
                    if ($row['password'] !== NULL) {
                        $data .= '
                    <button class="btn btn-sm btn-secondary" onclick="resetpass(this);">Remove Password</button>
                    ';
                    }
                }
                $data . ' 
                    </td>
                </tr>
                ';
            }
            if ($t == "studentstb") {
                $data .= ' <tr>
                <td>' . $row[0] . '</td>
                <td>' . $row[1] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $row[3] . '</td>
                <td>' . $row[4] . '</td>
                <td>' . $row[5] . '</td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="setupstudent(this);" value="' . $row[0] . '">Update</button>
                    
                   
                    
                    ' . $row['checkdata'] . '
                    <button title="Student Personal Details" class="btn btn-sm btn-info" onclick="set_update_student(this);" value="' . $row[1] . '"> <i class="bi bi-info-lg"></i></button>


                 
                </td>
                
            </tr>';
            }
            if ($t == "gdumptable") {
                $data .= ' <tr>
                <td>' . $row[0] . '</td>
                <td>' . $row[1] . '</td>
                <td ondblclick="editName(this);">' . ($row[2] == NULL ? "" : $row[2]) . '</td>
                <td>' . $row[3] . '</td>
                <td>' . $row[9] . '(' . $row[4] . ')</td>
                <td>' . $row[7] . '</td>
                <td>' . btnDisplay($row[0], $row[1], ($row[2] == NULL ? "" : $row[2])) . '</td>
            </tr>';
            }
            /*    <button class="btn btn-sm btn-primary" title="Duplicate Details of This student for ladderize" onclick="duplicate_data(this);" value="' . $row[0] . '">
                    
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
</svg>
                    </button>
 */
        }
    } else {

        $data .= '
        <tr>
            <td colspan = "' . $cspan . '" class="text-center">No Data Found</td>
        </tr>
        ';
    }

    echo $data;
}

//<!--<button class="btn btn-sm btn-primary checkdata" data-bs-toggle="modal" data-bs-target="#staticBackdrop" onclick="checkData(this);" value="' . $row[0] . '">Check Data</button>-->
//<button class="btn btn-sm btn-primary checkdata" onclick="checkData(this);" value="' . $row[0] . '" stdid="' . $row[1] . '" stdname="' . $row[2] . '">Check Data</button>
function btnDisplay($id, $stdid, $stdname)
{
    $d = '';
    $sql = "SELECT g.status FROM `gradesdump` g WHERE g.`gradesdump_id` = '$id'";
    $res = queryWOParam($sql);
    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_array($res);
        if ($row[0] == "No Data Found") {
            $d = '<button class="btn btn-sm btn-warning nodata" data-bs-toggle="modal" data-bs-target="#staticBackdrop" onclick="addStudent(this);" value="' . $id . '">No Data Found</button>';
        } else if ($row[0] == "Done") {
            $d = '<button class="btn btn-sm btn-success done">Done</button>';
        } else {
            $d = '<button class="btn btn-sm btn-primary checkdata" onclick="checkData(this);" value="' . $id . '" stdid="' . $stdid . '" stdname="' . $stdname[2] . '">Check Data</button>';
        }
    }
    return $d;
}


function diffoc($row, $t)
{
    if ($t == "employee_tb") {
        return '<a href="#" data-bs-toggle="modal" data-bs-target="#viewDetails" onclick="viewDetails(\'' . $row[4] . '\');"><i class="bi bi-eye lead"></i></a>';
    } else if ($t == "admintb" || $t == "newadmintb") {
        return '<a href="#" data-bs-toggle="modal" data-bs-target="#viewSched" onclick="viewSched(\'' . $row[0] . '|Admin\');"><i class="bi bi-eye lead"></i></a>';
    } else if ($t == "proftb" || $t == "newproftb" || $t == "exproftb" || $t == "exprofptb") {
        return '<a href="#" data-bs-toggle="modal" data-bs-target="#viewSched" onclick="viewSched(\'' . $row['proftb_id'] . '|Professor\');"><i class="bi bi-eye lead"></i></a>';
    }
}
