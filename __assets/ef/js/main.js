$('#main-template').html('');
$.post(host+'loading-'+cont+'/'+cont2, {}, function(respons){
	var parsing = $.parseJSON(respons);
	$('#main-template').html(parsing.page);
});

function kumpulAction(type, p1, p2, p3){
	switch(type){
		case "krj":
			$('.modal-dialog').css({'width':'500px'});
			$('#modalencuk').html('');
			$.post(host+'keranjang-belanja', { }, function(resp){
				parsingan = $.parseJSON(resp)
				$('#modalencuk').html(parsingan.page);
				$('#productModal').modal('show'); 
			})
		break;
	}
}

(function($) {
    "use strict";

    /*----------------------------
     	jQuery MeanMenu
    ------------------------------ */
    jQuery('nav#dropdown').meanmenu();


    /*----------------------------
    	tab-carosual-list
     ------------------------------ */
    $('.tab-carosual-list').bxSlider({
        slideWidth: 100,
        minSlides: 2,
        maxSlides: 3,
        slideMargin: 10,
        controls: true,
        prevText: ["<i class='fa fa-angle-left'></i>"],
        nextText: ["<i class='fa fa-angle-right'></i>"],
        pager: false,
    });



    /*------------------------------
     price-slider active
    ------------------------------ */
    $("#slider-range").slider({
        range: true,
        min: 40,
        max: 600,
        values: [60, 570],
        slide: function(event, ui) {
            $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
        }
    });
    $("#amount").val("$" + $("#slider-range").slider("values", 0) +
        " - $" + $("#slider-range").slider("values", 1));

    /*--------------------------
     scrollUp
    ---------------------------- */
    $.scrollUp({
        scrollText: '<i class="fa fa-angle-up"></i>',
        easingType: 'linear',
        scrollSpeed: 900,
        animation: 'fade'
    });

    /*--------------------------
     counter
    ---------------------------- */
    $('.counter').counterUp({
        delay: 10,
        time: 2000
    });

    /*--------------------------
     counter
    ---------------------------- */
    $('.masonry-active').masonry({
        // options
        itemSelector: '.col-md-4',
        columnWidth: 1
    });
    /*--------------------------
     sliphover
    ---------------------------- */
    $(".blog-flickr").sliphover();

    /*--------------------------
     Select Option
    ---------------------------- */
    $('.selectpicker').selectpicker({
        style: 'btn-info',
        size: 4
    });
	
    /*---------------------
    	Category menu
    --------------------- 
    $('#categorymenu li.active').addClass('open').children('ul').show();
    $('#categorymenu li.has-sub>a').on('click', function() {
        $(this).removeAttr('href');
        var element = $(this).parent('li');
        if (element.hasClass('open')) {
            element.removeClass('open');
            element.find('li').removeClass('open');
            element.find('ul').slideUp(400);
        } else {
            element.addClass('open');
            element.children('ul').slideDown(400);
            element.siblings('li').children('ul').slideUp(400);
            element.siblings('li').removeClass('open');
            element.siblings('li').find('li').removeClass('open');
            element.siblings('li').find('ul').slideUp(400);
        }
    });
	*/
    // coming soon template
    $('[data-countdown]').each(function() {
        var $this = $(this),
            finalDate = $(this).data('countdown');
        $this.countdown(finalDate, function(event) {
            $this.html(event.strftime('<span class="estut-count days"><span class="time-count">%-D</span> <p>Days</p></span> <span class="estut-count hour"><span class="time-count">%-H</span> <p>Hours</p></span> <span class="estut-count minutes"><span class="time-count">%M</span> <p>Minutes</p></span> <span class="estut-count second"> <span><span class="time-count">%S</span> <p>Seconds</p></span>'));
        });
    });

    /*---------------------
    	Mobile menu
    --------------------- */
    $('nav#mobile-menu').meanmenu();
})(jQuery);