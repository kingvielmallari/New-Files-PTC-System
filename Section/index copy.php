    <?php
    include_once '../Menu/Header.php';
    include_once 'functionsec.php';
    ?>
    <style>
        th,
        td {
            text-align: left;
        }
    </style>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Section</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Section</li>
                    <li class="breadcrumb-item">College</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <table class="bg-body mb-3 shadow rounded custom" id="seltb" width="100%">
                        <thead>
                            <tr>
                                <th>
                                    <label class="req">Select Academic Year</label>
                                </th>
                                <th>
                                    <label class="req">Select Semester</label>
                                </th>
                                <th>
                                    <label class="req">Program</label>
                                </th>
                                <th>
                                    <label class="req">Year Level</label>
                                </th>
                                <th>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> <select id="sectioningsy" class="form-select" required>
                                        <option value="" selected disabled>Select School Year</option>
                                        <?php
                                        echo getAllAcadYeartoOpt();
                                        ?>
                                    </select></td>
                                <td>
                                    <select id="sectioningsem" class="form-select" required>
                                        <option value="" selected disabled>Select Semester</option>
                                        <option value="1st">1st</option>
                                        <option value="2nd">2nd</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="sectioningprog" class="form-select" required>
                                        <option value="" selected disabled>Select Program</option>
                                        <?php
                                        $sqlgetprogram = "SELECT * FROM `program_list`";
                                        $res = mysqli_query($conn, $sqlgetprogram);
                                        if (mysqli_num_rows($res) > 0) {
                                            while ($row = mysqli_fetch_array($res)) {
                                                echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select id="sectioningyl" class="form-select">
                                        <option value="">Select Program</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="spinner-border text-success" id="loading" role="status" style="display: none;">
                                        <span class="sr-only"></span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- LIST OF FRESHMEN STUDENTS -->
                            <table class="table table-hover table-bordered table-striped text-center" style="text-align: left;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Section</th>
                                        <th>Semester</th>
                                        <th>School Year</th>
                                        <th>No of Students</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                  /*   echo setTableSec("", "", "")  */?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <div class="modal fade bd-example-modal-lg" id="stdlist" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="padding: 20px;">
                <div id="bodymodal"></div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-modal-fullscreen" tabindex="-1" id="sectionSched" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="padding: 20px;">
                <div class="modal-body" id="sectionSchedBody">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">Back</button>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
            <div class="toast-header">
                <!--  <img src="..." class="rounded me-2" alt="..."> -->
                <strong class="me-auto">Message</strong>
                <!-- <small>11 mins ago</small> -->
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" onclick="closeT();"></button>
            </div>
            <div class="toast-body">
                <span id="resultT"></span>
            </div>
        </div>
    </div>
    <script src="section.js"></script>t
    <?php include_once '../Menu/Footer.php'; ?>