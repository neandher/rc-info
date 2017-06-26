(function($) {
 "use strict";

$(window).load(function(){
  $('.flexslider').flexslider({
    animation: "slide",
    animationLoop: false,
    //animationLoop: true,
    itemWidth: 1170,
    itemMargin: 5,
    pausePlay: true,
    start: function(slider){
      $('body').removeClass('loading');
    }
  });
});

})(jQuery);