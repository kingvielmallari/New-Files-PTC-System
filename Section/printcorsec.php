<?php
require_once '../dbcon.php';
require_once 'functionsec.php';


?>
<link rel="icon" type="image/x-icon" href="../assets/img/ptclogo.png">
<title>COR</title>
<style>
    body {
        width: 240mm;
        height: 100%;
        margin: 0 auto;
        padding: 0;
        font-size: 12pt;
    }

    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .main-page {
        width: 210mm;
        min-height: 297mm;
        margin: 10mm auto;
        background: white;
        box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
        padding: 20px;
        position: relative;
    }

    .sub-page {
        padding: 1cm;
        height: 297mm;
    }

    .btn {
        display: inline-block;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        text-align: center;
        text-decoration: none;
        vertical-align: middle;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        background-color: transparent;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .btn-primary {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-primary:hover {
        color: #fff;
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .position-absolute {
        position: absolute !important;
    }

    .top-100 {
        margin-top: -25px;
    }

    .start-50 {
        left: 50% !important;
    }

    .translate-middle {
        transform: translate(-50%, -50%) !important;
    }

    .relat {
        position: relative;
    }

    .esize {
        width: 100px;
        height: 100px;
    }

    @page {
        size: LETTER;
        margin: 0;
    }

    @media print {


        body {
            width: 268mm;

        }

        .main-page {
            margin: 0;
            margin-right: 5mm;
            margin-left: 5mm;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
        }

        .section-break {
            page-break-after: always;
        }

        .imglugu {
            margin-left: 200px;
        }

        .btn,
        .pgs {
            display: none;
        }

        .onprintdiv {
            margin-left: 40px;
        }

        .position-absolute {
            position: absolute !important;
        }

        .top-100 {
            margin-top: -25px;
        }

        .start-50 {
            right: 50% !important;
        }

        .translate-middle {
            transform: translate(-50%, -50%) !important;
        }

        .relat {
            position: relative;
        }

        .taytel {
            margin-left: -20px;
        }

       
    }

    .border {
        border: solid black 1px;
        width: 46%;
        margin-top: 10px;

    }

    .text-center {
        text-align: center;
    }

    .widths {
        width: 46%;
        margin-top: 10px;
    }

    .exam {
        border: solid black 1px;
        margin-bottom: 10px;
    }

    .watermark {
        width: 500px;
        height: 500px;
        display: block;
        position: absolute;
        margin-top: 50px;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 95px;
        color: rgba(0, 0, 0, 0.3);
        text-transform: uppercase;
        pointer-events: none;
        white-space: nowrap;
    }

    .watermark::after {
        content: "";
        background-image: url('../assets/img/ptclogo.png');
        background-repeat: no-repeat;
        background-size: 100%;
        opacity: 0.25;
        top: -10px;
        left: -10px;
        bottom: -10px;
        right: -10px;
        position: absolute;
        z-index: -1;
        filter: grayscale(100%);
    }

    .watermarkdata {
        position: absolute;
        top: 30px;
        right: 20px;
        opacity: 1;
        pointer-events: none;
        font-size: 10px;
        white-space: nowrap;
        text-align: center;
    }

    .watermarkdata p {
        margin: 0;
        padding: 0;
        font-size: 13px;

    }
</style>

<button style="float:right;margin-right: -100px;" class="btn btn-primary" id="print" onclick="window.print();addhistory(this);">Print</button>
<a style="float:right;margin-right: -250px;" class="btn btn-primary" id="switch_cor" href="<?php echo "printcorsec.php?secId=" . $_GET['secId'] .  (isset($_GET['stdid']) ? '&stdid=' .$_GET['stdid'] : "")  . (isset($_GET['new_format']) ? "" : "&new_format=true") ?>"><?php echo (isset($_GET['new_format']) ? "Old" : "New") ?> Rate</a>

<?php
$secId = $_GET['secId'];
$stdid = (isset($_GET['stdid']) ? " and enstd.Student_id = '" . $_GET['stdid'] . "'" : "");

$viewsql = "SELECT es.Student_id, concat(pl.Program_code, ' - ', yl.year_level_id, sec.letter) as section, es.School_year, es.Semester
FROM `enrolledstudents` es
JOIN `students` st ON st.Student_idno = es.Student_id
JOIN `program_year_sections` pys ON pys.Prog_year_section_id = es.Program_section
JOIN `program_year` py ON py.Program_year_id = es.Program_year
JOIN `program_list` pl ON pl.Program_list_id = py.Program_list_id
JOIN `year_level` yl ON yl.year_level_id = py.Year_level_id
JOIN `sections` sec ON sec.Section_id = pys.Section_id
JOIN `studtype` ty ON ty.studType_id = es.student_type
LEFT JOIN `studstatus` sts ON sts.Studstatus_id = es.student_status
LEFT JOIN `student_pinfo` spinfo ON spinfo.Student_id = st.Student_idno
where es.Program_section = '$secId'" . ($stdid !== "" ? " and es.Student_id = '" . $_GET['stdid'] . "'"  : '');
$viewres = mysqli_fetch_array(execquery($viewsql));
$newids =  $_GET['stdid'] ?? '';

addlogs("View COR: " . ($newids === "" ? "" : "STD ID:" . $_GET['stdid']) . ", Section: " . $viewres[1] . ", A.Y: " . $viewres[2] . ", Semester: " . $viewres[3]);

$pagecnt = 1;

if (!isset($_GET['secId'])) {
    echo '<script> window.close(); </script>';
}

$semsy = semSy($secId);
$sql = "SELECT DISTINCT
CONCAT(
    stds.Student_lastname,
    ', ',
    stds.Student_firstname,
    ' ',
    IF(stds.Student_middlename = 'N/A' or stds.Student_middlename = null or stds.Student_middlename = '', '', concat(LEFT(stds.Student_middlename,1),'.'))
) AS `Student_Name`,
stds.Student_idno,
pl.Program_desc,
stdp.Student_street as st,  
stdp.Student_brgy, stdp.Student_brgy as bg, 
stdp.Student_city as ct,
CONCAT(yl.Year_level, ' YEAR') AS `yrlevel`,
enstd.Semester,
stdtype.type,
CONCAT(
    pl.Program_code,
    '-',
    yl.year_level_id,
    sec.letter
) AS `Sectionss`,
enstd.Program_section as ps
FROM
`enrolledstudents` enstd
JOIN `students` stds ON
enstd.Student_id = stds.Student_idno
LEFT JOIN `student_pinfo` stdp ON
stdp.Student_id = enstd.Student_id
JOIN `program_year` py ON
py.Program_year_id = enstd.Program_year
JOIN `program_list` pl ON
py.Program_list_id = pl.Program_list_id
JOIN `year_level` yl ON
py.Year_level_id = yl.year_level_id
JOIN `studtype` stdtype ON
stdtype.studType_id = enstd.student_type
JOIN `program_year_sections` pys ON
enstd.Program_section = pys.Prog_year_section_id
JOIN `sections` sec ON
sec.Section_id = pys.Section_id
WHERE
`Program_section` = '$secId' and enstd.`student_status` = '3' $stdid ORDER BY Student_Name desc";

$res = mysqli_query($conn, $sql);
$nr = mysqli_num_rows($res);
if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_array($res)) {
/*  echo date('Y-m-d')  */
?>

        <span style="float:left;margin-right: 100px;padding-right: 100px;" class="pgs">Page <?php echo $pagecnt; ?></span>
        <div class="main-page <?php echo($pagecnt < $nr ? 'section-break' : ''); ?>">
            <input type="hidden" name="datas" id="datas" value="<?php echo ($newids === "" ? '' : "STD ID:" . $newids . ", ") . "Section: " . $viewres[1] . ", A.Y: " . $viewres[2] . ", Semester: " . $viewres[3] ?>">
            <input type="hidden" name="stdids" id="secids" value="<?php echo $_GET['secId'] ?>">
            <input type="hidden" name="stdids" id="stdids" value="<?php echo $newids ?>">
            <div class="watermarkdata">
                <p>PATEROS TECHNOLOGICAL COLLEGE</p>
                <p>ENROLLED</p>
                <p><u>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo strtoupper($semsy[1]); ?> &nbsp;&nbsp;&nbsp;</u>SEM: S.Y: <u><?php echo $semsy[0]; ?></u></p>
                <p>SIGNATURE:____________</p>
                <p contenteditable="true">DATE: <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="date" contenteditable="true" style="cursor: pointer;"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></p>
        
            </div>

            <!-- OPENING DIV PRINT -->
            <div class="onprintdiv" style="margin-bottom:-50px;">
                <img src="../assets/img/ptclogo.png" alt="" height="65px;" class="imglugu" style="float:left; margin-left: 200px;">
                <span style="margin-left: 18px;font-size: 20px;">Pateros Technological College</span><br>
                <span style="margin-left: 15px;font-size: 11px;">College st. Sto. Rosario-Kanluran Pateros, Metro Manila</span><br>
                <span style="margin-left: 80px;font-size: 11px;">Tel no.: 8424-8370 Loc. 306</span><br>
                <h4 style="text-align: center;margin-bottom:-50px;" class="taytel">CERTIFICATE OF REGISTRATION</h4>
            </div>
            <br><br><br>
            <!-- HEADER OF COR -->
            <table style="width: 100%;font-size:14px;">
                <tbody>
                    <tr>
                        <td colspan="2">
                            NAME:<u><?php echo strtoupper($row['Student_Name']); ?></u>
                        </td>
                        <td>ID NO:<u><?php echo trim($row['Student_idno']) ?></u></td>
                        <td colspan="5">PROGRAM:<u><?php echo $row['Program_desc']; ?></u></td>
                    </tr>
                </tbody>
            </table>
            <!-- NAME AND STUDENT DETAILS -->
            <table style="width: 100%;font-size:14px;">
                <tbody>
                    <tr>
                        <td colspan="2" contenteditable="true">ADDRESS:<u><?php echo strtoupper($row['st'] . " " . $row['bg']) ?></u></td>
                        <td>YEAR LEVEL:<u><?php echo strtoupper($row['yrlevel']) ?></u></td>
                        <td>SEM:<u><?php echo strtoupper($row['Semester']) ?> SEM</u></td>
                        <td>STATUS:<u><?php echo strtoupper($row['type']) ?></u></td>
                    </tr>
                    <tr>
                        <td contenteditable="true"><u><?php echo strtoupper($row['ct']) ?></u></td>
                    </tr>
                </tbody>
            </table>
            <!-- STUDENT SCHEDULE -->
            <?php
            $where = "WHERE cs.Prog_year_section_id='" . $row['ps'] . "'";
            echo viewclasssched($where, $row['Sectionss'], $row['Student_Name'], $row['ps'], $row['Student_idno'],$new);
            ?>
            <!-- CLOSING DIV PRINT -->
        </div>
<?php


        $pagecnt++;
    }
}
?>
<br><br>
<script src="../jq.js"></script>
<script src="../funcs.js"></script>
<script src="cor.js"></script>