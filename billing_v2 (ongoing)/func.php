<?php
require_once '../dbcon.php';

use PhpOffice\PhpSpreadsheet\Style\Border;

$Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

$pages = $_POST['pages'] ?? "";
$entries = $_POST['entries'] ?? "";
$search = $_POST['search'] ?? "";
$ay = ((isset($_POST['ay'])) ? $_POST['ay'] : "");
$ayid = ((isset($_POST['ayid'])) ? $_POST['ayid'] : "");
$aysem = getaysem($ayid);
$programid = ((isset($_POST['programid'])) ? $_POST['programid'] : "");
$progyearid = ((isset($_POST['progyearid'])) ? $_POST['progyearid'] : "");
$sectionid = ((isset($_POST['sectionid'])) ? $_POST['sectionid'] : ""); 
$status = ((isset($_POST['status'])) ? $_POST['status'] : "");
$sem = $_POST['sem'] ?? "";
$type = $_POST['type'] ?? "";
$csid = $_POST['csid'] ?? "";
$stdid = $_POST['stdid'] ?? "";
$subs = $_POST['subs'] ?? '';
$esid = $_POST['esid'] ?? '';
$subselected = $_POST['subselected'] ?? '';
$cids = $_POST['cids'] ?? '';
$stnum = $_POST['stnum'] ?? '';
$stdname = $_POST['stdname'] ?? '';
$currid = $_POST['currid'] ?? '';
$val = $_POST['val'] ?? '';
$id = $_POST['id'] ?? '';
$letter = $_POST['letter'] ?? '';
$arr = $_POST['arr'] ?? '';
$data = $_POST['data'] ?? '';
$stdid = $_POST['stdid'] ?? '';
$sel = $_POST['sel'] ?? '';
$new = $_GET['new_format'] ?? 'false';


if (isset($_POST['updatesex'])) {
    $sql = "INSERT INTO `student_pinfo` (`Student_id`, `Student_sex`) VALUES ('$stdid', '$sel') ON DUPLICATE KEY UPDATE `Student_sex` = VALUES(`Student_sex`);";
    echo affected($sql);
}



