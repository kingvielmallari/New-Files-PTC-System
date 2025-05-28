<?php
// Load the database configuration file 
include_once '../dbcon.php';
$sec = $_GET['sec'];
$secid = $_GET['secid'];

// Filter the excel data 
function filterData(&$str)
{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

// Excel file name for download 
$fileName = $sec . " List.xls";

// Column names 
$fields = array('Student ID', 'FULL NAME');
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n";

// Fetch records from database 
$sql = 'SELECT
s.Student_idno,
CONCAT(
    s.Student_lastname,
    ", ",
    s.Student_firstname,
    " ",
    IFNULL(s.Student_middlename, "N/A")
) AS `Full Name`
FROM
`enrolledstudents` es
JOIN `students` s ON
es.Student_id = s.Student_idno
WHERE `Program_section` = "' . $secid . '"';
$res = mysqli_query($conn, $sql);
if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_array($res)) {
        $lineData = array($row['Student_idno'], ucwords($row['Full Name']));
        array_walk($lineData, 'filterData');
        $excelData .= implode("\t", array_values($lineData)) . "\n";
    }
} else {
    $excelData .= 'No records found...' . "\n";
}

ob_end_clean();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\"");
// Render excel data 
echo $excelData;
/* $excelData->save('php://output'); */
exit;
