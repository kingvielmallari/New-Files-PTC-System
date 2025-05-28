$(document).ready(function () {

    setay();
    setprog();

    $("select[name='prog']").change(function (e) {
        setyl();
        if ($("select[name='ay']").val() != "" && $("select[name='sem']").val() != "" && $("select[name='yl']").val() != "") {
            setTb($("select[name='yl']").val(), $("select[name='sem']").val(), $("select[name='ay']").val());
        }
    });

    $("select[name='ay']").change(function (e) {
        setsem();
        if ($("select[name='ay']").val() != "" && $("select[name='sem']").val() != "" && $("select[name='yl']").val() != "") {
            setTb($("select[name='yl']").val(), $("select[name='sem']").val(), $("select[name='ay']").val());
        }
    });

    $("select[name='sem']").change(function (e) {
        if ($("select[name='ay']").val() != "" && $("select[name='sem']").val() != "" && $("select[name='yl']").val() != "") {
            setTb($("select[name='yl']").val(), $("select[name='sem']").val(), $("select[name='ay']").val());
        }
    });

    $("select[name='yl']").change(function (e) {
        setTb($("select[name='yl']").val(), $("select[name='sem']").val(), $("select[name='ay']").val());
    });
});

function setTb(yl, sem, sy) {
    $('.table > tbody')
        .html(`<tr><td colspan="6" class="text-center">` +
            loadd() + `</td></tr>`);
    $.post("functionsec.php", {
        setTableSec: "",
        yearId: yl,
        sem: sem,
        textsem: $("select[name='sem'] option:selected").text(),
        sy: sy
    },
        function (data, textStatus, jqXHR) {
            setTimeout(() => {
                $('.table > tbody').html(data);
            }, 500);
        }
    );
}

function setyl() {
    st = $("select[name='yl']");
    elems = $("select[name='prog']");
    datas = $(elems).serializeArray();
    datas.push({ name: "setyl", value: "" });
    setSelect(st, datas);
}

function setay() {
    st = $("select[name='ay']");
    datas = [];
    datas.push({ name: "setay", value: "" });
    setSelect(st, datas);
}

function setsem() {
    st = $("select[name='sem']");
    elems = $("select[name='ay']");
    datas = $(elems).serializeArray();
    datas.push({ name: "setsem", value: "" });
    setSelect(st, datas);
}

function setprog() {
    st = $("select[name='prog']");
    datas = []
    datas.push({ name: "setprog", value: "" });
    setSelect(st, datas);
}

function setSelect(select, data) {
    $.post("../Grades/grades.php", data,
        function (data, textStatus, jqXHR) {
            $(select).html(data);
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