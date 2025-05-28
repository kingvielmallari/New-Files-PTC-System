$(document).ready(function () {
    $('#sectioningprog').change(function () {
        var sem = $('#sectioningsem').val();
        var sy = $('#sectioningsy').val();
        var d = $(this).val();
        var yl = $('#sectioningyl');
        if (sem == "" && sy == "") {
            message = "Please Select School Year and Sem";
            messToast(message);
        } else if (sem == "") {
            message = "Please Select Sem";
            messToast(message);
        } else if (sy == "") {
            message = "Please Select School Year";
            messToast(message);
        } else {
            $.getJSON("functionsec.php", {
                getYearLevel: "",
                progID: d
            },
                function (data, textStatus, jqXHR) {
                    if (data != "") {
                        yl.html('<option value="" selected disabled>Select Year Level</option>');
                        $.each(data, function (id, let) {
                            var spl = let.split('|');
                            yl.append(`<option value="` + spl[0] + `">` + spl[1] + `</option>`)
                        });
                    } else {
                        yl.html('<option value="">Not Available</option>');
                    }
                }
            );
        }
    });
    $('#sectioningyl').change(function () {
        var d = $(this).val();
        var sem = $('#sectioningsem').val();
        var sy = $('#sectioningsy').val();
        var load = $('#loading');
        if (sy == null && sem == null) {
            message = "Please Select School Year and Semester";
            messToast(message);
        } else if (sem == null) {
            message = "Please Select Semester";
            messToast(message);
        } else if (sy == null) {
            message = "Please Select School Year";
            messToast(message);
        } else {
            setTb(yl, sem, sy);
        }
    });
    $('#sectioningsem').change(function () {
        var sy = $('#sectioningsy').val();
        var sem = $(this).val();
        var yl = $('#sectioningyl').val();
        var pg = $('#sectioningprog').val();
        var load = $('#loading');

        if (sy == null) {
            message = "Please Select School Year";
            messToast(message);
        }
        else if (yl == "" || pg == "") {
            message = "Please Select Program and Year Level";
            messToast(message);
        }
        else {
            setTb(yl, sem, sy);
        }
    });
    $('#sectioningsy').change(function () {
        var sy = $(this).val();
        var sem = $('#sectioningsem').val();
        var yl = $('#sectioningyl').val();
        var pg = $('#sectioningprog').val();
        var load = $('#loading');

        if (sem == null) {
            message = "Please Select Sem";
            messToast(message);
        }
        else if (yl == "" || pg == "") {
            message = "Please Select Program and Year Level";
            messToast(message);
        }
        else {
            setTb(yl, sem, sy);
        }
    });
    setTb("", "", "");

});

function setTb(yl, sem, sy) {
    var load = $('#loading');
    $('.table > tbody')
        .html(`<tr><td colspan="6" class="text-center">` +
            loadd() + `</td></tr>`);
    $.post("functionsec.php", {
        setTableSec: "",
        yearId: yl,
        sem: sem,
        sy: sy
    },
        function (data, textStatus, jqXHR) {
            load.show();
            setTimeout(() => {
                load.hide();
                $('.table > tbody').html(data);
            }, 500);
        }
    );
}

function loadd() {
    var l = `<div class="spinner-border text-success" id="loading" role="status">
    <span class="sr-only"></span>
</div>`;
    return l;
}


function closeT() {
    $('#liveToast').hide();
}
function messToast(message) {
    $('#liveToast').fadeIn();
    $('#resultT').html(message);
    setTimeout(() => {
        $('#liveToast').fadeOut();
    }, 5000);
}
function getSec(secid) {
    var sp = secid.split('|');
    $.post("functionsec.php", {
        getSec: "",
        secid: sp[0],
        section: sp[1]
    },
        function (data, textStatus, jqXHR) {
            $('#bodymodal').html(data);
        }
    );
}
function getSched(data) {
    var og = data;
    var sp = data.split('|');
    $.post("../Schedule/getschedprocess.php", {
        getSched: "",
        pysec: sp[0],
        sy: sp[1],
        sem: sp[2],
        sec: sp[3]
    },
        function (data, textStatus, jqXHR) {
            $('#sectionSchedBody').html(
                `<div class="float-start">` +
                `<h5>School Year: ` + sp[1] + `</h5>` +
                `<h5>Semester: ` + sp[2] + `</h5>` +
                `<h5>Section: ` + sp[3] + `</h5></div>` +
                `<br><br><br><a href="exportexcel.php?data=` + og + `" class="btn btn-primary float-end" >Export Grading Sheet</a><br>`
                + data);
        }
    );
}