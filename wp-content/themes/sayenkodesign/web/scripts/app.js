jQuery(function() {
    jQuery(document).foundation();
    jQuery('.slick').slick({});
    jQuery('.slick-4').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
    });
    jQuery('.warranty img').on('click', function(){
        console.log('click');
        jQuery(this).prev('.tooltip').toggleClass('hide');
    });
    jQuery('.warranty .tooltip').on('click', function(){
        jQuery(this).toggleClass('hide');
    });
});