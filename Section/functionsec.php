<?php
require_once '../dbcon.php';

$stdids = $_POST['stdids'] ?? '';
$secids = $_POST['secids'] ?? '';
$datas = $_POST['datas'] ?? '';
$new = $_GET['new_format'] ?? 'false';

if (isset($_POST['printedcor'])) {
  addlogs("Printed an COR: " . $datas);
}

function getAllAcadYeartoOpt()
{
  $data = '';
  $c = new con();
  $con = $c->con();
  $sql = "SELECT DISTINCT school_year FROM `academic_status`";
  $res = mysqli_query($con, $sql);
  while ($row = mysqli_fetch_array($res)) {
    $data .= '<option value="' . $row[0] . '">' . $row[0] . '</option>';
  }
  return $data;
}

if (isset($_POST['getSecProg'])) {
  $program = $_POST['program'];
  $sem = $_POST['sem'];
  $sy = $_POST['sy'];
  echo getSecProg($sy, $sem, $program);
}
function getSecProg($sy, $sem, $program)
{
  $c = new con();
  $con = $c->con();
  $sql = "SELECT
    (
    SELECT
        CONCAT(
            pl.Program_code,
            '-',
            yl.year_level_id,
            sec.letter
        )
) AS `Section`,
pys.Semester,
pys.School_year,
pys.Prog_year_section_id
FROM
    `program_year_sections` pys
JOIN `sections` sec ON
    pys.Section_id = sec.Section_id
JOIN `program_year` py ON
    pys.Program_year_id = py.Program_year_id
JOIN `program_list` pl ON
    py.Program_list_id = pl.Program_list_id
JOIN `year_level` yl ON
    py.Year_level_id = yl.year_level_id
WHERE 
    pys.School_year = '$sy' AND pys.Semester = '$sem' AND pl.Program_desc = '$program'";
}



if (isset($_GET['getYearLevel'])) {
  $progID = $_GET['progID'];
  echo getYearLevel($progID);
}

function getYearLevel($progID)
{
  $c = new con();
  $con = $c->con();
  $data = [];
  $sql = "SELECT
    py.Program_year_id,
   	yl.Year_level
FROM
    `program_year` py
JOIN `year_level` yl ON
    py.Year_level_id = yl.year_level_id
WHERE
    `Program_list_id` = '$progID'";
  $res = mysqli_query($con, $sql);
  while ($row = mysqli_fetch_array($res)) {
    $data[] .= $row[0] . '|' . $row[1];
  }
  return json_encode($data);
}
if (isset($_POST['setTableSec'])) {
  $yearId = $_POST['yearId'];
  $sem = $_POST['sem'];
  $sy = $_POST['sy'];
  echo setTableSec($sem, $sy, $yearId);
}
function setTableSec($sem, $sy, $yearId)
{
  require_once '../mainfunc.php';
  $data = '';
  $textsem = $_POST['textsem'] ?? '';
  if ($sem != "" || $sy != "" || $yearId != "") {
    $where = "WHERE
    (pys.academic_id = '$sem' or pys.School_year = '$sy' and pys.Semester = '$textsem') AND py.Program_year_id = '$yearId'";
  } else {
    $where = "";
  }
  $cnt = 1;
  $sql = "SELECT
  (
  SELECT
      CONCAT(
          pl.Program_code,
          '-',
          yl.year_level_id,
          sec.letter
      )
) AS `Section`,
pys.Semester,
pys.School_year,
pys.Prog_year_section_id
FROM
  `program_year_sections` pys
JOIN `sections` sec ON
  pys.Section_id = sec.Section_id
JOIN `program_year` py ON
  pys.Program_year_id = py.Program_year_id
JOIN `program_list` pl ON
  py.Program_list_id = pl.Program_list_id
JOIN `year_level` yl ON
  py.Year_level_id = yl.year_level_id
" . $where . "
";
  echo $sql;
  $res = queryWOParam($sql);
  if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_array($res)) {
      if (countstd($row[3]) > 0) {
        $data .= '
        <tr>
            <td>' . $cnt . '</td>
            <td>' . $row['Section'] . '</td>
            <td>' . $row[1] . '</td>
            <td>' . $row[2] . '</td>
            <td>' . countstd($row[3]) . '</td>
            <td><!--<a href="exportstd.php?secid=' . $row[3] . '&sec=' . $row['Section'] . '" class="btn btn-primary">Export</a>-->
            <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#stdlist" onclick="getSec(\'' . $row[3] . '|' . $row['Section'] . '\');">View Students</a>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sectionSched" onclick="getSched(\'' . $row[3] . '|' . $row[2] . '|' . $row[1] . '|' . $row['Section'] . '\')">View Schedule</a>
            </td>
        </tr>
        ';
        $cnt++;
      }
    }
  }
  if ($cnt == 1) {
    $data = '
    <tr>
        <td colspan="6" class="text-center">No Section Found</td>
    </tr>
    ';
  }

  return $data;
}
function countstd($pysec)
{
  $c = new con();
  $con = $c->con();
  $countstd = "SELECT COUNT(`Program_section`) AS Sec FROM `enrolledstudents` WHERE `Program_section` = '" . $pysec . "'";
  $countstdres = mysqli_query($con, $countstd);
  $datsa = mysqli_fetch_assoc($countstdres);
  return $datsa['Sec'];
}

