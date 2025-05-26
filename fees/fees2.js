
//delete program


async function manual(btn) {
    var fd = new FormData();
    fd.append('manual', '');
    var res = await myajax('yearlevelsfunc.php', fd);
    modalxl(`Manual`, res);
}

function setaddprogram(btn) {
    var tr = $(btn).closest("tr");
    var tds = $(tr).find("td");
    var elem = `
        <td colspan="2">
            <label for="payment_name" class="fw-bold">Payment Name</label>
            <input type="text" value="" class="form-control text-center" name="payment_name" id="payment_name" minlength="3" maxlength="100" required>
        </td>
        <td style="width: 150px;">
            <label for="paying_old" class="fw-bold">Paying Old</label>
            <input type="text" value="" class="form-control form-control-sm text-center" name="paying_old" id="paying_old" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="paying_new" class="fw-bold">Paying New</label>
            <input type="text" value="" class="form-control form-control-sm text-center" name="paying_new" id="paying_new" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="unifast_old" class="fw-bold">Unifast Old</label>
            <input type="text" value="" class="form-control form-control-sm text-center" name="unifast_old" id="unifast_old" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="unifast_new" class="fw-bold">Unifast New</label>
            <input type="text" value="" class="form-control form-control-sm text-center" name="unifast_new" id="unifast_new" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="executive_old" class="fw-bold">Executive Old</label>
            <input type="text" value="" class="form-control form-control-sm text-center" name="executive_old" id="executive_old" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="executive_new" class="fw-bold">Executive New</label>
            <input type="text" value="" class="form-control form-control-sm text-center" name="executive_new" id="executive_new" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 200px;" class="align-middle">
            <button type="submit" class="btn btn-sm btn-success" onclick="addprogram(this);"><span>Submit </span><i class="bi bi-check-circle"></i></button>
            <button class="btn btn-sm btn-danger" onclick="cancelbtn(this);"><span>Cancel </span><i class="bi bi-x-circle"></i></button>
        </td>`;
    $(tr).html(elem);
}

function addprogram(btn) {
    var tr = $(btn).closest("tr");
    var td = $(btn).closest("td");
    var btns = $(td).html();
    $(td).html(loadingsm());
    var tf = true;

    var requiredFields = [
        'payment_name',
        'paying_old',
        'paying_new',
        'unifast_old',
        'unifast_new',
        'executive_old',
        'executive_new'
    ];

    requiredFields.forEach(function(field) {
        var input = $(tr).find('[name="' + field + '"]');
        if (input.length && input.prop('required')) {
            var val = input.val();
            var minlength = parseInt(input.attr('minlength'), 10) || 0;
            if (!val || val.length < minlength) {
                twarning((input.attr('in') || field) + " " + (input.attr('title') || 'is required'));
                tf = false;
            }
        }
    });

    var datas = [];
    requiredFields.forEach(function(field) {
        var input = $(tr).find('[name="' + field + '"]');
        if (input.length) {
            datas.push({ name: field, value: input.val() });
        }
    });
    datas.push({ name: "addfee", value: "" });

    if (tf) {
        $.post("yearlevelsfunc.php",
            datas,
            function (data, textStatus, jqXHR) {
                if (data.trim() == "Added") {
                    tsuccess(`Fee added.`);
                    $(td).html(btns);
                    pages($("#entries").val(), $("#page"), $("#search").val(), "feestb");
                } else {
                    terror(data.trim());
                    $(td).html(btns);
                }
            }
        );
    } else {
        twarning(`Fillup all fields.`);
        $(td).html(btns);
    }
}


function setupprogram(btn) {
    tr = $(btn).closest("tr");
    var tds = $(tr).find("td"); 
    // Get the status from the appropriate cell (assuming status is in tds[6])
    var status = '';
    var disableBtnLabel = status === 'Enabled' ? 'Disable' : 'Enable';
    var disableBtnClass = status === 'Enabled' ? 'btn-secondary' : 'btn-success';

    elem = `
        <td colspan="2">
            <label for="payment_name" class="fw-bold">Payment Name</label>
            <input type="text" value="` + $(tds[1]).text().trim() + `" class="form-control text-center" name="payment_name" id="payment_name" minlength="3" maxlength="100" required>
        </td>
        <td style="width: 150px;">
            <label for="paying_old" class="fw-bold">Paying Old</label>
            <input type="text" value="` + $(tds[2]).text().trim() + `" class="form-control form-control-sm text-center" name="paying_old" id="paying_old" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="paying_new" class="fw-bold">Paying New</label>
            <input type="text" value="` + $(tds[3]).text().trim() + `" class="form-control form-control-sm text-center" name="paying_new" id="paying_new" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="unifast_old" class="fw-bold">Unifast Old</label>
            <input type="text" value="` + $(tds[4]).text().trim() + `" class="form-control form-control-sm text-center" name="unifast_old" id="unifast_old" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="unifast_new" class="fw-bold">Unifast New</label>
            <input type="text" value="` + $(tds[5]).text().trim() + `" class="form-control form-control-sm text-center" name="unifast_new" id="unifast_new" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="executive_old" class="fw-bold">Executive Old</label>
            <input type="text" value="` + $(tds[6]).text().trim() + `" class="form-control form-control-sm text-center" name="executive_old" id="executive_old" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 150px;">
            <label for="executive_new" class="fw-bold">Executive New</label>
            <input type="text" value="` + $(tds[7]).text().trim() + `" class="form-control form-control-sm text-center" name="executive_new" id="executive_new" minlength="1" maxlength="20" required style="width: 100%; margin: 0 auto;">
        </td>
        <td style="width: 300px;" class="align-middle">
            <button type="submit" class="btn btn-sm btn-success" onclick="upprogram(this);" value="` + $(btn).val() + `"><span>Save </span><i class="bi bi-check-circle"></i></button>
            <button class="btn btn-sm btn-warning" onclick="cancelbtn(this);"><span>Cancel </span><i class="bi bi-x-circle"></i></button>
            <button class="btn btn-sm btn-danger" onclick="delpayment(this);" value="` + $(btn).val() + `"><span>Delete </span><i class="bi bi-trash"></i></button>
        </td>`;
    $(tr).html(elem);
}



