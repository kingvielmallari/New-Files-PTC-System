<?php
require_once '../dbcon.php';
?>

<head>
    <link rel="icon" type="image/x-icon" href="../assets/img/ptclogo.png">
    <title>COR-IRREG</title>
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
            ;
        }
    </style>
</head>
<?php $new = $_GET['new_format'] ?? 'false'; ?>
<button style="float:right;margin-right: -100px;" class="btn btn-primary" id="print" onclick="window.print();addhistory(this);">Print</button>
<a style="float:right;margin-right: -250px;" class="btn btn-primary" id="switch_cor" href="<?php echo "printirreg.php?secId=" . $_GET['secId'] .  (isset($_GET['stdid']) ? '&stdid=' . $_GET['stdid'] : "")  . (isset($_GET['new_format']) ? "" : "&new_format=true") ?>"><?php echo (isset($_GET['new_format']) ? "Old" : "New") ?> Rate</a>
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
addlogs("View COR: STD ID:" .  ($newids === "" ? "" : "STD ID:" . $_GET['stdid'])  . ", Section: " . $viewres[1] . ", A.Y: " . $viewres[2] . ", Semester: " . $viewres[3]);

if (!isset($_GET['secId'])) {
    echo '<script> window.close(); </script>';
}
$semsy = semSy($secId);

$pagecnt = 1;

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
stdp.Student_brgy, concat('BRGY. ', stdp.Student_brgy) as bg, 
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
`Program_section` = '$secId' $stdid 
    


ORDER BY Student_Name desc";

/* echo $sql;
exit(); */