if (isset($_POST['getSec'])) {
  $secid = $_POST['secid'];
  $section = $_POST['section'];
  echo getSec($secid, $section);
}
function getSec($secid, $section)
{
  $data = '';
  $c = new con();
  $con = $c->con();
  $link = "";
  $chkschedsec = "SELECT
  *
FROM
  `class_schedules` cs
JOIN `program_year_sections` pys ON
  pys.Prog_year_section_id = cs.Prog_year_section_id
JOIN `sections` sec ON
  pys.Section_id = sec.Section_id
WHERE cs.`Prog_year_section_id` = '$secid'";
  $chkschedsecres = mysqli_query($con, $chkschedsec);
  if (mysqli_num_rows($chkschedsecres) > 0) {
    $link = "printcorsec";
    $data .= '<span style="float:left;">Section:</span> ' . $section . '<a href="printcorsec.php?secId=' . $secid . '" target="_blank" class="btn btn-primary" style="float:right;">Print COR of this Section</a>';
  } else {
    $sql = "SELECT * FROM `program_year_sections` p join `sections` s on p.Section_id = s.Section_id where s.letter = 'Irregular' and p.Prog_year_section_id = '$secid'";
    $res = mysqli_query($con, $sql);
    if (mysqli_num_rows($res) > 0) {
      $link = "printirreg";
      $data .= '<span style="float:left;">Section:</span> ' . $section . '<a href="printirreg.php?secId=' . $secid . '" target="_blank" class="btn btn-primary" style="float:right;">Print Irregular COR</a>';
    } else {
      $data .= '<strong style="float:right;">No Schedule Created Yet</strong>';
    }
  }
  $data .= '
    <table class="table table-sm text-center">
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Print COR</th>
            </tr>
        </thead>
        <tbody>
    ';
  $cntstd = 1;
  $getstd = 'SELECT DISTINCT
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
WHERE
    es.`Program_section` = "' . $secid . '"';
  $getstdres = mysqli_query($con, $getstd);
  if (mysqli_num_rows($getstdres) > 0) {
    while ($rowstd = mysqli_fetch_array($getstdres)) {
      $data .= '
            <tr>
                <td>' . $cntstd . '</td>
                <td>' . $rowstd['Student_idno'] . '</td>
                <td>' . ucwords($rowstd['Full Name']) . '</td>
                <td><a href="' . $link . '.php?secId=' . $secid . '&stdid=' . $rowstd['Student_idno'] . '" target="_blank" class="btn btn-primary">Print COR</a></td>
            </tr>
            ';
      $cntstd++;
    }
  } else {
    $data = '
        <tr>
            <td>No Student Added in this Section YEt</td>
        </tr>
        ';
  }
  $data .= '</tbody></table>';

  return $data;
}

