<?php

require_once '../dbcon.php';

function con()
{
    $c = new con();
    return $c->con();
}


if (isset($_POST['pages'])) {
    $search = $_POST['s'];
    $page = $_POST['p'];
    $entries = $_POST['e'];
    $tbname = $_POST['t'];
    $count = 0;
    $arr = array();
    $sql = "";
    array_push($arr, $search);
    if ($tbname == "employee_tb") {
        $sql = "SELECT
        COUNT(*)
        FROM
            `employee_tb` emp
        WHERE CONCAT(emp.last_name,emp.first_name,emp.id_no) LIKE ?
        ORDER BY
        emp.last_name ASC 
        LIMIT $page,$entries";
        $res = mysqli_fetch_array(queryOneParam($sql, $arr));
        $count = $res[0];
    }
    pages($count, $entries);
}


function pages($count, $entries)
{
    $pages = '';
    if ($count > 0) {
        $p = $count / $entries;
        $p = ceil($p);

        for ($i = 0; $i < $p; $i++) {
            $pages .= '<option   value=\'' . $i . '\' >' . $i + 1 . '</option>';
        }
    }
    echo $pages;
}

function queryOneParam($sql, $param)
{
    $c = new con();
    $conn = $c->con();
    $c = count($param);
    $stmt = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, str_repeat('s', $c), ...$param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result;
}
