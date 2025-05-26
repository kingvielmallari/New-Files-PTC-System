







$("#progidylvls, #progtype").change(function (e) {
    e.preventDefault();
    setpgsylvls();
});


function setpgsylvls() {

    entries = $("#entriesylvls").val();
    page = $('#pageylvls');
    progid = $('#progidylvls').val();
    progtype = $('#progtype').val();


    if (progid == "") {
        $('#ylvlstb').html(` <tr>
        <td colspan="3">Select Program</td>
    </tr>`)
        return;
    }
    ayid = $('#ayid').val();
    search = $('#searchylvls').val();
    $.post("yearlevelsfunc.php", {
        entries: ((entries != "") ? entries : ""),
        search: ((search != "") ? search : ""),
        progid: ((progid != "") ? progid : ""),
        progtype: ((progtype != "") ? progtype : ""),
        setpgs: "",
    },
        function (data, textStatus, jqXHR) {
            $(page).html(data.trim());
            settbylvls();
        }
    );
}

function settbylvls() {
    var tb = $('#ylvlstb');
    entries = $("#entriesylvls").val();
    progid = $('#progidylvls').val();
    page = $('#pageylvls').val();
    search = $('#searchylvls').val();
    progtype = $('#progtype').val();
    $(tb).html(loadingsm("Loading..."));
    $.post("yearlevelsfunc.php", {
        entries: ((entries != "") ? entries : ""),
        page: ((page != "") ? page : ""),
        search: ((search != "") ? search : ""),
        progid: ((progid != "") ? progid : ""),
        progtype: ((progtype != "") ? progtype : ""),
        settb: "",
    },
        function (data, textStatus, jqXHR) {
            $(tb).html(data.trim());
            settbsort();

        }
    );

}

$("button[name='prevnxtbtnylvls']").click(function (e) {
    e.preventDefault();
    var l = $('#pageylvls > option').length;
    var s = $('#pageylvls').prop('selectedIndex');
    if (s != l - 1 && $(this).hasClass("next")) {
        s++;
        $('#pageylvls').prop('selectedIndex', s);
        $("#pageylvls").trigger('change');
    } else if ((s <= l - 1 && s > 0) && $(this).hasClass("prev")) {
        s--;
        $('#pageylvls').prop('selectedIndex', s);
        $("#pageylvls").trigger('change');
    } else {
        reqfunc($('#pageylvls'));
    }
});


$('#searchylvls').keyup(function (e) {
    e.preventDefault();
    setpgsylvls();
});

$('#entriesylvls,select.setpgsylvls').change(function (e) {
    e.preventDefault();
    setpgsylvls();
});

$('#pageylvls').change(function (e) {
    e.preventDefault();
    settbylvls();
});