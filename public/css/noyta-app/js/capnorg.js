(function($) {
    "use strict"; // Start of use strict

   // Slider code

   if ($('.lagence-accord').length) {
	$('.lagence-accord').zAccordion({
			startingSlide: 1,
			auto: false,
			tabWidth: "20%",
			width: "100%",
			height: "auto",
			trigger: "click",
		    speed: 800
	});
	}
	$('.lagence-block').on('click', function(){
	       $('.lagence-block').removeClass('anime');
		   $(this).addClass('anime');
	});
   
   
   
    // jQuery for page scrolling feature - requires jQuery Easing plugin
    $('a.page-scroll').bind('click', function(event) {
        var $anchor = $(this);
		var top;
        $('html, body').stop().animate({
            scrollTop: ($($anchor.attr('href')).offset().top - 15)
        }, 1000, 'easeInOutExpo');
        event.preventDefault();
    });

    // Highlight the top nav as scrolling occurs
    $('body').scrollspy({
    target: '#mainNav',
    offset: 15
    });

    // Closes the Responsive Menu on Menu Item Click
    $('.navbar-collapse ul li a').click(function() {
        $('.navbar-toggle:visible').click();
    });

    
	
	var navbarCollapse = function() {
    if ($("header").offset().top > 15) {
	  $("body").addClass("active");
	  //$("header").addClass("active");
      $("#mainNav").addClass("affix");
      } else 
	  {
	  $("body").removeClass("active");
	  //$("header").removeClass("active");
      $("#mainNav").removeClass("affix");
    }
  };
  // Collapse now if page is not at top
  navbarCollapse();
  
 
  // Collapse the navbar when page is scrolled
  $(window).scroll(navbarCollapse);

})(jQuery); // End of use strict


// PLANS SLIDER
(function(){
    $('#plans-slide').carousel({ interval: 2000 });
  }());
  
  (function(){
    $('.carousel-showmanymoveone .item').each(function(){
      var itemToClone = $(this);
  
      for (var i=1;i<3;i++) {
        itemToClone = itemToClone.next();
  
        // wrap around if at end of item collection
        if (!itemToClone.length) {
          itemToClone = $(this).siblings(':first');
        }
  
        // grab item, clone, add marker class, add to collection
        itemToClone.children(':first-child').clone()
          .addClass("cloneditem-"+(i))
          .appendTo($(this));
      }
    });
  }());


