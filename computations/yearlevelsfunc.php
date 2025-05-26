<?php
include '../dbcon.php';

$page = ((isset($_POST['page'])) ? $_POST['page'] : "0");
$entries = ((isset($_POST['entries'])) ? $_POST['entries'] : "0");
$search = ((isset($_POST['search']) && $_POST['search'] != "") ? "AND yl.Year_level  LIKE '%" . $_POST['search'] . "%'" : "");
$progid = ((isset($_POST['progid'])) ? $_POST['progid'] : "");
$pyid = ((isset($_POST['pyid'])) ? $_POST['pyid'] : "");
$ylid = ((isset($_POST['ylid'])) ? $_POST['ylid'] : "");
$progtype = ((isset($_POST['progtype'])) ? "AND py.ProgStatus_id = '" . $_POST['progtype'] . "'" : "");
$progid = ((isset($_POST['progid'])) ? $_POST['progid'] : "");
$code = ((isset($_POST['progcode'])) ? $_POST['progcode'] : "");
$desc = ((isset($_POST['progdesc'])) ? $_POST['progdesc'] : "");
$dur = ((isset($_POST['progduration'])) ? $_POST['progduration'] : "");
$av = ((isset($_POST['progavailability'])) ? $_POST['progavailability'] : "");
$program = isset($_POST['program']) ? $_POST['program'] : '';
$unit_old = isset($_POST['unit_old']) ? $_POST['unit_old'] : '';
$unit_new = isset($_POST['unit_new']) ? $_POST['unit_new'] : '';
$lab_old = isset($_POST['lab_old']) ? $_POST['lab_old'] : '';
$lab_new = isset($_POST['lab_new']) ? $_POST['lab_new'] : '';
$comp_old = isset($_POST['comp_old']) ? $_POST['comp_old'] : '';
$comp_new = isset($_POST['comp_new']) ? $_POST['comp_new'] : '';
$nstp_old = isset($_POST['nstp_old']) ? $_POST['nstp_old'] : '';
$nstp_new = isset($_POST['nstp_new']) ? $_POST['nstp_new'] : '';

if (isset($_POST['manual'])) {
    echo '
    <div class="row g-3">
        <div class="row me-2 mt-2">
            <button class="btn btn-sm btn-primary w-auto tutorial_btn" data-bs-toggle="collapse" data-bs-target="#tutorial_manage_programs">How to manage programs?</button>
            <div class="collapse border m-3" id="tutorial_manage_programs" data-bs-parent="#accordion">
                <ul>
                    <li><span>In this page, you can see the list of programs. </span></li>
                    <img width="1000" src="../photos_tutorial/PROGRAMS/1.png">
                    <li><span>Click the "Add program" button to add a new program, then click "Submit".</span></li>
                    <img class="img-fluid" src="../photos_tutorial/PROGRAMS/2.png">
                    <li><span>Click the "Update" button to update a program, then click "Submit".</span></li>
                    <img class="img-fluid" src="../photos_tutorial/PROGRAMS/3.png">
                    <li><span>In the "Manage Year Level" button, you can add year levels to the programs</span></li>
                    <li>Select program</li>
                    <img src="../photos_tutorial/PROGRAMS/4.png">
                    <li>Select program type</li>
                    <img src="../photos_tutorial/PROGRAMS/5.png">
                    <li>These are the year levels under that program.</li>
                    <img src="../photos_tutorial/PROGRAMS/6.png">
                    <li>Click the "Add year level" button to add a new year level to the program, then select the year level you want to add.</li>
                    <img src="../photos_tutorial/PROGRAMS/7.png">
                </ul>    
            </div>
        </div>
    </div>
    ';
}
if (isset($_POST['delpayment'])) {
    $payment_id = isset($_POST['payment_id']) ? trim($_POST['payment_id']) : '';

    // Check if payment exists
    $sql_check = "SELECT `program` FROM `computation_fees` WHERE `id` = ?";
    $res_check = queryOneParam($sql_check, [$payment_id]);
    if (mysqli_num_rows($res_check) == 0) {
        echo "Computation Not Found";
        return;
    }

    $row = mysqli_fetch_array($res_check);
    $payment_name = $row['program'];

    // Delete payment
    $sql = "DELETE FROM `computation_fees` WHERE `id` = ?";
    $res = queryOneParam($sql, [$payment_id]);

    if ($res == "") {
        addlogs("Deleted a computation: " . $payment_name);
        echo "Success";
    } else {
        echo "Error While Deleting Computation";
    }
}



if (isset($_POST['addfee'])) {
    $program = isset($_POST['program']) ? trim($_POST['program']) : '';
    $unit_old = isset($_POST['unit_old']) ? trim($_POST['unit_old']) : '';
    $unit_new = isset($_POST['unit_new']) ? trim($_POST['unit_new']) : '';
    $lab_old = isset($_POST['lab_old']) ? trim($_POST['lab_old']) : '';
    $lab_new = isset($_POST['lab_new']) ? trim($_POST['lab_new']) : '';
    $comp_old = isset($_POST['comp_old']) ? trim($_POST['comp_old']) : '';
    $comp_new = isset($_POST['comp_new']) ? trim($_POST['comp_new']) : '';
    $nstp_old = isset($_POST['nstp_old']) ? trim($_POST['nstp_old']) : '';
    $nstp_new = isset($_POST['nstp_new']) ? trim($_POST['nstp_new']) : '';

    // Check if program already exists
    $sql_check = "SELECT * FROM `computation_fees` WHERE `program` = ?";
    $res_check = queryOneParam($sql_check, [$program]);
    if (mysqli_num_rows($res_check) > 0) {
        echo "Program Already Used";
        return;
    }

    $sql = "INSERT INTO `computation_fees` (`program`, `unit_old`, `unit_new`, `lab_old`, `lab_new`, `comp_old`, `comp_new`, `nstp_old`, `nstp_new`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [$program, $unit_old, $unit_new, $lab_old, $lab_new, $comp_old, $comp_new, $nstp_old, $nstp_new];
    $res = queryOneParam($sql, $params);

    if ($res == "") {
        addlogs("Added a computation: " . $program);
        echo "Added";
    } else {
        echo "Error While Adding Computation";
    }
}

