function addRowToTable(table, cell1, cell2, cell3, cell4) {
    var row;
    row = "<tr><td><span>" + cell1 + "</span></td><td><span style='text-align: center'>" + cell2 +" "+ cell3 +"</span></td><td><span>" + cell4 + "</span></td><td><span>" + ('<a href="#" class="btn_edit"></a><a href="#" class="btn_del_zap"></a>') + "</span></td></tr>";
    table.append(row);
}

$(document).ready(function() {
    $('.add_btn').click(function() {
        addRowToTable($('#table_add_plans'), $('#t1').val(), $('#t2').val(),$('#t3').val(), $('#t4').val());
    });
});