function viewclasssched($where, $sec, $stdname, $secid, $stdid , $new = 'false')
{
  $lec = 0;
  $lab = 0;
  $complab = 0;
  $units = 0;
  $c = new con();
  $con = $c->con();
  $totallab = 0;
  $totalcompfee = 0;
  $totalnstpfee = 0;
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
                WHERE pys.Prog_year_section_id = '$secid' LIMIT 1";
  $progRes = mysqli_query($con, $progQuery);
  $progCode = '';
  if ($progRes && mysqli_num_rows($progRes) > 0) {
      $progRow = mysqli_fetch_assoc($progRes);
      $progCode = $progRow['Program_code'];
  }

  // Get multipliers for this program
  $feesQuery = "SELECT unit_new, lab_new, comp_new, nstp_new, unit_old, lab_old, comp_old, nstp_old 
                FROM computation_fees WHERE program = '$progCode' LIMIT 1";
  $feesRes = mysqli_query($con, $feesQuery);
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
  
  $semsy = semSy($secid);
  $sql = "SELECT
  cs.Class_Scheduleid AS `id`,
  sub.Subject_code AS `subcode`,
  sub.Subject_title AS `subtitle`,
  GROUP_CONCAT(DISTINCT cs.Day)  AS `day`,
  GROUP_CONCAT(DISTINCT
  CONCAT(
      cs.Starting_time,
      ' - ',
      cs.Ending_time
  )) AS `time`,
  pl.Program_code AS `course`,
  s.letter AS `section`,
  IF(
      ins.Instructor_lastname = '' or ins.Instructor_lastname is null,'',
      concat('Prof. ',ins.Instructor_lastname, ', ', SUBSTRING_INDEX(ins.Instructor_firstname, ' ',1))
  ) AS `prof`,
  IFNULL(ins.Instructor_id, '') AS `insid`,
  IF(ro.Room_no = 'Online', '', '') AS `room`,
  IFNULL(ro.Room_id, '') AS `roomid`,
  sub.Subject_lec,
  IF(sub.Subject_lab = '0' OR sub.Subject_lab is null, '', sub.Subject_lab) as Subject_lab,
  sub.Subject_units,
  pl.Program_code,
  IF(sub.complab is NULL OR sub.complab = 0, '', sub.complab) as complabs
  FROM `class_schedules` cs 
  JOIN program_year_sections pys
  ON cs.Prog_year_section_id=pys.Prog_year_section_id
  JOIN program_year py
  ON pys.Program_year_id=py.Program_year_id
  JOIN program_list pl
  ON py.Program_list_id=pl.Program_list_id
  JOIN year_level yl
  ON py.Year_level_id=yl.year_level_id
  JOIN sections s
  ON pys.Section_id=s.Section_id
  JOIN curriculums cur
  ON cs.Curriculum_id=cur.Curriculum_id
  JOIN subjects sub
  ON cur.Subject_id=sub.Subject_id
  LEFT JOIN rooms ro
  ON cs.Room_id=ro.Room_id  
  LEFT JOIN instructors ins ON
  cs.Instructor_id = ins.Instructor_id
  $where
 
 group by sub.Subject_id
 
 ";
  $tb = '';
  $res = mysqli_query($con, $sql);
  if (mysqli_num_rows($res) > 0) {
    $tb .= '
   
    <table border="1" style="border-collapse: collapse;width: 100%;font-size:13px;">
        <thead>
        <tr>
          <th style="width:5%">Subject code</th>
          <th style="width:25%">Subject Title</th>
          <th>Unit</th>
          <th>Lec</th>
          <th>Lab</th>
          <th>Comp</th>
          <th style="width:9%">Section</th>
          <th>Day</th>
          <th>Time</th>
          <th>Professor</th>
          <th>Room</th>
        </tr>
        </thead>
        <tbody>';
    $cnt = 0;
    while ($r = mysqli_fetch_array($res)) {
      $cnt++;
      $is_nstp = (stripos($r[1], 'NSTP') !== false);
      $unit_val = (int)$r['Subject_units'];

   
      $units += $unit_val;

    
      if ($is_nstp) {
        $nstp_units = isset($nstp_units) ? $nstp_units : 0;
        $nstp_units += $unit_val;
      }

      $tb .= '<tr oncontextmenu="removethis(this);">
          <td style="height:25px;">' . $r[1] . '</td>
          <td style="height:25px;">' . $r[2] . '</td>
          <td style="text-align:center;height:25px;">' . ($r[3] === null ? '0' : $r['Subject_units']) . '</td>
          <td style="text-align:center;height:25px;">' . $r['Subject_lec'] . '</td>
          <td style="text-align:center;height:25px;">' . ($r['Subject_lab'] == "0" ? '' : $r['Subject_lab']) . '</td>
          <td style="text-align:center;height:25px;">' .  $r['complabs'] . '</td>
          <td style="height:25px;">' . $sec . '</td>
          <td style="text-align:center;height:25px;">' . ($r[3] === null ? 'SPL.SUBJ.' : $r[3]) . '</td>
          <td style="text-align:center;height:25px;">' . ($r[3] === null ? 'NON-ACADEMIC' : $r[4]) . '</td>
          <td style="height:25px;">' . ucwords(strtolower($r[7])) . '</td>
          <td style="height:25px;">' . $r[9] . '</td>        
        </tr>';

      $lec += (int)$r['Subject_lec'];
      $lab += (int)$r['Subject_lab'];
      $complab += ((int)$r['complabs'] === "" ? 0 : (int)$r['complabs']);
      $totallab += (int)$r['Subject_lab'] * $multiplier_lab;
      $totalcompfee += (int)$r['complabs'] * $multiplier_comp;
    }

    // Calculate NSTP fee if any NSTP units found
    $nstp_units = isset($nstp_units) ? $nstp_units : 0;
    $totalnstpfee = $nstp_units * $multiplier_nstp;

    $sqlirregsub = "
    SELECT
            s.Subject_code,
            s.Subject_title,
            s.Subject_units,
            s.Subject_lec,
            s.Subject_lab,
            s.Subject_lab AS `Comp`,
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
            cs.Room_id = rr.Room_id WHERE es.`Student_id` = '$stdid' and es.`Program_section` = '$secid' and esub.csid IS NOT NULL
       
    ";

    $resirregsub = execquery($sqlirregsub);

    while ($r = mysqli_fetch_array($resirregsub)) {
      $cnt++;
      $tb .= '<tr oncontextmenu="removethis(this);">
                <td style="height:25px;">' . $r[0] . '</td>
                <td style="height:25px;">' . $r[1] . '</td>
                <td style="text-align:center;height:25px;">' . $r['Subject_units'] . '</td>
                <td style="text-align:center;height:25px;">' . $r['Subject_lec'] . '</td>
                <td style="text-align:center;height:25px;">' . ($r['Subject_lab'] == "0" ? '' : $r['Subject_lab']) . '</td>
                <td style="text-align:center;height:25px;">' . $r['complabs'] . '</td>
                <td style="height:25px;">' . $r[6] . '</td>
                <td style="height:25px;">' . $r[7] . '</td>
                <td style="height:25px;">' . $r[8] . '</td>
                <td style="height:25px;">' . ucwords(strtolower($r[9])) . '</td>
                <td style="height:25px;">' . $r[10] . '</td>        
            </tr>';

      $lec += (int)$r['Subject_lec'];
      $lab += (int)$r['Subject_lab'];
      $complab += ((int)$r['complabs'] === "" ? 0 : (int)$r['complabs']);
      $units += (int)$r['Subject_units'];

      $totallab += (int)$r['Subject_lab'] * $multiplier_lab;
      $totalcompfee += (int)$r['complabs'] * $multiplier_comp;
      // Calculate NSTP fee (only for NSTP subjects)
      if (stripos($r[1], 'NSTP') !== false) {
        $totalnstpfee += (int)$r['Subject_units'] * $multiplier_nstp;
      }
      

     
    }

    $totalunits = ($units - $nstp_units) * $multiplier_unit;

    $cnt = 10 - $cnt;
    for ($i = 0; $i < $cnt; $i++) {
      $tb .= '
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
      ';
    }
    $tb .= '
        <tr>
        <td colspan = "2" style="text-align:center;">TOTAL:</td>
        <td style="text-align:center;">' . $units . '</td>
        <td style="text-align:center;">' . $lec . '</td>
        <td style="text-align:center;">' . $lab . '</td>
        <td style="text-align:center;">' . $complab . '</td>
        <td colspan="5"></td>
        </tr>
        ';
    $tb .= "</body></table>";
    $tb .= '
    <div class="watermark">
    </div>
    
       
      <!-- STUDENT FEE -->
      <div class="border" style="float:left;">
        <table style="font-size:13px;">
        <tbody>';
        
        if ($totalunits > 0) {
          $tb .= '
          <tr>
          <td style="width: 244px;" >Tuition Fee:</td>
          <td>₱ ' . number_format($totalunits) . '</td>
          </tr>';
        }
        if ($totallab > 0) {
          $tb .= '
          <tr>
          <td style="width: 244px;" >Laboratory Fee:</td>
          <td>₱ ' . number_format($totallab) . '</td>
          </tr>';
        }
        if ($totalcompfee > 0) {
          $tb .= '
          <tr>
          <td style="width: 244px;" >Computer Fee:</td>
          <td>₱ ' . number_format($totalcompfee) . '</td>
          </tr>';
        }
        if ($totalnstpfee > 0) {
          $tb .= '
          <tr>
          <td style="width: 244px;" >NSTP Fee:</td>
          <td>₱ ' . number_format($totalnstpfee) . '</td>
          </tr>';
        }

        $tb .= '
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
          ';
        $totalfee = 0;
        $totalfee = $totalfee + $totalunits + $totallab + $totalcompfee + $totalnstpfee;
          

        $programStatus = '';
      $statusQuery = "SELECT pl.status 
               FROM `program_year_sections` pys
               JOIN `program_year` py ON pys.Program_year_id = py.Program_year_id
               JOIN `program_list` pl ON py.Program_list_id = pl.Program_list_id
               WHERE pys.Prog_year_section_id = '$secid'";
      $statusResult = mysqli_query($con, $statusQuery);
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
      $feeResult = mysqli_query($con, $feeQuery);
      if ($feeResult && mysqli_num_rows($feeResult) > 0) {
        while ($feeRow = mysqli_fetch_assoc($feeResult)) {
          $fees[$feeRow['payment_name']] = (float)$feeRow['amount'];
        }
      }

             
        foreach ($fees as $name => $amount) {
          if ($amount != 0) {
            $tb .= ' <tr>
              <td style="width: 244px;">' . $name . '</td>
              <td>₱' . number_format($amount) . '</td>
          </tr>';
            $totalfee = $totalfee +  $amount;
          }
        }
        
        $tb .= '  </tbody>
          </table>';


    $tb .= ' </div>
        <div class="border">
        <table style="font-size:12px;">
          <tbody>
            <tr>
                <td style="width: 244px;">Total Fee: </td>
                <td><strong>₱' . number_format($totalfee) . '</strong></td>
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
              <td><strong>_______________________</strong></td>
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
                        <td style="text-align: center;"><u>' . strtoupper($stdname) . '</u></td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">Student\'s Signature</td>
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

      <span style="font-size:16px;float:left;">
      Section Learning Hub: ' . getcc($secid) . '
      </span>
      <p style="font-size:14px;opacity:75%;">
      <br>
      Note: This document serves as proof of your enrollment for the ' . $semsy[1] . ' Semester, A.Y ' . $semsy[0] . '</p> </i>
      <br>
    </div>
        ';
  } else {
    $tb .= "No Schedule";
  }

  return $tb;
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