if (isset($_POST['upprogram'])) {
   $program = isset($_POST['program']) ? trim($_POST['program']) : '';
    $unit_old = isset($_POST['unit_old']) ? trim($_POST['unit_old']) : '';
    $unit_new = isset($_POST['unit_new']) ? trim($_POST['unit_new']) : '';
    $lab_old = isset($_POST['lab_old']) ? trim($_POST['lab_old']) : '';
    $lab_new = isset($_POST['lab_new']) ? trim($_POST['lab_new']) : '';
    $comp_old = isset($_POST['comp_old']) ? trim($_POST['comp_old']) : '';
    $comp_new = isset($_POST['comp_new']) ? trim($_POST['comp_new']) : '';
    $nstp_old = isset($_POST['nstp_old']) ? trim($_POST['nstp_old']) : '';
    $nstp_new = isset($_POST['nstp_new']) ? trim($_POST['nstp_new']) : '';

    $arr = [];
    array_push($arr, $program);
    array_push($arr, $unit_old);
    array_push($arr, $unit_new);
    array_push($arr, $lab_old);
    array_push($arr, $lab_new);
    array_push($arr, $comp_old);
    array_push($arr, $comp_new);
    array_push($arr, $nstp_old);
    array_push($arr, $nstp_new);
    array_push($arr, $progid);

    $sql = "UPDATE
        `computation_fees`
        SET
            `program` = ?,
            `unit_old` = ?,
            `unit_new` = ?,
            `lab_old` = ?,
            `lab_new` = ?,
            `comp_old` = ?,
            `comp_new` = ?,
            `nstp_old` = ?,
            `nstp_new` = ?
        WHERE
            `id` = ?";
    $arr1 = [];
    array_push($arr1, $program);
    $sql1 = "SELECT * FROM `computation_fees` af WHERE af.program=?";
    $res1 = queryOneParam($sql1, $arr1);
    if (mysqli_num_rows($res1) > 0) {
        $r = mysqli_fetch_array($res1);
        if ($r[0] == $progid) {
            $res = queryOneParam($sql, $arr);
            if ($res == "") {
                addlogs("Update a computation: " . $code);
            }
            echo ($res == "") ? "Success" : "Error While Updating Computation";
        } else { 
            echo "Computation Name Already Used";
        }
    } else {
        $res = queryOneParam($sql, $arr);
        echo ($res == "") ? "Success" : "Error While Updating Computation";
    }
}




if (isset($_POST['setpgs'])) {

    $sql = "SELECT COUNT(*) FROM
    `program_year` py
    LEFT JOIN year_level yl ON
    py.Year_level_id = yl.year_level_id
    WHERE 1 AND py.Program_list_id='$progid' $search $progtype";
    /* echo $sql; */

    pgs($sql, $entries);
}

if (isset($_POST['settb'])) {
    if ($page == "" || $page == "0") {
        $page = '0';
    } else {
        $page = ceil($page * $entries);
    }
    $limit = "LIMIT $page,$entries";

    $data = "";
    $sql = "SELECT
    py.Program_year_id,
    yl.Year_level,
    (SELECT COUNT(*) FROM program_year_sections pys WHERE pys.Program_year_id=py.Program_year_id)
    FROM
        `program_year` py
    LEFT JOIN year_level yl ON
    py.Year_level_id = yl.year_level_id
    WHERE 1 AND py.Program_list_id='$progid'
    $search
    $progtype
     ORDER BY yl.year_level_id ASC $limit  ";

    /* echo $sql; */
    $res = execquery($sql);
    $numrows = mysqli_num_rows($res);
    $cnt = 1;
    if ($numrows > 0) {

        $data .= '
    <thead>
        <tr>
            <th>#</th>
            <th>Year Lvl</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3"> <button onclick="setupaddylvl(this);" class="btn btn-sm btn-primary">Add Year lvl</button></td>
        </tr>
    ';
        while ($r = mysqli_fetch_array($res)) {
            $data .= '    <tr>
        <td class="border">' . $cnt . '</td>
        <td class="border">' . $r[1] . '</td>
        <td id="'.$r[0].'">
        ' . ((!$r[2]) ? '<button value="' . $r[0] . '" onclick="delylvl(this);" class="btn btn-sm btn-danger">Delete</button>' : '') . '
        </td>
    </tr>';
            $cnt++;
        }

        $data .= '</tbody>';
    } else {
        $data .= '<tbody>
    <tr>
            <td colspan="3"> <button onclick="setupaddylvl(this);" class="btn btn-sm btn-primary">Add Year lvl</button></td>
        </tr>
    <tr>
        <td colspan="3" class="fs-3">No Year lvl Found!</td>
    </tr>
    </tbody>
    ';
    }


    echo  $data;
}
