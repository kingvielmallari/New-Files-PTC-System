<?php
require_once '../dbcon.php';

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
  if ($sem != "" || $sy != "" || $yearId != "") {
    $where = "WHERE
    pys.School_year = '$sy' AND pys.Semester = '$sem' AND py.Program_year_id = '$yearId'";
  } else {
    $where = "";
  }
  $data = '';
  $cnt = 1;
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
" . $where . "

";
  $res = mysqli_query($con, $sql);
  if (mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_array($res)) {
      $countstd = "SELECT
            COUNT(`Program_section`) AS Sec
        FROM
            `enrolledstudents`
        WHERE
            `Program_section` = '" . $row[3] . "'";
      $countstdres = mysqli_query($con, $countstd);
      $datsa = mysqli_fetch_assoc($countstdres);
      if ($datsa['Sec'] > 0) {
        $data .= '
            <tr>
                <td>' . $cnt . '</td>
                <td>' . $row['Section'] . '</td>
                <td>' . $row[1] . '</td>
                <td>' . $row[2] . '</td>
                <td>' . $datsa['Sec'] . '</td>
                <td><!--<a href="exportstd.php?secid=' . $row[3] . '&sec=' . $row['Section'] . '" class="btn btn-primary">Export</a>-->
                <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#stdlist" onclick="getSec(\'' . $row[3] . '|' . $row['Section'] . '\');">View Students</a>
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sectionSched" onclick="getSched(\'' . $row[3] . '|' . $row[2] . '|' . $row[1] . '|' . $row['Section'] . '\')">View Schedule</a>
                </td>
            </tr>
            ';
        $cnt++;
      }
    }
    if ($cnt == 1) {
      $data = '
            <tr>
                <td colspan="6" class="text-center">No Section Found</td>
            </tr>
            ';
    }
  } else {
    $data = '
        <tr>
            <td colspan="5" class="text-center">No Section Found</td>
        </tr>
        ';
  }
  return $data;
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
  $chkschedsec = "SELECT * FROM `class_schedules` WHERE `Prog_year_section_id` = '$secid'";
  $chkschedsecres = mysqli_query($con, $chkschedsec);
  if (mysqli_num_rows($chkschedsecres) > 0) {
    $data .= '<span style="float:left;">Section:</span> ' . $section . '<a href="printcorsec.php?secId=' . $secid . '" target="_blank" class="btn btn-primary" style="float:right;">Print COR of this Section</a>';
  } else {
    $data .= '<strong style="float:right;">No Schedule Created Yet</strong>';
  }
  $data .= '
    <table class="table table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Student Name</th>
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
    ) AS `Full Name`,
    esp.Student_sex
FROM
    `enrolledstudents` es
JOIN `students` s ON
    es.Student_id = s.Student_idno
JOIN `student_pinfo` esp ON
    es.Student_id = esp.Student_id
