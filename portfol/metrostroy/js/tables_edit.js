function addRowToTable(table, cell1, cell2, cell3, cell4) {
    var row;
    row = "<tr><td><span>" + cell1 + "</span></td><td><span style='text-align: center'>" + cell2 +" "+ cell3 +"</span></td><td><span>" + cell4 + "</span></td><td><span>" + ('<a href="#" class="btn_edit"></a><a href="#" class="btn_del_zap"></a>') + "</span></td></tr>";
    table.append(row);
}
function addRowToTable2(table, cell1, cell2, cell3, cell4) {
    var row;
    row = "<tr><td><span style='text-align: left'>" + cell1 + "</span></td><td><span style='text-align: center'>" + cell2 +" "+ cell3 +"</span></td><td><span>" + ('<a href="#" class="btn_edit"></a><a href="#" class="btn_del_zap"></a>') + "</span></td></tr>";
    table.append(row);
}
function addRowToTable3(table, cell1, cell2, cell3) {
    var row;
    row = "<tr><td style='width: 174px'><span style='text-align: center'>" + cell1 + "</span></td><td style='width: 70px'><span style='text-align: left'>" + cell2 +" "+ cell3 +"</span></td><td style='width:60px'><span>" + ('<a href="#" class="btn_edit"></a><a href="#" class="btn_del_zap"></a>') + "</span></td></tr>";
    table.append(row);
}
$(document).ready(function() {

    //$("#table_add_plans .btn_del_zap").on("click",function() {
    //    var tr = $(this).closest('tr');
    //    tr.css("background-color","#FF3700");
    //
    //    tr.fadeOut(400, function(){
    //        tr.remove();
    //    });
    //    return false;
    //});
    $(document).on('click', 'a.btn_del_zap', function () {
        $(this).closest('tr').fadeOut(300, function(){ });
        return false;
    });
    var addClass = function(el, className) {
            if (el.classList) {
                el.classList.add(className);
            } else {
                el.className += ' ' + className;
            }
        },
        hasClass = function(el, className) {
            return el.classList ?
                el.classList.contains(className) :
                new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
        },
        removeClass = function(el, className) {
            if (el.classList) {
                el.classList.remove(className);
            } else {
                el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
            }
        },
        updateSelectPlaceholderClass = function(el) {
            var opt = el.options[el.selectedIndex];
            if(hasClass(opt, "placeholder")) {
                addClass(el, "placeholder");
            } else {
                removeClass(el, "placeholder");
            }
        },
        selectList = document.querySelectorAll("select");
//Simulate placeholder text for Select box
    for(var i = 0; i < selectList.length; i++) {
        var el = selectList[i];
        updateSelectPlaceholderClass(el);
        el.addEventListener("change", function() {
            updateSelectPlaceholderClass(this);
        });
    }

    //ссылки на таблицу
    $('tr[data-href]').on("click", function(e) {
        //console.log(e.target.tagName);
        //return false;
        if (e.target.tagName !== "SPAN") {
            document.location = $(this).data('href');
        }
    });
    $('.bl_diagramm .key_ico, .edit_bl_diagr .key_ico, .edit_bl_diagr .clear_btn,.edit_bl_diagr .close').click(function(){
        $(this).closest('.slide_bl_diagr').find('.edit_bl_diagr').fadeToggle('300');
    })
});