if (isset($_POST['settbfp'])) {
    $wheres = '';
    $tb = '';
    if ($sectionid !== "") {
        $wheres =  " and es.Program_section='$sectionid'";
    } else if ($progyearid !== "") {
        $wheres =  " and es.Program_year='$progyearid'";
    } else if ($programid !== "") {
        $wheres =  " and pl.Program_list_id = '$programid'";
    }

    // Get all fee types from assessment_fees table, sorted by highest value first (using *_old columns)
    $feeSql = "SELECT * FROM assessment_fees WHERE status = 'Enabled' 
               ORDER BY GREATEST(paying_old, unifast_old, executive_old) DESC";
    $feeRes = execquery($feeSql);
    $feeColumns = [];
    
    // Build the table header with fixed columns first, then dynamic fees
    $tb = '
    <thead>
        <tr>
            <th>Sequence</th>
            <th>Student Number</th>
            <th>Last name</th>
            <th>Given Name</th>
            <th>Middle Initial</th>
            <th>Degree Program</th>
            <th>Year Level</th>
            <th>Sex at Birth (M/F)</th>
            <th>Email Address</th>
            <th>Phone Number</th>
            <th title="Academic Units Enrolled (credit and non-credit courses)">Academic Units Enrolled <br>(credit and non-credit courses)</th>
            <th title="Laboratory Units/subject">Lab Units</th>
            <th title="Computer Lab Units/Subject">Comp Lab Units</th>
            <th title="NSTP Units Enrolled (credit and non-credit courses)">NSTP Units Enrolled <br>(credit and non-credit courses)</th>
            <th title="Tuition Fee based on enrolled academic units (credit and non-credit courses)">Tuition Fee</th>
            <th>Laboratory Fee</th>
            <th>Computer Fee</th>
            <th title="Academic Units of NSTP Enrolled  (credit and non-credit courses)">NSTP fee</th>';
    
    // Add dynamic fee columns to header (sorted by highest value)
    while ($feeRow = mysqli_fetch_assoc($feeRes)) {
        $feeColumns[] = $feeRow;
        $tb .= '<th>' . $feeRow['payment_name'] . '</th>';
    }
    
    $tb .= '
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
    ';

    $unitfee = 300;
    $labfee = 300;
    $compfee = 300;
    $nstpfee = 150;
    
    $sql = "SELECT 
    es.Enrolled_student_id, 
    s.Student_idno,
    s.Student_lastname, 
    s.Student_firstname, 
    if(s.Student_middlename = 'n/a' or s.Student_middlename = 'na', '' ,left(s.Student_middlename, 1)), 
    pl.Program_code, 
    concat(yl.Year_level, ' year'), 
    if(sp.Student_sex = 'M', 'Male', if(sp.Student_sex = 'F' , 'Female', Student_sex )), 
    s.personal_email,
    sp.Student_cellphone_no, 
    sum(case when sss.Subject_code not like '%nstp%' then sss.Subject_units else 0 end),
    sum(sss.Subject_lab), 
    sum(sss.complab), 
    SUM(CASE WHEN sss.Subject_code LIKE '%nstp%' THEN sss.Subject_units ELSE 0 END),
    (sum(case when sss.Subject_code not like '%nstp%' then sss.Subject_units else 0 end) * $unitfee),
    (sum(sss.Subject_lab)* $labfee),
    (sum(sss.complab) *$compfee),
    (SUM(CASE WHEN sss.Subject_code LIKE '%nstp%' THEN sss.Subject_units ELSE 0 END)* $nstpfee),
    typ.type as regtype,
    pl.status as program_status,
    es.Program_section
    FROM `enrolledstudents` es 
    LEFT JOIN `students` s ON s.Student_idno = es.Student_id 
    LEFT JOIN `student_pinfo` sp ON sp.Student_id = s.Student_idno
    LEFT JOIN `program_year` py ON py.Program_year_id = es.Program_year
    LEFT JOIN `program_list` pl ON pl.Program_list_id = py.Program_list_id
    LEFT JOIN `year_level` yl ON yl.year_level_id = py.Year_level_id
    LEFT JOIN `enrolled_subjects` esubs ON esubs.Enrolled_student_id = es.Enrolled_student_id
    LEFT JOIN `curriculums` c ON c.Curriculum_id = esubs.Curriculum_id
    LEFT JOIN `subjects` sss ON sss.Subject_id = c.Subject_id
    LEFT JOIN `program_year_sections` pys ON pys.Prog_year_section_id = es.Program_section
    LEFT JOIN `sections` sec ON sec.Section_id = pys.Section_id
    LEFT JOIN `studtype` typ on es.student_type = typ.studType_id
    where  es.School_year = '$ay' and es.Semester  = '$sem'" . $wheres . "  and py.ProgStatus_id = '2' and pl.status = 'Unifast' and concat(s.Student_firstname, s.Student_idno, s.Student_middlename, s.Student_lastname) like concat('%$search%') 
    group by es.Enrolled_student_id 
    order by concat(s.Student_lastname,s.Student_firstname) ASC
    limit $pages, $entries";
    $res = execquery($sql);

    if (mysqli_num_rows($res) > 0) {
        $cnt = 1;
        while ($r = mysqli_fetch_array($res)) {
            $total = 0;
            $tuitionFee = $r[14];
            $labFee = $r[15];
            $compFee = $r[16];
            $nstpFee = $r[17];
            $total += $tuitionFee + $labFee + $compFee + $nstpFee;

            $newlink = '';
            if ($r['regtype'] === "Regular") {
                $newlink = "../Section/new_cor_reg.php?secId=" . $r['Program_section'] . "&stdid=" . $r[1] . "";
            } else if ($r['regtype'] === "Irregular") {
                $newlink = "../Section/new_cor_irreg.php?secId=" . $r['Program_section'] . "&stdid=" . $r[1] . "";
            }
            $seq = ($pages * $entries) + $cnt;
            $dp = ($seq < 10 ? 'PTC-000' . $seq : ($seq < 100 ? 'PTC-00' . $seq : ($seq < 1000 ? 'PTC-0' . $seq : ($seq < 10000 ? 'PTC-' . $seq : ''))));
            $tb .= '
                <tr onclick="tdhighlight(this)">
                    <td>' . $dp . '</td>
                    <td>' . $r[1] . '</td>
                    <td>' . $r[2] . '</td>
                    <td>' . $r[3] . '</td>
                    <td>' . $r[4] . '</td>
                    <td>' . $r[5] . '</td>
                    <td>' . $r[6] . '</td>
                    <td>' . $r[7] . '</td>
                    <td>' . $r[8] . '</td>
                    <td>' . $r[9] . '</td>
                    <td>' . $r[10] . '</td>
                    <td>' . $r[11] . '</td>
                    <td>' . $r[12] . '</td>
                    <td>' . $r[13] . '</td>
                    <td>' . number_format((float)$tuitionFee, 2, '.', '') . '</td>
                    <td>' . number_format((float)$labFee, 2, '.', '') . '</td>
                    <td>' . number_format((float)$compFee, 2, '.', '') . '</td>
                    <td>' . number_format((float)$nstpFee, 2, '.', '') . '</td>';
            
            // Add dynamic fee values based on program status (only Unifast)
            foreach ($feeColumns as $fee) {
                $feeValue = 0;
                if ($r['program_status'] === 'Unifast') {
                    $feeValue = $fee['unifast_new'];
                    $total += $feeValue;
                    $tb .= '<td>' . number_format((float)$feeValue, 2, '.', '') . '</td>';
                }
            }
            
            $tb .= '<td>' . number_format((float)$total, 2, '.', '') . '</td>';
            $tb .= '</tr>';
            $cnt++;
        }
    } else {
        $tb .= '<tr><td colspan="' . (18 + count($feeColumns)) . '">No data found</td></tr>';
    }

    echo $tb .= '</tbody>';
}