WHERE
    `Program_section` = "' . $secid . '"';
  $getstdres = mysqli_query($con, $getstd);
  if (mysqli_num_rows($getstdres) > 0) {
    while ($rowstd = mysqli_fetch_array($getstdres)) {
      $data .= '
            <tr>
                <td>' . $cntstd . '</td>
                <td>' . $rowstd['Student_idno'] . '</td>
                <td>' . ucwords($rowstd['Full Name']) . '</td>
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

  return $data;
}

/* SELECT
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
    orig query
    */


function viewclasssched($where, $sec, $stdname)
{
  $lec = 0;
  $lab = 0;
  $units = 0;
  $c = new con();
  $con = $c->con();
  $sql = "SELECT
    cs.Class_Scheduleid AS `id`,
    sub.Subject_code AS `subcode`,
    sub.Subject_title AS `subtitle`,
    cs.Day AS `day`,
    CONCAT(
        cs.Starting_time,
        ' - ',
        cs.Ending_time
    ) AS `time`,
    pl.Program_code AS `course`,
    s.letter AS `section`,
    IFNULL(
        ins.Instructor_lastname,
        ''
    ) AS `prof`,
    IFNULL(ins.Instructor_id, '') AS `insid`,
    IFNULL(ro.Room_no, '') AS `room`,
    IFNULL(ro.Room_id, '') AS `roomid`,
    sub.Subject_lec,
    sub.Subject_lab,
    sub.Subject_units
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
  cs.Instructor_id = ins.Instructor_id $where";
  $tb = '';
  $res = mysqli_query($con, $sql);
  if (mysqli_num_rows($res) > 0) {
    $tb .= '<table border="1" style="border-collapse: collapse;width: 100%;font-size:13px;">
        <thead>
        <th style="width:5%">Subject code</th>
        <th style="width:25%">Subject Title</th>
        <th>Unit</th>
        <th>Lec</th>
        <th>Lab</th>
        <th>Comp</th>
        <th style="width:7%">Section</th>
        <th>Day</th>
        <th>Time</th>
        <th>Professor</th>
        <th>Room</th>
        </thead>
        <tbody>';
    $cnt = 0;
    while ($r = mysqli_fetch_array($res)) {
      $cnt++;
      $tb .= '<tr>
                <td>' . $r[1] . '</td>
                <td>' . $r[2] . '</td>
                <td style="text-align:center;">' . $r['Subject_units'] . '</tds>
                <td style="text-align:center;">' . $r['Subject_lec'] . '</td>
                <td style="text-align:center;">' . $r['Subject_lab'] . '</td>
                <td style="text-align:center;">' . $r['Subject_lab'] . '</td>
                <td>' . $sec . '</td>
                <td>' . $r[3] . '</td>
                <td>' . $r[4] . '</td>
                <td>Prof. ' . ucwords($r[7]) . '</td>
                <td>' . $r[9] . '</td>        
            </tr>';
      $lec = $lec + $r['Subject_lec'];
      if ($r['Subject_lab'] != "") {
        $lab = $lab + $r['Subject_lab'];
      }
      $units = $units + $r['Subject_units'];
      $totallec = $lec * 300;
      $totallab = $lab * 300;
      $totalunits = $units * 300;
      /*     $_SESSION['totallec'] = $totallec;
            $_SESSION['totalunits'] = $totalunits;
            $_SESSION['totallab'] = $totallab; */
    }
    $cnt = 10 - $cnt;
    for ($i = 0; $i < $cnt; $i++) {
      $tb .= '
      <tr>
        <td style="height: 15px;"></td>
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
        <td style="text-align:center;">' . $lab . '</td>
        <td colspan="5"></td>
        </tr>
        ';
    $tb .= "</body></table>";
    $tb .= '
        <!-- STUDENT FEE -->
        <div class="border" style="float:left;">
          <table style="font-size:13px;">
            <tbody>
              <tr>
                <td style="width:85%;">Tuition Fee:</td>
                <td>₱ ' . number_format($totalunits) . '</td>
              </tr>
              <tr>
                <td>Laboratory Fee:</td>
                <td>₱' . number_format($totallab) . '</td>
              </tr>
              <tr>
                <td>Computer Fee:</td>
                <td>₱' . number_format($totallab) . '</td>
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
              ';

    $getmiscfee = "SELECT * FROM miscfee";
    $getmiscres = mysqli_query($con, $getmiscfee);
    $miscdata = mysqli_fetch_array($getmiscres);
    $query = mysqli_query($con, "SHOW COLUMNS FROM `miscfee`");
    $fl = mysqli_num_rows($query);

    $totalfee = 0;
    $totalfee = $totalfee + $totalunits + $totallab + $totallab;
    for ($i = 1; $i < $fl; $i++) {
      $totalfee = $totalfee + $miscdata[$i];
    }
    $tb .= '
              <tr>
                <td style="width: 88.4%;">Athletic Fee:</td>
                <td>₱' . $miscdata[1] . ' </td>
              </tr>
              <tr>
                <td>Cultural Fee:</td>
                <td>₱' . $miscdata[2] . ' </td>
              </tr>
              <tr>
                <td>PTC Cup: </td>
                <td>₱' . $miscdata[3] . ' </td>
              </tr>
              <tr>
                <td>Supreme Student Council(SSC):</td>
                <td>₱' . $miscdata[4] . '</td>
              </tr>
              <tr>
                <td>Guidance Fee:</td>
                <td>₱' . $miscdata[5] . '</td>
              </tr>
              <tr>
                <td>Career Development:</td>
                <td>₱' . $miscdata[6] . '</td>
              </tr>
              <tr>
                <td>Student Handbook:</td>
                <td>₱' . $miscdata[7] . '</td>
              </tr>
              <tr>
                <td>Library Fee:</td>
                <td>₱' . $miscdata[8] . '</td>
              </tr>
              <tr>
                <td>Medical and Dental Fee:</td>
                <td>₱' . $miscdata[9] . '</td>
              </tr>
              <tr>
                <td>Insurance Fee:</td>
                <td>₱' . $miscdata[10] . '</td>
              </tr>
              <tr>
                <td>Registration Fee:</td>
                <td>₱' . $miscdata[11] . '</td>
              </tr>
              <tr>
                <td>ID Validation Fee:</td>
                <td>₱' . $miscdata[12] . '</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="border">
        <table style="font-size:12px;">
          <tbody>
            <tr>
              <td style="width: 85.7%;">Total Fee: </td>
              <td><strong>₱' . number_format($totalfee) . '</strong></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div id="signature" style="margin-top:50px;">
      <div id="assign" class="sign" style="margin-left: 50px;;">
        <table style="font-size:13px;">
          <tbody>
            <tr>
              <td>Assessed by: </td>
              <td><strong>________________________</strong></td>
            </tr>
            <tr>
              <td></td>
              <td>ROWENA B. DEL ROSARIO</td>
            </tr>
          </tbody>
        </table>
      </div>
      <br>
      <br><br><br>
  
      <table style="width:100%;">
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
                        <td style="text-align: center;"><u>ERMIE I. LANCERO</u></td>
                      </tr>
                      <tr>
                        <td style="text-align: center;">OIC - REGISTRAR</td>
                      </tr>
                    </tbody>
                  </table>
                </center>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
        ';
  } else {
    $tb .= "No Schedule";
  }

  return $tb;
}
