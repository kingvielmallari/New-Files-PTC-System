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
$payment_name = isset($_POST['payment_name']) ? $_POST['payment_name'] : '';
$paying_old = isset($_POST['paying_old']) ? $_POST['paying_old'] : '';
$paying_new = isset($_POST['paying_new']) ? $_POST['paying_new'] : '';
$unifast_old = isset($_POST['unifast_old']) ? $_POST['unifast_old'] : '';
$unifast_new = isset($_POST['unifast_new']) ? $_POST['unifast_new'] : '';

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
    $sql_check = "SELECT `payment_name` FROM `assessment_fees` WHERE `id` = ?";
    $res_check = queryOneParam($sql_check, [$payment_id]);
    if (mysqli_num_rows($res_check) == 0) {
        echo "Payment Not Found";
        return;
    }

    $row = mysqli_fetch_array($res_check);
    $payment_name = $row['payment_name'];

    // Delete payment
    $sql = "DELETE FROM `assessment_fees` WHERE `id` = ?";
    $res = queryOneParam($sql, [$payment_id]);

    if ($res == "") {
        addlogs("Deleted a payment: " . $payment_name);
        echo "Success";
    } else {
        echo "Error While Deleting Payment";
    }
}



if (isset($_POST['addfee'])) {
    $payment_name = isset($_POST['payment_name']) ? trim($_POST['payment_name']) : '';
    $paying_old = isset($_POST['paying_old']) ? trim($_POST['paying_old']) : '';
    $paying_new = isset($_POST['paying_new']) ? trim($_POST['paying_new']) : '';
    $unifast_old = isset($_POST['unifast_old']) ? trim($_POST['unifast_old']) : '';
    $unifast_new = isset($_POST['unifast_new']) ? trim($_POST['unifast_new']) : '';
    $executive_old = isset($_POST['executive_old']) ? trim($_POST['executive_old']) : '';
    $executive_new = isset($_POST['executive_new']) ? trim($_POST['executive_new']) : '';

    // Check if payment_name already exists
    $sql_check = "SELECT * FROM `assessment_fees` WHERE `payment_name` = ?";
    $res_check = queryOneParam($sql_check, [$payment_name]);
    if (mysqli_num_rows($res_check) > 0) {
        echo "Payment Name Already Used";
        return;
    }

    $sql = "INSERT INTO `assessment_fees` (`payment_name`, `paying_old`, `paying_new`, `unifast_old`, `unifast_new`, `executive_old`, `executive_new`)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params = [$payment_name, $paying_old, $paying_new, $unifast_old, $unifast_new, $executive_old, $executive_new];
    $res = queryOneParam($sql, $params);

    if ($res == "") {
        addlogs("Added a fee: " . $payment_name);
        echo "Added";
    } else {
        echo "Error While Adding Fee";
    }
}


if (isset($_POST['upprogram'])) {
    $payment_name = isset($_POST['payment_name']) ? trim($_POST['payment_name']) : '';
    $paying_old = isset($_POST['paying_old']) ? trim($_POST['paying_old']) : '';
    $paying_new = isset($_POST['paying_new']) ? trim($_POST['paying_new']) : '';
    $unifast_old = isset($_POST['unifast_old']) ? trim($_POST['unifast_old']) : '';
    $unifast_new = isset($_POST['unifast_new']) ? trim($_POST['unifast_new']) : '';
    $executive_old = isset($_POST['executive_old']) ? trim($_POST['executive_old']) : '';
    $executive_new = isset($_POST['executive_new']) ? trim($_POST['executive_new']) : '';

    $arr = [];
    array_push($arr, $payment_name);
    array_push($arr, $paying_old);
    array_push($arr, $paying_new);
    array_push($arr, $unifast_old);
    array_push($arr, $unifast_new);
    array_push($arr, $executive_old);
    array_push($arr, $executive_new);
    array_push($arr, $progid);
    $sql = "UPDATE
        `assessment_fees`
        SET
            `payment_name` = ?,
            `paying_old` = ?,
            `paying_new` = ?,
            `unifast_old` = ?,
            `unifast_new` = ?,
            `executive_old` = ?,
            `executive_new` = ?
        WHERE
            `id` = ?";
    $arr1 = [];
    array_push($arr1, $payment_name);
    $sql1 = "SELECT * FROM `assessment_fees` af WHERE af.payment_name=?";
    $res1 = queryOneParam($sql1, $arr1);
    if (mysqli_num_rows($res1) > 0) {
        $r = mysqli_fetch_array($res1);
        if ($r[0] == $progid) {
            $res = queryOneParam($sql, $arr);
            if ($res == "") {
                addlogs("Update a fee: " . $code);
            }
            echo ($res == "") ? "Success" : "Error While Updating Program";
        } else { 
            echo "Payment Name Already Used";
        }
    } else {
        $res = queryOneParam($sql, $arr);
        echo ($res == "") ? "Success" : "Error While Updating Program";
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
