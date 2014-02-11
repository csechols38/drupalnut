(function($) {
    $(document).ready(function() {
        //For activating background colors on main menu dropdown
        $('.menu-wrapper li').on('click', 'a', function(event) {
            var items = $('.menu-wrapper li a').map(function() {
                if ($(this).hasClass('active')) {
                    $(this).removeClass("active");
                }
            }).get();
            $(this).addClass('active');
        }); // end activation
        
        
        
        //menu bar search expand
         $('.menu-wrapper li.last').click(function(){
          $('.form-item-search-block-form').animate({minWidth:'180px'}, 'fast');
         // $(this).find('.search-block-input').attr('size', 250);
          $(this).find('.form-submit').addClass('active');
          $(this).focus();
          
          $(this).off('focus').focusout(function(){
          $('.form-item-search-block-form').animate({minWidth:'90px'}, 'slow');
          $(this).find('.form-submit').removeClass('active');
        });
        });
        
        
        
        
        
        //swip menu 
        var maxSlideMenuWidth = 0;
        //swip menu
        var windowWidth = window.innerWidth;
        windowWidth = windowWidth * 1;
        if (windowWidth <= 1315) {
          SlideMenu();
        }
        
        function getSlideItemWidths(){
          var item_widths = $('.menu-wrapper li').map(function() {
                var slideItemPxWidth = $(this).css('width');
                maxSlideMenuWidth = (parseInt(slideItemPxWidth, 10)) + (maxSlideMenuWidth);
            }).get();
            var slidePadding = maxSlideMenuWidth - windowWidth;
            maxSlideMenuWidth = maxSlideMenuWidth - slidePadding;
            return maxSlideMenuWidth;
        }
        
        function SlideMenu(){
            var slideItemRight = 0;
            var slideItemLeft = 0;
            var moveDistance;
            maxSlideMenuWidth = getSlideItemWidths();
            
            $('.middle-head-menu').wipetouch({
                // settings here
                wipeLeft: function(result) {
                    if (maxSlideMenuWidth >= slideItemRight) {
                        slideItemRight = slideItemRight + result.x;
                        moveDistance = slideItemRight;
                        $('.menu-wrapper').animate({
                            left: '-' + moveDistance + 'px'
                        }, 'fast');
                    }else{
                      maxSlideMenuWidth = getSlideItemWidths();
                    }
                    console.log(slideItemRight);
                },
                wipeRight: function(result) {
                    slideItemLeft = slideItemRight - result.x;
                    slideItemRight = slideItemLeft;
                    if (slideItemLeft <= 0) {
                        slideItemLeft = 0;
                        slideItemRight = 0;
                    }
                    moveDistance = slideItemLeft;
                    $('.menu-wrapper').animate({
                        left: '-' + moveDistance + 'px'
                    }, 'fast');
                    //console.log(slideItemLeft);
                },
            });
        }
    });
})(jQuery);