if (isset($_POST['first_sheet_tb'])) {
    if (isset($_POST['data'])) {
        $jsonData = $_POST['data'];
        $data = json_decode($jsonData, true);

        if ($data !== null) {
            if (!empty($data)) {
                $firstRow = reset($data);
                $columnReferences = array_keys($firstRow);
            } else {
                echo 'Error: No data found in the JSON array.';
                exit;
            }

            $originalFile = '../excel_files/BILLING.xlsx';
            $destination = 'EXPORTED/';
            createDirectory($destination);
            $destination .= "" . $ay . " - " . $sem . " Semester - BILLING.xlsx";

            if (copy($originalFile, $destination)) {
                try {
                    $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $spreadSheet = $Reader->load($destination);
                    $worksheet = $spreadSheet->getActiveSheet();
                    $worksheet = $spreadSheet->getSheet('0');

                    $columnReferences = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD'];
                    // $columnReferences = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];

                    $cellnum = 14;
                    /*   $originalRow = $worksheet->getRowIterator(2)->current();
                    $clonedRow = clone $originalRow; */

                    foreach ($data as $rowData) {
                        /*    $clonedRow = clone $originalRow;

                        foreach ($rowData as $index => $cellValue) {
                            $column = $columnReferences[$index];
                            $cellCoordinate = $column . $cellnum;
                        } */
                        $worksheet->insertNewRowBefore($cellnum);
                        $cellnum++;
                    }

                    $worksheet->fromArray($data, NULL, 'A13');

                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadSheet); // Update WriterXlsx to \PhpOffice\PhpSpreadsheet\Writer\Xlsx
                    $writer->save($destination);

                    echo $destination;
                } catch (Exception $e) {
                    die('Error loading or saving file: ' . $e->getMessage());
                }
            } else {
                echo 'Failed to copy the file.';
            }
        } else {
            echo 'Error: Failed to decode JSON data.';
        }
    } else {
        echo 'Error: Missing data key in the request.';
    }
}

function stylearr($bgcolor)
{
    $styleArray = [
        'font' => [
            'bold' => true
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000020'],
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => $bgcolor],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ];
    return $styleArray;
}