function delpayment(btn) {
    var payment_id = $(btn).val();
    if (confirm("Are you sure you want to delete this payment?")) {
        $.post("yearlevelsfunc.php", {
            payment_id: payment_id,
            delpayment: ""
        },
        function (data) {
            if (data.trim() == "Success") {
                tsuccess(`Payment deleted.`);
                pages($("#entries").val(), $("#page"), $("#search").val(), "feestb");
            } else {
                terror(data.trim());
            }
        });
    }
}

function cancelbtn(btn) {
    tb($("#entries").val(), $("#page").val(), $("#search").val(), "feestb");
}

function upprogram(btn) {
    var tr = $(btn).closest("tr");
    var td = $(btn).closest("td");
    var btns = $(td).html();
    var t = $("#feestb");
    $(td).html(loadingsm());
    var tf = true;

    // Only validate and collect the specific fields as per the provided HTML structure
    var requiredFields = [
        'payment_name',
        'paying_old',
        'paying_new',
        'unifast_old',
        'unifast_new',
        'executive_old',
        'executive_new'
    ];

    // Validate required fields
    requiredFields.forEach(function(field) {
        var input = $(tr).find('[name="' + field + '"]');
        if (input.length && input.prop('required')) {
            var val = input.val();
            var minlength = parseInt(input.attr('minlength'), 10) || 0;
            if (!val || val.length < minlength) {
                twarning((input.attr('in') || field) + " " + (input.attr('title') || 'is required or too short'));
                tf = false;
            }
        }
    });

    // Collect data
    var datas = [];
    requiredFields.forEach(function(field) {
        var input = $(tr).find('[name="' + field + '"]');
        if (input.length) {
            datas.push({ name: field, value: input.val() });
        }
    });
    datas.push({ name: "upprogram", value: "" });
    datas.push({ name: "progid", value: $(btn).val() });

    if (tf) {
        $.post("yearlevelsfunc.php",
            datas,
            function (data, textStatus, jqXHR) {
                if (data.trim() == "Success") {
                    tsuccess(`Program updated.`);
                    $(td).html(btns);
                    pages($("#entries").val(), $("#page"), $("#search").val(), "feestb");
                } else {
                    terror(data.trim());
                    $(td).html(btns);
                }
            }
        );
    } else {
        twarning(`Fillup all fields.`);
        $(td).html(btns);
    }
}


function pages(entries, page, search, tbn) {
    $.post("../pgntb/pages.php",
        {
            pages: "",
            e: entries,
            s: search,
            t: tbn
        },
        function (data) {
            $(page).html(data.trim());
            tb(entries, page.val(), search, tbn);
        }
    );
}

function tb(entries, page, search, tb) {
    tbdy = '#' + tb + ' > tbody';
    // console.log(tbdy);
    $(tbdy).html(loadingsm());
    $.post("../pgntb/tbs.php",
        {
            tb: "",
            e: entries,
            p: page,
            s: search,
            t: tb
        },
        function (data) {
            $(tbdy).html(data.trim());
            settbsort();
        }
    );

}

$(document).ready(function () {
    $("button.next,button.prev").click(function (e) {
        div = $(this).closest("div.card-body");
        t = $(div).find("table");

        var l = $('#page > option').length;
        var s = $('#page').prop('selectedIndex');

        if (s != l - 1 && $(this).hasClass("next")) {
            s++;
            $('#page').prop('selectedIndex', s);
            tb($("#entries").val(), $("#page").val(), $("#search").val(), "feestb");
        } else if ((s <= l - 1 && s > 0) && $(this).hasClass("prev")) {
            s--;
            $('#page').prop('selectedIndex', s);
            tb($("#entries").val(), $("#page").val(), $("#search").val(), "feestb");
        }
    });

    let timeoutId;

    $("#search").keyup(function (e) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function () {    
            pages($("#entries").val(), $("#page"), $("#search").val(), "feestb");
        }, 500); // Schedule a call to setpgs() after 500 milliseconds
    });


    pages($("#entries").val(), $("#page"), $("#search").val(), "feestb");

    $("button.export").click(function (e) {
        htmltb_to_excel('xlsx', $(this).attr("targettb"));
    });

});