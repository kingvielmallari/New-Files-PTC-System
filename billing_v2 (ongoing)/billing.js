
file = "func.php";
$(document).ready(function () {

    setay();
    setprograms();

    $('#ay').change(function (e) {
        setsem();
    });


    $('#ayid').change(function (e) {
        /* if ($('#programid').val() !== "" && $('#progyearid').val() !== "") { */
        setpages($(this));
        setyearlvl($(this));
        $("#sectionid").empty();
        $("#sectionid").append(`<option value="">Select year level</option>`);
        /* } */
    });

    $('#programid').change(function (e) {
        setyearlvl($(this));
        setpages($(this));
    });

    $('#progyearid').change(function (e) {
        setsections($(this));
        setpages($(this));
    });

    $('#sectionid, #entries_fp').change(function (e) {
        setpages($(this));
    });

    $('#pages_fp').change(function (e) {
        settbfp();
    });

    $("input[type='text'].resettb").keyup(function (e) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            setpages();
        }, 250);
    });

});

let feeType = 'new'; // Default to new fees

function toggleFeeType() {
    feeType = feeType === 'new' ? 'old' : 'new';
    document.getElementById('feeToggle').textContent = feeType === 'new' ? 'Switch to Old Fees' : 'Switch to New Fees';
    setpages(); // Refresh the table with the new fee type
}

async function updatesex(btn) {
    stdid = $(btn).closest('tr').find('td:eq(1)').text();
    sel = $(btn).closest('td').find('select');
    if (sel.val() === "") {
        reqfunc($(sel));
        return false;
    }
    var fd = new FormData();
    fd.append('sel', $(sel).val());
    fd.append('stdid', stdid);
    fd.append('updatesex', '');
    var res = await myajax(file, fd);
    if (res === "1") {
        $(btn).closest('td').html($(sel).val());
        tsuccess(`Data updated`);
    } else {
        terror(`Theres something wrong, please try again.`, 3);
    }
}

let useOldRates = false;

function toggleFeeRates() {
    useOldRates = !useOldRates;
    document.getElementById('rateToggleBtn').textContent = useOldRates ? "Use New Rates" : "Use Old Rates";
    setpages(); // Refresh the table with the new rate setting
}

// Modify your setpages function to include the rate parameter
function setpages() {
    const useOld = useOldRates ? 'true' : 'false';
    // ... rest of your existing setpages code ...
    // Make sure to include use_old_rates in your AJAX data
    data += '&use_old_rates=' + useOld;
    // ... rest of your AJAX call ...
}

function cancelsex(btn) {
    $(btn).closest('td').html(``);
    $(btn).closest('td').attr(`ondblclick`, 'setupdatesex(this)');
}

async function setupdatesex(td) {
    $(td).closest('td').attr(`ondblclick`, '');
    stdid = $(td).closest('tr').find('td:eq(1)').text();
    console.log(stdid);
    $(td).html(`
    <select class="form-control form-control-sm">
        <option value="">Select sex</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>
    <button class="btn btn-success btn-sm" onclick="updatesex(this)"><i class="bi bi-check"></i></button>
    <button class="btn btn-danger btn-sm" onclick="cancelsex(this)"><i class="bi bi-x"></i></button>
    `);
}

async function first_sheet_tb(btn) {
    form = $('#formpl')[0];
    elems = $(form).find('select:required');
    if (!validator(elems)) {
        return;
    }
    $(btn).prop('disabled', true);
    twarning(`Please don't do anything yet. This might take a while, thank you.`, 120);
    var tbody = $('#first_sheet_tb tbody');
    var data = [];
    tbody.find('tr').each(function () {
        var row = {};
        $(this).find('td').each(function (index, cell) {
            row[index] = $(cell).text();
        });
        data.push(row);
    });
    var jsonData = JSON.stringify(data);
    var fd = new FormData(form);
    fd.append('sem', $('#ayid option:selected').text());
    fd.append('data', jsonData);
    fd.append('first_sheet_tb', '');
    var res = await myajax(file, fd);
    if (res.includes("EXPORTED/")) {
        var link = document.createElement('a');
        link.href = res;
        link.download = getFileNameFromURL(res);
        link.click();
        tsuccess(`Downloaded.<i class="bi bi-check-circle text-success"></i>
        <i>If not downloaded, please <a href="${res}">click here.</a></i>
        `, 8);
    } else {
        reqfunc($(btn));
        terror(`There's something wrong, please try again.`, 3);
    }
    $(btn).prop('disabled', false);
}