if (isset($_POST['setpages'])) {
    $wheres = '';
    if ($sectionid !== "") {
        $wheres =  " and es.Program_section='$sectionid'";
    } else if ($progyearid !== "") {
        $wheres =  " and es.Program_year='$progyearid'";
    } else if ($programid !== "") {
        $wheres =  " and pl.Program_list_id = '$programid'";
    }

    $sql = "SELECT COUNT(*) FROM `enrolledstudents` es 
    JOIN `students` s ON s.Student_idno = es.Student_id 
    JOIN `student_pinfo` sp ON sp.Student_id = s.Student_idno
    JOIN `program_year` py ON py.Program_year_id = es.Program_year
    JOIN `program_list` pl ON pl.Program_list_id = py.Program_list_id
    JOIN `year_level` yl ON yl.year_level_id = py.Year_level_id
    where  es.School_year = '$ay' and es.Semester  = '$sem'" . $wheres . " and py.ProgStatus_id = '2' and concat(s.Student_firstname, s.Student_idno, s.Student_middlename, s.Student_lastname) like concat('%$search%')";
    pgs($sql, $entries);
}

if (isset($_POST['setsections'])) {
    $sql = "SELECT
    pys.Prog_year_section_id,
    CONCAT(s.letter,' (',(SELECT COUNT(*) FROM `enrolledstudents` es WHERE es.Program_section=pys.Prog_year_section_id),' Students)'),
    s.letter
    FROM
        `program_year_sections` pys
        LEFT JOIN sections s 
        ON pys.Section_id=s.Section_id
    WHERE pys.Program_year_id='$progyearid' AND (
                pys.academic_id = '$ayid' OR pys.School_year = '$ay' AND pys.Semester = '$sem'
            )ORDER BY s.Section_id ASC ";
    $res = execquery($sql);
    $data = '<option value="" selected>Select Section</option>';
    opttags($res, $data, '', 'letter');
}


if (isset($_POST['setyearlvl'])) {
    $sql = "SELECT
    py.Program_year_id,
    CONCAT(
        yl.Year_level,
        ' (',
        (
        SELECT
            COUNT(*)
        FROM
            `program_year_sections` pys
        WHERE
            pys.Program_year_id = py.Program_year_id AND(
                pys.academic_id = '$ayid' OR pys.School_year = '$ay' AND pys.Semester = '$sem'
            ) and (SELECT count(*) FROM `enrolledstudents` where pys.Prog_year_section_id = `Program_section`)
    ),
    ' Sections)'
    ),
    yl.Year_level
FROM
    `program_year` py
LEFT JOIN year_level yl ON
    py.Year_level_id = yl.year_level_id
WHERE
    py.Program_list_id = '$programid'
ORDER BY
    yl.Year_level ASC";
    $res = execquery($sql);
    $data = '<option value="" selected >Select Year Lvl</option>';
    opttags($res, $data, '', 'yl');
}


if (isset($_POST['setay'])) {     
    $sql = "SELECT DISTINCT(ass.school_year),ass.school_year FROM `academic_status` ass WHERE 1 ORDER BY ass.school_year DESC";
    $res = execquery($sql);
    $data = '<option value="" selected >Select A.Y.</option>';
    opttags($res, $data);
}

if (isset($_POST['setsem'])) {
    $sql = "SELECT ass.academic_id,ass.semester FROM `academic_status` ass WHERE 1 AND ass.school_year='$ay' and ass.semester != '3rd'  ORDER BY ass.semester asc";
    $res = execquery($sql);
    $data = ' <option value="" selected >Select Sem</option>';
    opttags($res, $data);
}

if (isset($_POST['setprograms'])) {
    $sql = "SELECT DISTINCT pl.Program_list_id, pl.Program_code 
    FROM program_list pl
    JOIN program_year py ON py.Program_list_id = pl.Program_list_id
    WHERE py.ProgStatus_id = '2' 
    AND pl.Program_code NOT LIKE '%EXEC%' 
    AND pl.Program_code NOT LIKE '%SHS%' 
    ORDER BY pl.Program_list_id ASC;
    ";
    $res = execquery($sql);
    $data = '<option value="" selected >Select Program</option>';
    opttags($res, $data);
}
