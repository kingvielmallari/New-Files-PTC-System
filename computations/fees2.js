
//delete program




function setaddprogram(btn) {
    var tr = $(btn).closest("tr");
    var elem = `
        <td></td>
        <td>
            <label for="program" class="fw-bold">Program</label>
<input 
  type="text" 
  class="form-control" 
  name="program" 
  id="program" 
  minlength="3" 
  maxlength="10" 
  required 
  style="text-transform: uppercase;" 
  oninput="this.value = this.value.toUpperCase();">        </td>
        <td>
            <label for="unit_old" class="fw-bold">Unit Fee Old</label>
            <input type="text" class="form-control" name="unit_old" id="unit_old" required>
        </td>
        <td>
            <label for="unit_new" class="fw-bold">Unit Fee New</label>
            <input type="text" class="form-control" name="unit_new" id="unit_new" required>
        </td>
        <td>
            <label for="lab_old" class="fw-bold">Lab Fee Old</label>
            <input type="text" class="form-control" name="lab_old" id="lab_old" required>
        </td>
        <td>
            <label for="lab_new" class="fw-bold">Lab Fee New</label>
            <input type="text" class="form-control" name="lab_new" id="lab_new" required>
        </td>
        <td>
            <label for="comp_old" class="fw-bold">Computer Fee Old</label>
            <input type="text" class="form-control" name="comp_old" id="comp_old" required>
        </td>
        <td>
            <label for="comp_new" class="fw-bold">Computer Fee New</label>
            <input type="text" class="form-control" name="comp_new" id="comp_new" required>
        </td>
        <td>
            <label for="nstp_old" class="fw-bold">NSTP Fee Old</label>
            <input type="text" class="form-control" name="nstp_old" id="nstp_old" required>
        </td>
        <td>
            <label for="nstp_new" class="fw-bold">NSTP Fee New</label>
            <input type="text" class="form-control" name="nstp_new" id="nstp_new" required>
        </td>
        <td class="align-middle">
            <button type="submit" class="btn btn-sm btn-success" onclick="addprogram(this);"><span>Save </span><i class="bi bi-check-circle"></i></button>
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

    // Fields based on the new table structure
    var requiredFields = [
        'program',
        'unit_old',
        'unit_new',
        'lab_old',
        'lab_new',
        'comp_old',
        'comp_new',
        'nstp_old',
        'nstp_new'
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
    datas.push({ name: "addfee", value: "" });

    if (tf) {
        $.post("yearlevelsfunc.php",
            datas,
            function (data, textStatus, jqXHR) {
                var resp = data.trim();
                if (resp === "Added") {
                    tsuccess(`Computation added.`);
                    $(td).html(btns);
                    pages($("#entries").val(), $("#page"), $("#search").val(), "comptb");
                } else if (resp === "Computation Already Used") {
                    twarning("Computation already exists.");
                    $(td).html(btns);
                } else {
                    terror(resp);
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
    var tr = $(btn).closest("tr");
    var tds = $(tr).find("td");

    // Fill input values from the current row
    var elem = `
        <td></td>
        <td>
            <label for="program" class="fw-bold">Program</label>
            <input type="text" class="form-control" name="program" id="program"
                minlength="3" maxlength="10" required   style="text-transform: uppercase;" 
                oninput="this.value = this.value.toUpperCase();"
                value="` + $(tds[1]).text().trim() + `">

              
  
        </td>
        <td>
            <label for="unit_old" class="fw-bold">Unit Fee Old</label>
            <input type="text" class="form-control" name="unit_old" id="unit_old" required
                value="` + $(tds[2]).text().trim() + `">
        </td>
        <td>
            <label for="unit_new" class="fw-bold">Unit Fee New</label>
            <input type="text" class="form-control" name="unit_new" id="unit_new" required
                value="` + $(tds[3]).text().trim() + `">
        </td>
        <td>
            <label for="lab_old" class="fw-bold">Lab Fee Old</label>
            <input type="text" class="form-control" name="lab_old" id="lab_old" required
                value="` + $(tds[4]).text().trim() + `">
        </td>
        <td>
            <label for="lab_new" class="fw-bold">Lab Fee New</label>
            <input type="text" class="form-control" name="lab_new" id="lab_new" required
                value="` + $(tds[5]).text().trim() + `">
        </td>
        <td>
            <label for="comp_old" class="fw-bold">Computer Fee Old</label>
            <input type="text" class="form-control" name="comp_old" id="comp_old" required
                value="` + $(tds[6]).text().trim() + `">
        </td>
        <td>
            <label for="comp_new" class="fw-bold">Computer Fee New</label>
            <input type="text" class="form-control" name="comp_new" id="comp_new" required
                value="` + $(tds[7]).text().trim() + `">
        </td>
        <td>
            <label for="nstp_old" class="fw-bold">NSTP Fee Old</label>
            <input type="text" class="form-control" name="nstp_old" id="nstp_old" required
                value="` + $(tds[8]).text().trim() + `">
        </td>
        <td>
            <label for="nstp_new" class="fw-bold">NSTP Fee New</label>
            <input type="text" class="form-control" name="nstp_new" id="nstp_new" required
                value="` + $(tds[9]).text().trim() + `">
        </td>
        <td class="align-middle">
            <button type="submit" class="btn btn-sm btn-success" onclick="upprogram(this);" value="` + $(btn).val() + `"><span>Save </span><i class="bi bi-check-circle"></i></button>
            <button class="btn btn-sm btn-warning" onclick="cancelbtn(this);"><span>Cancel </span><i class="bi bi-x-circle"></i></button>
            <button class="btn btn-sm btn-danger" onclick="delpayment(this);" value="` + $(btn).val() + `"><span>Delete </span><i class="bi bi-trash"></i></button>
        </td>
    `;
    $(tr).html(elem);
}



function delpayment(btn) {
    var payment_id = $(btn).val();
    if (confirm("Are you sure you want to delete this computation?")) {
        $.post("yearlevelsfunc.php", {
            payment_id: payment_id,
            delpayment: ""
        },
        function (data) {
            if (data.trim() == "Success") {
                tsuccess(`Payment deleted.`);
                pages($("#entries").val(), $("#page"), $("#search").val(), "comptb");
            } else {
                terror(data.trim());
            }
        });
    }
}

function cancelbtn(btn) {
    tb($("#entries").val(), $("#page").val(), $("#search").val(), "comptb");
}
function upprogram(btn) {
    var tr = $(btn).closest("tr");
    var td = $(btn).closest("td");
    var btns = $(td).html();
    $(td).html(loadingsm());
    var tf = true;

    // Fields based on the new table structure
    var requiredFields = [
        'program',
        'unit_old',
        'unit_new',
        'lab_old',
        'lab_new',
        'comp_old',
        'comp_new',
        'nstp_old',
        'nstp_new'
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
                    pages($("#entries").val(), $("#page"), $("#search").val(), "comptb");
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
            tb($("#entries").val(), $("#page").val(), $("#search").val(), "comptb");
        } else if ((s <= l - 1 && s > 0) && $(this).hasClass("prev")) {
            s--;
            $('#page').prop('selectedIndex', s);
            tb($("#entries").val(), $("#page").val(), $("#search").val(), "comptb");
        }
    });

    let timeoutId;

    $("#search").keyup(function (e) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function () {    
            pages($("#entries").val(), $("#page"), $("#search").val(), "comptb");
        }, 500); // Schedule a call to setpgs() after 500 milliseconds
    });


    pages($("#entries").val(), $("#page"), $("#search").val(), "comptb");

    $("button.export").click(function (e) {
        htmltb_to_excel('xlsx', $(this).attr("targettb"));
    });

});