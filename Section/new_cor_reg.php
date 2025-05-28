<?php
require_once '../dbcon.php';
?>
<title>Certificate of Registration(COR)</title>
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
        size: A4;
        margin: 0;
    }

    @media print {
        <?php if ($_SESSION['usertype'] === "assessment" || $_SESSION['usertype'] === "viewer" || $_SESSION['usertype'] === "adviser" || $_SESSION['usertype'] === "dean") { ?>body {
            display: none;
        }

        <?php } ?>
        
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
<?php
if ($_SESSION['usertype'] !== "assessment" && $_SESSION['usertype'] !== "viewer" && $_SESSION['usertype'] !== "adviser" && $_SESSION['usertype'] !== "dean") {
?>
    <button style="float:right;margin-right: -100px;" class="btn btn-primary" id="print" onclick="window.print();addhistory(this);">Print</button>
<?php
}
?>


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
if(isset($_GET['stdid'])){
    addlogs("View COR: STD ID:" . $_GET['stdid'] . ", Section: " . $viewres[1] . ", A.Y: " . $viewres[2] . ", Semester: " . $viewres[3]);
}


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
`Program_section` = '$secId' $stdid ORDER BY Student_Name desc";

$res = mysqli_query($conn, $sql);
$nr = mysqli_num_rows($res);
if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_array($res)) {
?>
        <span style="float:left;margin-right: 100px;padding-right: 100px;" class="pgs">Page <?php echo $pagecnt; ?></span>

        <div class="main-page  <?php echo($pagecnt < $nr ? 'section-break' : ''); ?>">
            <input type="hidden" name="datas" id="datas" value="<?php echo "STD ID:" . ($_GET['stdid'] ?? '') . ", Section: " . $viewres[1] . ", A.Y: " . $viewres[2] . ", Semester: " . $viewres[3] ?>">
            <input type="hidden" name="stdids" id="secids" value="<?php echo $_GET['secId'] ?>">
            <input type="hidden" name="stdids" id="stdids" value="<?php echo $_GET['stdid'] ?? '' ?>">
            <!-- OPENING DIV PRINT -->
            <div class="watermarkdata">
                <p>PATEROS TECHNOLOGICAL COLLEGE</p>
                <p>ENROLLED</p>
                <p><u>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo strtoupper($semsy[1]); ?> &nbsp;&nbsp;&nbsp;</u>SEM: S.Y: <u><?php echo $semsy[0]; ?></u></p>
                <p>SIGNATURE:____________</p>
                <!-- <p>DATE:__________________</p> -->
                <p contenteditable="true">DATE: <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="date" contenteditable="true" style="cursor: pointer;"><?php echo date('Y-m-d') ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></p>
                <script>
                    function editDate() {
                        var dateSpan = document.getElementById('date');
                        dateSpan.setAttribute('contenteditable', 'true');
                        dateSpan.focus();
                    }

                    document.getElementById('date').addEventListener('blur', function() {
                        var dateSpan = document.getElementById('date');
                        dateSpan.removeAttribute('contenteditable');

                        // Update the PHP date variable with the edited date
                        var editedDate = dateSpan.innerText;
                        // Assuming you want to update the date on the server, you can use AJAX to send the updated date to a PHP script that updates it in the database
                        // For simplicity, this example just updates the date displayed in the span without saving it to the server
                        dateSpan.innerText = editedDate;
                    });
                </script>
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
            </table>
            <!-- STUDENT SCHEDULE -->
            <?php
            $lec = 0;
            $lab = 0;
            $units = 0;
            $complab = 0;
            $nstpunits = 0;
            $totallab = 0;
            $totalcompfee = 0;
            $totalunits = 0;
            $totalnstpunits = 0;
            $sql2 = "SELECT
            sub.Subject_code,
            sub.Subject_title,
            if(sub.Subject_units = '0' OR sub.Subject_units is null, '' , sub.Subject_units) as Subject_units,
            if(sub.Subject_lec = '0' OR sub.Subject_lec is null, '' , sub.Subject_lec) as Subject_lec,
            IF(sub.Subject_lab = '0' OR sub.Subject_lab is null, '', sub.Subject_lab) as Subject_lab,
            sub.Subject_lab AS `Comp`,
            CONCAT(
                pl.Program_code,
                ' - ',
                py.Year_level_id,
                sec.letter
            ) AS `Section`,
            cs.Day,
            CONCAT(
                cs.Starting_time,
                ' - ',
                cs.Ending_time
            ) AS `Time`,
            CONCAT(
                '',
                ins.Instructor_lastname,
                ', ',
                ins.Instructor_firstname
            ) AS `Prof.Name`,
            IF(ro.Room_no = 'Online', '', '') AS `room`,
            IF(sub.complab is NULL OR sub.complab = 0, '', sub.complab) as complabs,
            if(sub.Subject_code like '%nstp%', '3', '') as nstp_units
            FROM `class_schedules` cs 
            JOIN program_year_sections pys
            ON cs.Prog_year_section_id=pys.Prog_year_section_id
            JOIN program_year py
            ON pys.Program_year_id=py.Program_year_id
            JOIN program_list pl
            ON py.Program_list_id=pl.Program_list_id
            JOIN year_level yl
            ON py.Year_level_id=yl.year_level_id
            JOIN sections sec
            ON pys.Section_id=sec.Section_id
            JOIN curriculums cur
            ON cs.Curriculum_id=cur.Curriculum_id
            JOIN subjects sub
            ON cur.Subject_id=sub.Subject_id
            LEFT JOIN rooms ro
            ON cs.Room_id=ro.Room_id  
            LEFT JOIN instructors ins ON
            cs.Instructor_id = ins.Instructor_id
            WHERE cs.Prog_year_section_id = '$secId'";
            $res2 = mysqli_query($conn, $sql2);
            if (mysqli_num_rows($res2) > 0) { ?>
                <table border="1" style="border-collapse: collapse;width: 100%;font-size:13px;">
                    <thead>
                        <th style="width:3%">SUBJECT CODE</th>
                        <th style="width:20%">SUBJECT TITLE</th>
                        <th style="width:2%">UNIT</th>
                        <th style="width:2%">LEC</th>
                        <th style="width:2%">LAB</th>
                        <th style="width:2%">COMP</th>
                        <th style="width:2%">NSTP</th>
                        <th style="width:10%">SECTION</th>
                        <th style="width:2%">DAY</th>
                        <th style="width:17%">TIME</th>
                        <th>PROFESSOR</th>
                        <th>ROOM</th>
                    </thead>
                    <tbody>
                        <?php
                        $cnt = 0;
                        while ($r = mysqli_fetch_array($res2)) {
                            $cnt++;
                        ?> <tr>
                                <td style="height:25px;"><?php echo $r[0] ?></td>
                                <td><?php echo $r[1] ?></td>
                                <td style="text-align:center;"><?php echo $r[2] ?></tds>
                                <td style="text-align:center;"><?php echo $r[3] ?></tds>
                                <td style="text-align:center;"><?php echo $r[4] ?></tds>
                                <td style="text-align:center;"><?php echo $r['complabs'] ?></tds>
                                <td style="text-align:center;"><?php echo $r['nstp_units'] ?></tds>
                                <td><?php echo $r[6] ?></td>
                                <td style="text-align:center;height:25px;"><?php echo ($r[7] === null ? 'SPL.SUBJ.' : $r[7]) ?></td>
                                <td style="text-align:center;height:25px;"><?php echo ($r[7] === null ? 'NON-ACADEMIC' : $r[8]) ?></td>
                                <td><?php echo ucwords(strtolower($r[9])) ?></td>
                                <td><?php echo $r[10] ?></td>
                            </tr><?php
                                    $lec += (int)$r['Subject_lec'];
                                    $lab += (int)$r['Subject_lab'];
                                    $complab += (int)($r['complabs'] === "" ? 0 : $r['complabs']);
                                    $units += (int)$r['Subject_units'];
                                    $nstpunits += (int)$r['nstp_units'];
                                    $totallab += (int)$r['Subject_lab'] * 300;
                                    $totalcompfee += (int)$r['complabs'] * 300;
                                    $totalnstpunits += (int)$r['nstp_units'] * 150;
                                    /*     if ($r['complabs'] === "") {
                                        $totallab += (int)$r['Subject_lab'] * 300;
                                        $totalcompfee += (int)$r['complabs'] * 0;
                                    } else {
                                        $totallab += (int)$r['Subject_lab'] * 100;
                                        $totalcompfee += (int)$r['complabs'] * 300;
                                    } */
                                }
                                $totalunits = $units * 300;
                            }

                            $stdid = $_GET['stdid'] ?? '';

                            $sqlirregsub = "SELECT
                            sub.Subject_code,
                            sub.Subject_title,
                            IF(sub.Subject_units = '0' OR sub.Subject_units IS NULL, '', sub.Subject_units) AS Subject_units,
                            IF(sub.Subject_lec = '0' OR sub.Subject_lec IS NULL, '', sub.Subject_lec) AS Subject_lec,
                            IF(sub.Subject_lab = '0' OR sub.Subject_lab IS NULL, '', sub.Subject_lab) AS Subject_lab,
                            sub.Subject_lab AS `Comp`,
                            CONCAT(pl.Program_code, ' - ', py.Year_level_id, sec.letter) AS `Section`,
                            cs.Day,
                            CONCAT(cs.Starting_time, ' - ', cs.Ending_time) AS `Time`,
                            CONCAT('', ins.Instructor_lastname, ', ', ins.Instructor_firstname) AS `Prof.Name`,
                            IF(ro.Room_no = 'Online', '', '') AS `room`,
                            IF(sub.complab IS NULL OR sub.complab = 0, '', sub.complab) AS complabs,
                            IF(sub.Subject_code LIKE '%nstp%', '3', '') AS nstp_units
                        FROM
                            `enrolledstudents` es
                            LEFT JOIN `enrolled_subjects` esub ON es.Enrolled_student_id = esub.Enrolled_student_id
                            LEFT JOIN `curriculums` c ON c.Curriculum_id = esub.Curriculum_id
                            LEFT JOIN `subjects` sub ON c.Subject_id = sub.Subject_id
                            LEFT JOIN `class_schedules` cs ON esub.csid = cs.Class_scheduleid
                            LEFT JOIN `program_year_sections` pys ON cs.Prog_year_section_id = pys.Prog_year_section_id
                            LEFT JOIN `program_year` py ON py.Program_year_id = pys.Program_year_id
                            LEFT JOIN `program_list` pl ON py.Program_list_id = pl.Program_list_id
                            LEFT JOIN `sections` sec ON sec.Section_id = pys.Section_id
                            LEFT JOIN `instructors` ins ON cs.Instructor_id = ins.Instructor_id
                            LEFT JOIN `rooms` ro ON cs.Room_id = ro.Room_id WHERE es.`Student_id` = '$stdid' and es.`Program_section` = '$secId' and esub.csid IS NOT NULL 
                            ";

                            $resirregsub = execquery($sqlirregsub);

                            while ($r = mysqli_fetch_array($resirregsub)) {
                                $cnt++;
                                    ?>
                        <tr>
                            <td style="height:25px;"><?php echo $r[0] ?></td>
                            <td><?php echo $r[1] ?></td>
                            <td style="text-align:center;"><?php echo $r[2] ?></tds>
                            <td style="text-align:center;"><?php echo $r[3] ?></tds>
                            <td style="text-align:center;"><?php echo $r[4] ?></tds>
                            <td style="text-align:center;"><?php echo $r['complabs'] ?></tds>
                            <td style="text-align:center;"><?php echo $r['nstp_units'] ?></tds>
                            <td><?php echo $r[6] ?></td>
                            <td style="text-align:center;height:25px;"></td>
                            <td style="text-align:center;height:25px;"></td>
                            <td><?php echo ucwords(strtolower($r[9])) ?></td>
                            <td><?php echo $r[10] ?></td>
                        </tr>
                    <?php
                                $lec += (int)$r['Subject_lec'];
                                $lab += (int)$r['Subject_lab'];
                                $complab += (int)($r['complabs'] === "" ? 0 : $r['complabs']);
                                $units += (int)$r['Subject_units'];
                                $nstpunits += (int)$r['nstp_units'];
                                $totallab += (int)$r['Subject_lab'] * 300;
                                $totalcompfee += (int)$r['complabs'] * 300;
                                $totalnstpunits += (int)$r['nstp_units'] * 150;

                                /*   if ($r['complabs'] === "") {
                                $totallab += (int)$r['Subject_lab'] * 300;
                                $totalcompfee += (int)$r['complabs'] * 0;
                              } else {
                                $totallab += $r['Subject_lab'] * 100;
                                $totalcompfee += $r['complabs'] * 300;
                              } */
                            }

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
                            <td></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="2" style="text-align:center;">TOTAL:</td>
                        <td style="text-align:center;"><?php echo $units ?></td>
                        <td style="text-align:center;"><?php echo $lec ?></td>
                        <td style="text-align:center;"><?php echo $lab ?></td>
                        <td style="text-align:center;"><?php echo $complab ?></td>
                        <td style="text-align:center;"><?php echo $nstpunits ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </body>
                </table>
                <div class="watermark">
                </div>
                <!-- STUDENT FEE -->
                <div class="border" style="float:left;">
                    <table style="font-size:13px;">
                        <tbody>
                            <tr>
                                <td style="width:<?php echo ($totalunits > 10000 ? "83.5" : "85.4") ?>%;">Tuition Fee:</td>
                                <td>₱ <?php echo number_format($totalunits) ?></td>
                            </tr>
                            <tr>
                                <td>Laboratory Fee:</td>
                                <td>₱ <?php echo number_format($totallab) ?></td>
                            </tr>
                            <tr>
                                <td>Computer Fee:</td>
                                <td>₱ <?php echo number_format($totalcompfee) ?></td>
                            </tr>
                            <tr>
                                <td>NSTP Fee:</td>
                                <td>₱ <?php echo number_format($totalnstpunits) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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

                            $getmiscfee = "SELECT * FROM miscfee";
                            $getmiscres = mysqli_query($conn, $getmiscfee);
                            $miscdata = mysqli_fetch_array($getmiscres);
                            $query = mysqli_query($conn, "SHOW COLUMNS FROM `miscfee`");
                            $fl = mysqli_num_rows($query);

                            $totalfee = 0;
                            $totalfee = $totalfee + $totalunits + $totallab + $totalcompfee + $totalnstpunits;
                            for ($i = 1; $i < $fl; $i++) {
                                $totalfee = $totalfee + $miscdata[$i];
                            }
                            ?>

                            <tr>
                                <td style="width: 89.5%;">Athletic Fee:</td>
                                <td>₱<?php echo $miscdata[1] ?> </td>
                            </tr>
                            <tr>
                                <td>Cultural Fee:</td>
                                <td>₱<?php echo $miscdata[2] ?> </td>
                            </tr>
                            <tr>
                                <td>PTC Cup: </td>
                                <td>₱<?php echo $miscdata[3] ?> </td>
                            </tr>
                            <tr>
                                <td>Supreme Student Council(SSC):</td>
                                <td>₱<?php echo $miscdata[4] ?></td>
                            </tr>
                            <tr>
                                <td>Guidance Fee:</td>
                                <td>₱<?php echo $miscdata[5] ?></td>
                            </tr>
                            <tr>
                                <td>Career Development:</td>
                                <td>₱<?php echo $miscdata[6] ?></td>
                            </tr>
                            <tr>
                                <td>Student Handbook:</td>
                                <td>₱<?php echo $miscdata[7] ?></td>
                            </tr>
                            <tr>
                                <td>Library Fee:</td>
                                <td>₱<?php echo $miscdata[8] ?></td>
                            </tr>
                            <tr>
                                <td>Medical and Dental Fee:</td>
                                <td>₱<?php echo $miscdata[9] ?></td>
                            </tr>
                            <tr>
                                <td>Insurance Fee:</td>
                                <td>₱<?php echo $miscdata[10] ?></td>
                            </tr>
                            <tr>
                                <td>Registration Fee:</td>
                                <td>₱<?php echo $miscdata[11] ?></td>
                            </tr>
                            <tr>
                                <td>ID Validation Fee:</td>
                                <td>₱<?php echo $miscdata[12] ?></td>
                            </tr>
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


function getcc($secid)
{
    $sql = execquery("SELECT `class_code` FROM `program_year_sections` WHERE `Prog_year_section_id` = '$secid'");
    return mysqli_fetch_array($sql)[0];
}

function semSy($secid)
{
    //getsemsy from program year sections
    $sql = "SELECT pys.School_year, pys.Semester FROM `program_year_sections` pys WHERE pys.`Prog_year_section_id` = '$secid'";
    $r = execquery($sql);
    return mysqli_fetch_array($r);
}
?>
<br><br>
<script src="../jq.js"></script>
<script src="../funcs.js"></script>
<script src="cor.js"></script>