async function exportexcel(btn) {
    tb2_excel(btn, $("#ay").val() + ", " + $('#ayid option:selected').text() + ` Semsd2ester - Billing, ${btn}`);
}

async function settbfp() {
    tb = $("#first_sheet_tb");
    $(tb).html(loadingsm("Fetching data..."));
    entries = $("#entries_fp").val();
    pages = $("#pages_fp").val();
    search = $("#search_fp").val();
    form = $('#formpl')[0];
    var formdata = new FormData(form);
    formdata.append('sem', $('#ayid option:selected').text());
    formdata.append('entries', entries);
    formdata.append('search', search);
    formdata.append('pages', (pages == undefined || pages == '' ? 0 : pages));
    formdata.append('settbfp', '');
    var res = await myajax(file, formdata);
    $(tb).html(res);
    settbsort();

}

async function setpages(btn = undefined) {
    form = $('#formpl')[0];
    elems = $(form).find('input:required,select:required');
    if (!validator(elems)) {
        return;
    }
    $("#pages_fp").prop('disabled', true);
    entries = $("#entries_fp").val();
    search = $("#search_fp").val();
    var formdata = new FormData(form);
    formdata.append('sem', $('#ayid option:selected').text());
    formdata.append('entries', entries);
    formdata.append('search', search);
    formdata.append('setpages', '');
    var res = await myajax(file, formdata);
    $("#pages_fp").html(res);
    $("#pages_fp").prop('disabled', false);
    await settbfp();
}

async function setsections() {
    st = $("#sectionid");
    pid = $("#progyearid");
    if (!$(pid).val()) {
        $(st).find('option[value!=""]').remove();
        return;
    }
    form = $('#formpl')[0];
    var formdata = new FormData(form);
    formdata.append('sem', $('#ayid option:selected').text());
    formdata.append('setsections', '');
    var res = await myajax(file, formdata);
    $(st).html(res);
}


async function setyearlvl() {
    st = $("#progyearid");
    pid = $("#programid");
    if (!$(pid).val()) {
        $(st).find('option[value!=""]').remove();
        return;
    }
    form = $('#formpl')[0];
    var formdata = new FormData(form);
    formdata.append('sem', $('#ayid option:selected').text());
    formdata.append('setyearlvl', '');
    var res = await myajax(file, formdata);
    $(st).html(res);
}

async function setay() {
    st = $("#ay");
    form = $('#formpl')[0];
    var formdata = new FormData(form);
    formdata.append('setay', '');
    var res = await myajax(file, formdata);
    $("#ay").html(res);
}

async function setsem() {
    ay = $("#ay");
    st = $("#ayid");
    if (!$(ay).val()) {
        $(st).find('option[value!=""]').remove();
        return;
    }
    form = $('#formpl')[0];
    var formdata = new FormData(form);
    formdata.append('setsem', '');
    var res = await myajax(file, formdata);
    $(st).html(res);
}

async function setprograms() {
    st = $("#programid");
    form = $('#formpl')[0];
    var formdata = new FormData(form);
    formdata.append('setprograms', '');
    var res = await myajax(file, formdata);
    $(st).html(res);
}

$("button.next,button.prev").click(function (e) {
    e.preventDefault();
    p = $("#pages_fp");
    l = $(p).find('option').length;
    s = $(p).prop('selectedIndex');
    if ($(this).hasClass('next')) {
        if (s != (l - 1)) {
            s++;
            $(p).prop('selectedIndex', s);
            settbfp();
        } else {
            reqfunc(p);
        }
    }
    if ($(this).hasClass('prev')) {
        if (s != 0) {
            s--;
            $(p).prop('selectedIndex', s);
            settbfp();
        } else {
            reqfunc(p);
        }
    }
});