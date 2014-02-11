(function($){
	$(document).ready(function(){
		DragSideBar = {};
		DragSideBar.functions = {};
		//displaying the sidebar
		$('.drag-list-title').click(function(){
			DragSideBar.block_id = '';
			DragSideBar.block_order = '';
			//make our blocks sortable
			$(".region").sortable({
	      connectWith: ".region",
	      placeholder: "drag-region-placeholder",
	      sort: function(event, ui){
		      
	      },
	      out: function(event, ui){ 
		      $(ui.item).attr('drag-weight', 0);
	      },
	      over: function(event, ui){ 
		      
	      },
	      update: function (event, ui) {
	      	DragSideBar.functions.updateBlockWeight(this, ui);
				},
				start: function (event, ui) {
					DragSideBar.block_id = $(ui.item).attr('id');
				}
	    });
			if($('.drag-module-list').hasClass('drag-list-active')){
				$('.drag-module-list').removeClass('drag-list-active');
				$('.drag-block-region').removeClass('active');
			}else{
				$('.drag-module-list').addClass('drag-list-active');
				$('.drag-block-region').addClass('active')
			}
		});
		
	//update our block weight
	DragSideBar.functions.updateBlockWeight = function(element, ui){	
		DragSideBar.block_order = $(element).sortable('toArray');
			console.log(DragSideBar.block_order);
			console.log(DragSideBar.block_id);
        	//find what position our block id is
					for(var i = 0; i < DragSideBar.block_order.length; i++){
						switch(DragSideBar.block_order[i]){
							case DragSideBar.block_id:
								$(ui.item).attr('drag-weight', i);
								console.log(i);
								//send our block values se we can update the block inside the database
								DragBlocks.functions.updateBlockSettings(Drupal.settings.drag.blackWeight, i);
							break;
						}
					}
				}
	});
})(jQuery);