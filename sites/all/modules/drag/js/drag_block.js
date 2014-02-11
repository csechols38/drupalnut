(function($) {
	$(document).ready(function() {
		//object to store various data
		DragBlocks = {};
		//object for runctions
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
			tolerance: 'pointer',
			over: function(event, ui) {
				//add an active class to the region
				$(this).addClass('block-drag-over');
			},
			//remove the draged-over class
			out: function(event, ui) {
				$(this).removeClass('block-drag-over');
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
					region: $(this).children('.region-delta').attr('drag-region-id'),
				};
				//update the blocks region
				$(ui.draggable).attr('drag-region', DragBlocks.blockData.region);
				//stor the blocks new region
				Drupal.settings.drag.blockSettings = DragBlocks.blockData;
			}
		});
		//ajax request to update the block they dragged
		DragBlocks.functions.updateBlockSettings = function(blockData, weight) {
			//ajax call
			$.getJSON('../../drag/ajax/update/' + blockData.bid + '/' + blockData.region + '/' + weight + '', function(data) {
			});
		}
	});
})(jQuery);