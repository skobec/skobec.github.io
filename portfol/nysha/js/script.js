// ===== Scroll to Top ====

$('#return-to-top').click(function() {      // When arrow is clicked
    $('body,html').animate({
        scrollTop : 1000                       // Scroll to top of body
    }, 500);
});
