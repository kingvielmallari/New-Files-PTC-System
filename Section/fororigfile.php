<?php
require_once('../import/vendor/autoload.php');
require_once '../dbcon.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;

$Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriterXlsx;


$datas = $_GET['data'];
$datas = explode("|", $datas);
$pysec = $datas[0];
$sy = $datas[1];
$sem = $datas[2];
$section = $datas[3];
$registrarhead = "";
$originalFile = 'EXCELFILES/CLASSRECORD.xlsx';

// Store the path of destination file
//$destination = '/Users/USER/Downloads';
$destination = '';
$con = con();

$sql = "SELECT
cs.Class_Scheduleid AS `id`,
sub.Subject_code AS `subcode`,
sub.Subject_title AS `subtitle`,
IF(sub.Subject_lec IS NULL OR sub.Subject_lec ='' ,'0',sub.Subject_lec),
IF(sub.Subject_lab IS NULL OR sub.Subject_lab='','0',sub.Subject_lab),
IF(sub.Subject_units IS NULL OR sub.Subject_units='','0',sub.Subject_units),
cs.Day AS `day`,
CONCAT(
    cs.Starting_time,
    ' - ',
    cs.Ending_time
) AS `time`,
TIMESTAMPDIFF(
    HOUR,
    STR_TO_DATE(cs.Starting_time, '%h:%i %p'),
    STR_TO_DATE(cs.Ending_time, '%h:%i %p')
) AS ttlh,
TIMESTAMPDIFF(
    HOUR,
    STR_TO_DATE(cs.Starting_time, '%h:%i %p'),
    STR_TO_DATE(cs.Ending_time, '%h:%i %p')
) * cs.Weeks AS ttlhw,
IFNULL(
    CONCAT(
        'Prof. ',
        ins.Instructor_lastname
    ),
    ''
) AS prof,
IFNULL(r.Room_no, ''),
ins.Instructor_id,
cs.Curriculum_id
FROM
    `class_schedules` cs
JOIN program_year_sections pys ON
    cs.Prog_year_section_id = pys.Prog_year_section_id
JOIN program_year py ON
    pys.Program_year_id = py.Program_year_id
JOIN program_list pl ON
    py.Program_list_id = pl.Program_list_id
JOIN year_level yl ON
    py.Year_level_id = yl.year_level_id
JOIN sections s ON
    pys.Section_id = s.Section_id
JOIN curriculums cur ON
    cs.Curriculum_id = cur.Curriculum_id
JOIN subjects sub ON
    cur.Subject_id = sub.Subject_id
LEFT JOIN rooms r ON
    cs.Room_id = r.Room_id
LEFT JOIN instructors ins ON
cs.Instructor_id = ins.Instructor_id
WHERE cs.Prog_year_section_id='$pysec' AND cs.Semester='$sem' AND cs.School_year='$sy'
ORDER BY STR_TO_DATE(cs.Starting_time,'%h:%i %p') ASC";

$res = mysqli_query($con, $sql);
if (mysqli_num_rows($res) > 0) {
    $newFolderPath = $sy . "_" . $sem . " Semester_" . $section;
    createDirectory($newFolderPath);
    while ($row = mysqli_fetch_array($res)) {
        $destination = '' . $newFolderPath . '/' . $section . " - " . $row[1] . '.xlsx';
        createFile($originalFile, $destination);
        writeScheduleDetails($destination, $Reader, $row[2], $row[1], $sem, $section, $row['day'] . " " . $row['time'], $sy, $row['Instructor_id'], $row['prof'], $registrarhead);
        writeStudentEnrolled($pysec, $sy, $sem, $row['Curriculum_id'], $destination, $Reader);
    }
    echo '
    <script>
        alert("Final Grade Sheet Created for ' . $section . '");
        window.location = "index.php";
    </script>
    ';
}



function createDirectory($section)
{
    if (!is_dir($section)) {
        mkdir($section);
    }
}

function createFile($originalFile, $destination)
{
    if (!copy($originalFile, $destination)) {
    } else {
    }
}
function writeScheduleDetails($destination, $Reader, $courseTitle, $courseCode, $sem, $section, $time, $sy, $profid, $profname, $registrarhead)
{

    // Copy the file from /user/desktop/geek.txt 
    // to user/Downloads/geeksforgeeks.txt'
    // directory

    // Read your Excel workbook

    try {
        $spreadSheet = $Reader->load($destination);
        $worksheet = $spreadSheet->getActiveSheet();
        $spreadSheetAry = $worksheet->toArray();
        $sheetCount = count($spreadSheetAry);
        $writer = new WriterXlsx($spreadSheet);

        $worksheet->setCellValue("C10", $courseTitle);
        $worksheet->setCellValue("C11", $courseCode);
        $worksheet->setCellValue("C12", $sem);
        $worksheet->setCellValue("G10", $section);
        $worksheet->setCellValue("G11", $time);
        $worksheet->setCellValue("G12", $sy);
        if ($profid != NULL) {
            $worksheet->setCellValue("A98", $profid);
            $worksheet->setCellValue("D98", $profname);
        }
        $worksheet->setCellValue("A100", $registrarhead);
        $writer->save($destination);
    } catch (Exception $e) {
        die('Error loading file "' . pathinfo($destination, PATHINFO_BASENAME) . '": ' . $e->getMessage());
    }
}

