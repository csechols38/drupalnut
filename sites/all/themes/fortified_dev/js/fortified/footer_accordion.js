;(function($){
'use strict';
	
	$(document).ready(function(){
		var FooterAccorion = {};
		FooterAccorion.footerLinkDisplay = $('.footer-children-wrapper').css('display');
		if(FooterAccorion.footerLinkDisplay == 'none'){
		FooterAccorion.accordionChildren = $('.footer-children-wrapper');
			FooterAccorion.openAccordion = $('.footer-link-title').click(function(){
				FooterAccorion.sameAccordion = false;
				var accordionId = $(this).attr('data-accordion');
				FooterAccorion.defaultTriggerElement = $('.footer-link-title');
				if( FooterAccorion.defaultTriggerElement.hasClass('active') ){
					 FooterAccorion.accordionChildren.slideUp('slow');
					 FooterAccorion.defaultTriggerElement.removeClass('active');
				 }
				 if(FooterAccorion.accordionId != '' && FooterAccorion.accordionId == accordionId){
					 FooterAccorion.sameAccordion = true;
				 }
				 FooterAccorion.accordionId = $(this).attr('data-accordion');
				 console.log(accordionId);
				 console.log(FooterAccorion.accordionId);
				 if(!FooterAccorion.sameAccordion){
					 FooterAccorion.linkContainer = $(this).parent('.footer-links-wrapper').find('.footer-children-wrapper');
					 if(FooterAccorion.linkContainer){
					 	 $(this).addClass('active');
						 FooterAccorion.linkContainer.slideDown('slow');
					 }
					}else{
						FooterAccorion.accordionId = '';
					}
			});
		}
	});
	
})(jQuery);