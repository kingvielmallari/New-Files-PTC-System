function removethis(tr) {
  if (confirm("Are you sure to remove this?")) {
    $(tr).closest('tbody').find('tr:last').prev().before(`<tr>
        <td style="height:25px;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>`);
    $(tr).remove();
  }
}

async function addhistory() {
  var fd = new FormData();
  fd.append('stdids', $('#stdid').val());
  fd.append('secids', $('#secids').val());
  fd.append('datas', $('#datas').val());
  fd.append('printedcor', '');
  var res = await myajax('functionsec.php', fd);

}
