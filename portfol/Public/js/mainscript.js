$(document).ready(function(){
    $('.calendar_data input, .select_date_bl input').on('input',function(){
        var
            selBegin = this.selectionStart,
            val = this.value;


        if(
            selBegin == this.selectionEnd &&
            selBegin != val.length
        ){
            this.value = val.substr(0, selBegin) + val.slice(selBegin + 1);
            this.selectionStart = this.selectionEnd = selBegin;
        }
    });

    $('.calendar_data input, .select_date_bl input').on('focus',function(){
        this.selectionStart = this.selectionEnd = 3;
    });
});