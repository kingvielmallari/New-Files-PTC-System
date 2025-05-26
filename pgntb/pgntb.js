$("button.uptb").click(function (e) {
    var tn = $("#tbnamelist").val();

    var l = $('select.uptb.page[name="etb"] > option').length;
    var s = $('select.uptb.page[name="etb"]').prop('selectedIndex');

    if (s != l - 1 && $(this).hasClass("next")) {
        s++;
        $('select.uptb.page[name="trent"]').prop('selectedIndex', s);
        tb(tn, $("#trentsel").val(), $('select.uptb.page[name="trent"]').val(), $('input.uptb.search[name="trent"]').val());
    } else if ((s <= l - 1 && s > 0) && $(this).hasClass("prev")) {
        s--;
        $('select.uptb.page[name="etb"]').prop('selectedIndex', s);
        tb(tn, $('select.uptb.entries[name="etb"]').val(), $('select.uptb.page[name="etb"]').val(), $('input.uptb.search[name="etb"]').val());
    }
});

$("input.uptb").keyup(function (e) {
    var tn = "transactiontable";
    pages(tn, $("#trentsel").val(), $('select.uptb.page[name="trent"]'), $('input.uptb.search[name="trent"]').val());
    //pages(tn, $('select.uptb.entries[name="etb"]').val(), $('select.uptb.page[name="etb"]'), $('input.uptb.search[name="etb"]').val());
});

$("select.uptb").change(function (e) {
    var tn = "transactiontable";
    if ($(this).hasClass("page")) {
        tb(tn, $("#trentsel").val(), $(this).val(), $('input.uptb.search[name="trent"]').val());
    } else {
        pages(tn, $("#trentsel").val(), $('select.uptb.page[name="trent"]'), $('input.uptb.search[name="trent"]').val());
    }
});

$('#slcttransact').change(function (e) {
    var tn = "transactiontable";
    pages(tn, $("#trentsel").val(), $('select.uptb.page[name="trent"]'), $('input.uptb.search[name="trent"]').val());
    $.post("url", data,
        function (data, textStatus, jqXHR) {

        }
    );
});

function pages(tbname, entries, page, search) {
    $.post("../pgntb/pages.php",
        {
            pages: "",
            t: tbname,
            e: entries,
            s: search
        },
        function (data, textStatus, jqXHR) {
            $(page).html(data.trim());
            tb(tbname, entries, page.val(), search);
        }
    );

}
function tb(tbname, entries, page, search) {
    $.post("../pgntb/tbs.php",
        {
            tb: "",
            t: tbname,
            e: entries,
            p: page,
            s: search
        },
        function (data, textStatus, jqXHR) {
            $("#" + tbname + " > tbody").html(data.trim());
        }
    );

}

pages("transactiontable", $("#trentsel").val(), $('select.uptb.page[name="trent"]'), $('input.uptb.search[name="trent"]').val());