function writeStudentEnrolled($pysec, $sy, $sem, $cid, $destination, $Reader)
{
    $spreadSheet = $Reader->load($destination);
    $worksheet = $spreadSheet->getActiveSheet();
    $spreadSheetAry = $worksheet->toArray();
    $sheetCount = count($spreadSheetAry);
    $writer = new WriterXlsx($spreadSheet);
    $cellnum = 15;
    $con = con();
    $sqlenstd = "SELECT
    stds.Student_idno,
    CONCAT(
        stds.Student_lastname,
        ', ',
        stds.Student_firstname,
        IF(
            stds.Student_middlename IS NULL OR stds.Student_middlename = '' OR LOWER(stds.Student_middlename) = 'n/a',
            ' ',
            CONCAT(' ', stds.Student_middlename)
        )
    ) AS `fn`
FROM
    `enrolledstudents` enstd
JOIN `students` stds ON
    enstd.Student_id = stds.Student_idno
JOIN `enrolled_subjects` ensub ON
    enstd.Enrolled_student_id = ensub.Enrolled_student_id
WHERE
    enstd.Program_section = '$pysec' AND enstd.School_year = '$sy' AND enstd.Semester = '$sem' AND ensub.Curriculum_id = '$cid'";
    $res = mysqli_query($con, $sqlenstd);
    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_array($res)) {
            if ($cellnum <= 84) {
                $worksheet->setCellValue("C" . $cellnum, $row[0]);
                $worksheet->setCellValue("D" . $cellnum, $row[1]);
                $cellnum++;
            }
        }
    }
    $writer->save($destination);
    // echo $sqlenstd;
}



function schedTb($pysec, $sem, $sy)
{

    $con = con();
    $sql = "SELECT
    cs.Class_Scheduleid AS `id`,
    sub.Subject_code AS `subcode`,
    sub.Subject_title AS `subtitle`,
    IF(sub.Subject_lec IS NULL OR sub.Subject_lec ='' ,'0',sub.Subject_lec),
    IF(sub.Subject_lab IS NULL OR sub.Subject_lab='','0',sub.Subject_lab),
    IF(sub.Subject_units IS NULL OR sub.Subject_units='','0',sub.Subject_units),
    cs.Day AS `day`,
    CONCAT(
        cs.Starting_time,
        ' - ',
        cs.Ending_time
    ) AS `time`,
    TIMESTAMPDIFF(
        HOUR,
        STR_TO_DATE(cs.Starting_time, '%h:%i %p'),
        STR_TO_DATE(cs.Ending_time, '%h:%i %p')
    ) AS ttlh,
    TIMESTAMPDIFF(
        HOUR,
        STR_TO_DATE(cs.Starting_time, '%h:%i %p'),
        STR_TO_DATE(cs.Ending_time, '%h:%i %p')
    ) * cs.Weeks AS ttlhw,
    IFNULL(
        CONCAT(
            'Prof. ',
            ins.Instructor_lastname
        ),
        ''
    ) AS prof,
    IFNULL(r.Room_no, ''),
    ins.Instructor_id
    FROM
        `class_schedules` cs
    JOIN program_year_sections pys ON
        cs.Prog_year_section_id = pys.Prog_year_section_id
    JOIN program_year py ON
        pys.Program_year_id = py.Program_year_id
    JOIN program_list pl ON
        py.Program_list_id = pl.Program_list_id
    JOIN year_level yl ON
        py.Year_level_id = yl.year_level_id
    JOIN sections s ON
        pys.Section_id = s.Section_id
    JOIN curriculums cur ON
        cs.Curriculum_id = cur.Curriculum_id
    JOIN subjects sub ON
        cur.Subject_id = sub.Subject_id
    LEFT JOIN rooms r ON
        cs.Room_id = r.Room_id
    LEFT JOIN instructors ins ON
    cs.Instructor_id = ins.Instructor_id
    WHERE cs.Prog_year_section_id='$pysec' AND cs.Semester='$sem' AND cs.School_year='$sy'
    ORDER BY STR_TO_DATE(cs.Starting_time,'%h:%i %p') ASC";

    $res = mysqli_query($con, $sql);

    $numf = mysqli_num_fields($res);
    if (mysqli_num_rows($res) > 0) {
        $tb = '<table class="table table-bordered ">
    <thead class="text-center">
      <tr>
        <th scope="col">SUBJECT ​CODE</th>
        <th scope="col">SUBJECT TITLE</th>
        <th scope="col">LEC</th>
        <th scope="col">LAB</th>
        <th scope="col">UNIT</th>
        <th scope="col">DAY</th>
        <th scope="col">TIME</th>
        <th scope="col">HOURS</th>
        <th scope="col">HOURS/WEEKS</th>
        <th scope="col">INSTRUCTOR</th>
        <th scope="col">ROOM</th>
      </tr>
    </thead>
    <tbody>';
        $tlec = 0;
        $tlab = 0;
        $tunit = 0;
        $th = 0;
        $thw = 0;
        while ($r = mysqli_fetch_array($res)) {
            $tlec += $r[3];
            $tlab += $r[4];
            $tunit += $r[5];
            $th += $r[8];
            $thw += $r[9];

            $tb .= '<tr value="' . $r[0] . '">';
            for ($i = 1; $i < $numf; $i++) {
                $tb .= '<td>' . $r[$i] . '</td>';
            }
            $tb .= '</tr>';
        }

        $tb .= '<tr> 
    <td colspan="2"></td>
    <td>' . $tlec . '</td>
    <td>' . $tlab . '</td>
    <td>' . $tunit . '</td>
    <td colspan="2"></td>
    <td>' . $th . '</td>
    <td>' . $thw . '</td>
    <td colspan="2"></td>
    </tr>
    </tbody>
  </table>';
    } else {
        $tb = 'nosched';
    }

    echo $tb;
}
