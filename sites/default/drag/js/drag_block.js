(function($) {
	$(document).ready(function() {
		DragBlocks = {};
		DragBlocks.functions = {};
		$('#drag-block-container').draggable();
		//make the block draggable on hover
		DragBlocks.functions.hoverBlock = $('#drag-block-container').live('hover', function(event) {
			//make our block draggable
			$(this).children('.drag-block-container').draggable({
      	connectToSortable: ".region",
				helper: "clone",
				revert: "invalid"
			});
		});
		
		//make the regions droppable
		$(".region").droppable({
    // tolerance can be set to 'fit', 'intersect', 'pointer', or 'touch'
    tolerance: 'intersect',
    over: function(event, ui) {
			//add an active class to the region
    	$(this).addClass('block-drag-over');
    },
    out: function(event, ui) {
    	$(this).removeClass('block-drag-over');
    },
    deactivate: function(event, ui){
	    
    },
    // Add .dropClass whenever #draggable is dropped on #droppable
    drop: function(event, ui) {
        $(this).addClass('block-dropped');
        //append the draggable black to the region
        $(ui.draggable).appendTo($(this));
        //remove style's from the draggable object
        $(ui.draggable).removeAttr('style');
        //remove the block-drag-over class
        $(this).removeClass('block-drag-over');
        //block settings
        DragBlocks.blockData = {
	        bid: $(ui.draggable).attr('drag-bid'),
	        delta: $(ui.draggable).attr('drag-delta'),
	        region: $(this).children('.region-delta').attr('id'),
        };
        Drupal.settings.drag.blackWeight = DragBlocks.blockData;
        //console.log(DragBlocks.blockData);
    }	
		});
		
		//ajax request to update the block they dragged
		DragBlocks.functions.updateBlockSettings = function(blockData, weight){
			$.getJSON('drag/ajax/update/'+blockData.bid+'/'+blockData.region+'/'+ weight +'', function(data) {
				console.log(data);
			});
		}
	});
})(jQuery);