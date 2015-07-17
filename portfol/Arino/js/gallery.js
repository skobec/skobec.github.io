$('#thumbs').delegate('img','click', function(){
    $('#largeImage').attr('src',$(this).attr('src').replace('thumb','large'));
    $('#description').html($(this).attr('alt'));
});/**
 * Created by skobe_000 on 15.01.2015.
 */
