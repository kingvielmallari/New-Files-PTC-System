<?php
include_once '../Menu/Header.php';


addlogs('Visit Page: Manage Assessment Fees');

?>
<?php if (!(valid4())) {
    echo '<script>window.location="../Dashboard";</script>';
} ?>


<main id="main" class="main">
 
    <div class="pagetitle">
        <h1>Asssessment</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../Dashboard/">Home</a></li>
                <li class="breadcrumb-item">Assessment Fees</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row  mt-3">
                            <div class="col text-start">
                                <label for="ae">Entries</label>
                                <select id="entries" name="entries" class="form-select-sm entries resettb">
                                    <option value="20">20</option>
                                </select>

                            </div>
                          
                            <div class="col-3 text-end">
                                <input type="text" class="form-control search resettb" name="search" id="search" placeholder="Search...">
                            </div>
                        </div>
                        <div class="row pt-2">
                            <div class="col table-responsive">
                                <table class="table table-bordered table-striped text-center" id="feestb">
                                  
                                     <thead >
                                        <tr>
                                            <th style="vertical-align: middle;" rowspan="2">#</th>
                                            <th style="vertical-align: middle;" rowspan="2">Payment Name</th>
                                            <th style="vertical-align: middle;" colspan="2">Paying Program</th>
                                            <th style="vertical-align: middle;" colspan="2">Unifast Program</th>
                                            <th style="vertical-align: middle;" colspan="2">Executive Program</th>
                                            <th style="vertical-align: middle;" rowspan="2">Actions</th>
                                        </tr>
                                        <tr>
                                            <th>Old</th>
                                            <th>New</th>
                                            <th>Old</th>
                                            <th>New</th>
                                            <th>Old</th>
                                            <th>New</th>
                                        </tr>
                                    
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="12">
                                                <button class="btn  btn-primary" onclick="setaddprogram(this);"> <span>Add Fee </span><i class="bi bi-cash "></i></button>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td colspan="2">
                                                <label for="progcode" class="fw-bold">Payment Name</label>
                                                <input type="text" class="form-control" name="progcode" id="progcode" minlength="3" maxlength="50" required>
                                           <td colspan="1">
                                                <label for="progcode" class="fw-bold">Paying Old</label>
                                                <input type="text" class="form-control" name="progcode" id="progcode" minlength="3" maxlength="50" required>
                                            </td>
                                            <td colspan="1">
                                                <label for="progcode" class="fw-bold">Paying New</label>
                                                <input type="text" class="form-control "name="progcode" id="progcode" minlength="3" maxlength="50" required>
                                            </td>
                                            <td colspan="1">
                                                <label for="progcode" class="fw-bold">Unifast Old</label>
                                                <input type="text" class="form-control " name="progcode" id="progcode" minlength="3" maxlength="50" required>
                                            </td>
                                            <td colspan="1">
                                                <label for="progcode" class="fw-bold">Unifast New</label>
                                                <input type="text" class="form-control " name="progcode" id="progcode" minlength="3" maxlength="50" required>
                                            </td>
                                             <td colspan="1">
                                                <label for="progcode" class="fw-bold">Executive Old</label>
                                                <input type="text" class="form-control " name="progcode" id="progcode" minlength="3" maxlength="50" required>
                                            </td>
                                            <td colspan="1">
                                                <label for="progcode" class="fw-bold">Executive New</label>
                                                <input type="text" class="form-control " name="progcode" id="progcode" minlength="3" maxlength="50" required>
                                            </td>
                                               <td class="align-middle">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="addprogram(this);"><span>Save </span><i class="bi bi-check-circle"></i></button>

                                                <button class="btn btn-sm btn-danger" onclick="cancelbtn(this);"><span>Cancel </span><i class="bi bi-x-circle"></i></button>
                                            </td>
                                        
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row  mt-2">
                            <div class="col"></div>
                            <div class="col-auto text-center">
                                <div class="input-group input-group-sm text-end">
                                    <button name="nextbtn" class="btn btn-sm btn-secondary prev me-1">
                                        <i class="bi bi-caret-left"></i></button>
                                    <select name="page" id="page" class="form-control resettb page">
                                    </select>
                                    <button name="prevbtn" class="btn btn-sm btn-secondary next ms-1 ">
                                        <i class="bi bi-caret-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script src="fees2.js"></script>
<?php
include_once '../Menu/Footer.php';
?>