$res = mysqli_query($conn, $sql);
$nr = mysqli_num_rows($res);
if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_array($res)) {
?>
        <span style="float:left;margin-right: 100px;padding-right: 100px;" class="pgs">Page <?php echo $pagecnt; ?></span>

        <div class="main-page <?php echo ($pagecnt < $nr ? 'section-break' : ''); ?>">
            <input type="hidden" name="datas" id="datas" value="<?php echo "STD ID:" . $newids . ", Section: " . $viewres[1] . ", A.Y: " . $viewres[2] . ", Semester: " . $viewres[3] ?>">
            <input type="hidden" name="stdids" id="secids" value="<?php echo $_GET['secId'] ?>">
            <input type="hidden" name="stdids" id="stdids" value="<?php echo $newids ?>">
            <!-- OPENING DIV PRINT -->
            <!--    <div class="watermarkdata">
                <p>PATEROS TECHNOLOGICAL COLLEGE</p>
                <p>ENROLLED</p>
                <p>_______SEM: S.Y:________</p>
                <p>SIGNATURE:____________</p>
                <p>DATE:__________________</p>
            </div> -->
            <div class="watermarkdata">
                <p>PATEROS TECHNOLOGICAL COLLEGE</p>
                <p>ENROLLED</p>
                <p><u>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo strtoupper($semsy[1]); ?> &nbsp;&nbsp;&nbsp;</u>SEM: S.Y: <u><?php echo $semsy[0]; ?></u></p>
                <p>SIGNATURE:____________</p>
                <p contenteditable="true">DATE: <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="date" contenteditable="true" style="cursor: pointer;"><?php echo date('Y-m-d') ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></p>

            </div>

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
                        <td><u><?php echo strtoupper($row['ct']) ?></u></td>
                    </tr>
                </tbody>
            <!-- STUDENT SCHEDULE -->
<?php

$lec = 0;
$lab = 0;
$units = 0;
$complab = 0;
$totallab = 0;
$totalcompfee = 0;
$totalunits = 0;
$totalnstpfee = 0;
$nstp_units = 0; // Initialize the variable to avoid undefined variable warnings

// Fetch multipliers from computation_fees table based on section's program
$multiplier_unit = 0;
$multiplier_lab = 0;
$multiplier_comp = 0;
$multiplier_nstp = 0;

// Get program code for this section
$progQuery = "SELECT pl.Program_code 
              FROM program_year_sections pys
              JOIN program_year py ON pys.Program_year_id = py.Program_year_id
              JOIN program_list pl ON py.Program_list_id = pl.Program_list_id
              WHERE pys.Prog_year_section_id = '$secId' LIMIT 1";
$progRes = mysqli_query($conn, $progQuery);
$progCode = '';
if ($progRes && mysqli_num_rows($progRes) > 0) {
    $progRow = mysqli_fetch_assoc($progRes);
    $progCode = $progRow['Program_code'];
}

// Get multipliers for this program
$feesQuery = "SELECT unit_new, lab_new, comp_new, nstp_new, unit_old, lab_old, comp_old, nstp_old 
              FROM computation_fees WHERE program = '$progCode' LIMIT 1";
$feesRes = mysqli_query($conn, $feesQuery);
if ($feesRes && mysqli_num_rows($feesRes) > 0) {
    $feesRow = mysqli_fetch_assoc($feesRes);
    if ($new == 'true') {
        $multiplier_unit = (float)$feesRow['unit_new'];
        $multiplier_lab = (float)$feesRow['lab_new'];
        $multiplier_comp = (float)$feesRow['comp_new'];
        $multiplier_nstp = (float)$feesRow['nstp_new'];
    } else {
        $multiplier_unit = (float)$feesRow['unit_old'];
        $multiplier_lab = (float)$feesRow['lab_old'];
        $multiplier_comp = (float)$feesRow['comp_old'];
        $multiplier_nstp = (float)$feesRow['nstp_old'];
    }
} else {
    // fallback default values if not found
    $multiplier_unit = ($new == 'true' ? 400 : 300);
    $multiplier_lab = ($new == 'true' ? 400 : 300);
    $multiplier_comp = ($new == 'true' ? 400 : 300);
    $multiplier_nstp = 0;
}

$semsy = semSy($secId);

$sql2 = "SELECT
            s.Subject_code,
            s.Subject_title,
            s.Subject_units,
            s.Subject_lec,
            IF(s.Subject_lab = '0' OR s.Subject_lab is null, '', s.Subject_lab) as Subject_lab,
            s.Subject_lab AS `Comp`,
            CONCAT(
                pl.Program_code,
                ' - ',
                py.Year_level_id,
                sec.letter
            ) AS `Section`,
            GROUP_CONCAT(DISTINCT cs.Day)  AS `day`,
            GROUP_CONCAT(DISTINCT
                CONCAT(
                    cs.Starting_time,
                    ' - ',
                    cs.Ending_time
                )) AS `Time`,
            CONCAT(
                'Prof. ',
                prof.Instructor_lastname,
                ', ',
                prof.Instructor_firstname
            ) AS `Prof.Name`,
            IF(rr.Room_no = 'Online', '', '') AS `room`,
            IF(s.complab is NULL OR s.complab = 0, '', s.complab) as complabs
        FROM
            `enrolledstudents` es
        JOIN `enrolled_subjects` esub ON
            es.Enrolled_student_id = esub.Enrolled_student_id
        LEFT JOIN `curriculums` c ON
            c.Curriculum_id = esub.Curriculum_id
        LEFT JOIN `subjects` s ON
            c.Subject_id = s.Subject_id
        LEFT JOIN `class_schedules` cs ON
            esub.csid = cs.Class_scheduleid
        LEFT JOIN `program_year_sections` pys ON
            cs.Prog_year_section_id = pys.Prog_year_section_id
        LEFT JOIN `program_year` py ON
            py.Program_year_id = pys.Program_year_id
        LEFT JOIN `program_list` pl ON
            py.Program_list_id = pl.Program_list_id
        LEFT JOIN `sections` sec ON
            sec.Section_id = pys.Section_id
        LEFT JOIN `instructors` prof ON
            cs.Instructor_id = prof.Instructor_id
        LEFT JOIN `rooms` rr ON
            cs.Room_id = rr.Room_id
        WHERE es.`School_year` = '" . $semsy[0] . "' and es.Semester = '" . $semsy[1] . "' and es.Program_section = '$secId' and es.Student_id = '" . $row['Student_idno'] . "'
        GROUP BY s.Subject_id";

$res2 = execquery($sql2);
$nr = mysqli_num_rows($res2);
if ($nr > 0) { ?>
    <table border="1" style="border-collapse: collapse;width: 100%;font-size:13px;">
        <thead>
            <th style="width:5%">Subject code</th>
            <th style="width:25%">Subject Title</th>
            <th>Unit</th>
            <th>Lec</th>
            <th>Lab</th>
            <th>Comp</th>
            <th style="width:9%">Section</th>
            <th>Day</th>
            <th style="width:18%">Time</th>
            <th>Professor</th>
            <th>Room</th>
        </thead>
        <tbody>
            <?php
            $cnt = 0;
            while ($r = mysqli_fetch_array($res2)) {
                $cnt++;
                echo "<script>console.log('Subject Title: " . addslashes($r[1]) . "');</script>";
                $is_nstp = (stripos($r[0], 'NSTP') !== false);

              
                
                $unit_val = (int)$r['Subject_units'];

                $units += $unit_val;

                if ($is_nstp) {
                    $nstp_units += $unit_val;
                }
            ?>
                <tr>
                    <td style="height:25px;"><?php echo $r[0] ?></td>
                    <td><?php echo $r[1] ?></td>
                    <td style="text-align:center;"><?php echo $r[2] ?></td>
                    <td style="text-align:center;"><?php echo $r[3] ?></td>
                    <td style="text-align:center;"><?php echo $r[4] ?></td>
                    <td style="text-align:center;"><?php echo $r['complabs'] ?></td>
                    <td><?php echo $r[6] ?></td>
                    <td><?php echo $r[7] ?></td>
                    <td><?php echo $r[8] ?></td>
                    <td><?php echo ucwords(strtolower($r[9])) ?></td>
                    <td><?php echo $r[10] ?></td>
                </tr>
            <?php
                $lec += (int)$r['Subject_lec'];
                $lab += (int)$r['Subject_lab'];
                $complab += ((int)$r['complabs'] === "" ? 0 : (int)$r['complabs']);

                $totallab += (int)$r['Subject_lab'] * $multiplier_lab;
                $totalcompfee += (int)$r['complabs'] * $multiplier_comp;
                // Calculate NSTP fee (only for NSTP subjects)
                if ($is_nstp) {
                    $totalnstpfee += (int)$r['Subject_units'] * $multiplier_nstp;
                }
            }

            $totalunits = ($units - $nstp_units) * $multiplier_unit;
            $cnt = 10 - $cnt;
            for ($i = 0; $i < $cnt; $i++) {
            ?>
                <tr>
                    <td style="height:25px;"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="2" style="text-align:center;">TOTAL:</td>
                <td style="text-align:center;"><?php echo $units ?></td>
                <td style="text-align:center;"><?php echo $lec ?></td>
                <td style="text-align:center;"><?php echo $lab ?></td>
                <td style="text-align:center;"><?php echo $complab ?></td>
                <td colspan="5"></td>
            </tr>
        </tbody>
    </table>
    <div class="watermark">
    </div>
    <!-- STUDENT FEE -->
    <div class="border" style="float:left;">
        <table style="font-size:13px;">
            <tbody>
                <?php if ($totalunits > 0) { ?>
                    <tr>
                        <td style="width:<?php echo ($totalunits > 10000 ? "83.5" : "85.4") ?>%;">Tuition Fee:</td>
                        <td>₱ <?php echo number_format($totalunits) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($totallab > 0) { ?>
                    <tr>
                        <td>Laboratory Fee:</td>
                        <td>₱<?php echo number_format($totallab) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($totalcompfee > 0) { ?>
                    <tr>
                        <td>Computer Fee:</td>
                        <td>₱<?php echo number_format($totalcompfee) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($totalnstpfee > 0) { ?>
                    <tr>
                        <td>NSTP Fee:</td>
                        <td>₱<?php echo number_format($totalnstpfee) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
                <!-- EXAM PAYMENTS -->
                <div class="widths" style="margin-left: 50px;float:right;">
                    <div class="exam">
                        <table style="font-size:11px;">
                            <tbody>
                                <tr>
                                    <td colspan="2">Due For Prelim</td>
                                </tr>
                                <tr>
                                    <td style="width: 44%;">OR#: </td>
                                    <td>_____________________________</td>
                                </tr>
                                <tr>
                                    <td>Amount Paid:</td>
                                    <td>_____________________________</td>

                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>_____________________________</td>

                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <span style="font-size:11px;">Collected by:</span>
                    </div>
                    <div class="exam">
                        <table style="font-size:11px;">
                            <tbody>
                                <tr>
                                    <td colspan="2">Due For Midterm</td>
                                </tr>
                                <tr>
                                    <td style="width: 44%;">OR#: </td>
                                    <td>_____________________________</td>

                                </tr>
                                <tr>
                                    <td>Amount Paid:</td>
                                    <td>_____________________________</td>

                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>_____________________________</td>

                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <span style="font-size:11px;">Collected by:</span>
                    </div>
                    <div class="exam">
                        <table style="font-size:11px;">
                            <tbody>
                                <tr>
                                    <td colspan="2">Due For Finals</td>
                                </tr>
                                <tr>
                                    <td style="width: 44%;">OR#: </td>
                                    <td>_____________________________</td>

                                </tr>
                                <tr>
                                    <td>Amount Paid:</td>
                                    <td>_____________________________</td>

                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>_____________________________</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <span style="font-size:11px;">Collected by:</span>
                    </div>
                    <div class="exam">
                        <table style="font-size:11px;">
                            <tbody>
                                <tr>
                                    <td style="width: 44%;">Total Balance:</td>
                                    <td>_____________________________</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="exam">
                        <table style="font-size:11px;">
                            <tbody>
                                <tr>
                                    <td colspan="2">Partial Payment:</td>
                                </tr>
                                <tr>
                                    <td style="width: 44%;">OR#: </td>
                                    <td>_____________________________</td>
                                </tr>
                                <tr>
                                    <td>Amount Paid:</td>
                                    <td>_____________________________</td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>_____________________________</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <span style="font-size:11px;">Collected by:</span>
                    </div>
                </div>
                <!-- MISC FEES -->
                <div class="border" style="float:left;">
                    <table style="font-size:13px;">
                        <tbody>
                            <?php
                            $totalfee = 0;
        $totalfee = $totalfee + $totalunits + $totallab + $totalcompfee;
    

        $programStatus = '';
$statusQuery = "SELECT pl.status 
               FROM `program_year_sections` pys
               JOIN `program_year` py ON pys.Program_year_id = py.Program_year_id
               JOIN `program_list` pl ON py.Program_list_id = pl.Program_list_id
               WHERE pys.Prog_year_section_id = '$secId'";
$statusResult = mysqli_query($conn, $statusQuery);
if ($statusResult && mysqli_num_rows($statusResult) > 0) {
    $statusRow = mysqli_fetch_assoc($statusResult);
    $programStatus = $statusRow['status'];
}

// Fetch fees from the database based on program status and format
$fees = [];
$feeType = '';
if ($programStatus == 'Paying') {
  $feeType = ($new == 'true') ? 'paying_new' : 'paying_old';
} else {
  $feeType = ($new == 'true') ? 'unifast_new' : 'unifast_old';
}

$feeQuery = "SELECT payment_name, `$feeType` as amount FROM assessment_fees";
$feeResult = mysqli_query($conn, $feeQuery);
if ($feeResult && mysqli_num_rows($feeResult) > 0) {
  while ($feeRow = mysqli_fetch_assoc($feeResult)) {
    $fees[$feeRow['payment_name']] = (float)$feeRow['amount'];
  }
}

  
        $tb = '';
        foreach ($fees as $name => $amount) {
          if ($amount != 0) {
            $tb .= ' <tr>
              <td style="width: 244px;">' . $name . '</td>
              <td>₱' . number_format($amount) . '</td>
          </tr>';
            $totalfee = $totalfee +  $amount;
          }
        }
        echo $tb;
        ?>
                        </tbody>
                    </table>

                </div>

                <div class="border">
                    <table style="font-size:12px;">
                        <tbody>
                            <tr>
                                <td style="width:<?php echo ($totalfee > 10000 ? "85.1" : "87.05") ?>%;">Total Fee: </td>
                                <td><strong>₱<?php echo number_format($totalfee) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="signature" style="margin-top:50px;">
                    <div id="assign" class="sign" style="margin-left: 60px;;">
                        <table style="font-size:13px;">
                            <tbody>
                                <tr>
                                    <td>Assessed by: </td>
                                    <td><strong>________________________</strong></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="relat">
                                        <img src="mamwengsign.png" alt="" class="position-absolute top-100 start-50 translate-middle esize">
                                        ROWENA B. DEL ROSARIO
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <br><br><br>

                    <table style="width:100%;margin-left:23px;">
                        <tbody>
                            <tr>
                                <td>
                                    <div id="stdassign">
                                        <center>
                                            <table style="font-size:13px;">
                                                <tbody>
                                                    <tr>
                                                        <td style="text-align: center;"><u><?php echo strtoupper($row['Student_Name']) ?></u></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: center;">Student's Signature</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </center>
                                    </div>
                                </td>
                                <td>
                                    <div id="OIC">
                                        <center>
                                            <table style="font-size:13px;">
                                                <tbody>
                                                    <tr>
                                                        <td class="relat">
                                                            <u style="margin-left:25px;">MELISSA L. PATCO, MBA</u>
                                                            <img src="esign.png" alt="" class="position-absolute start-50 translate-middle" style="width: 300px; height: 300px;  margin-top:-15px;">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: center;">COLLEGE REGISTRAR</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: center;">
                                                            <p style="font-size:10px;opacity:75%;">Not valid without seal and original signature ink</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table><br><br>
                    <i>
                        <p style="font-size:14px;opacity:75%;">
                            <!--   Section Learning Hub:<?php echo getcc($secId); ?>
                            <br> -->
                            Note: This document serves as proof of your enrollment for the <?php echo $semsy[1]; ?> Semester, A.Y <?php echo $semsy[0]; ?>
                        </p>
                    </i>
                </div>
        </div>
<?php
        $pagecnt++;
    }
}


function getcc($secId)
{
    $sql = execquery("SELECT `class_code` FROM `program_year_sections` WHERE `Prog_year_section_id` = '$secId'");
    return mysqli_fetch_array($sql)[0];
}

function semSy($secId)
{
    //getsemsy from program year sections
    $sql = "SELECT pys.School_year, pys.Semester FROM `program_year_sections` pys WHERE pys.`Prog_year_section_id` = '$secId'";
    $r = execquery($sql);
    return mysqli_fetch_array($r);
}
?>
<br><br>

<!-- <script src="../jq.js"></script>
<script src="../funcs.js"></script>
<script src="cor.js"></script> -->