(function($) {
	$(document).ready(function() {
		//sidebar animation
		var direction;
		//sidebar popout
		$('.drag-init-sidebar').click(function() {
			if (direction == "+=220") {
				direction = "-=220";
			} else {
				direction = "+=220";
			}
			$("#drag-module-sidebar-wrapper").animate({
				left: direction,
			}, "fast", function() {});
		});
		DragSideBar = {};
		DragSideBar.functions = {};
		//get lnght of object
		DragSideBar.functions.getObjLength = function(obj) {
			var size = 0,
				key;
			for (key in obj) {
				if (obj.hasOwnProperty(key)) size++;
			}
			return size;
		};
		//add attributes to the default blocks this theme has
		if (Drupal.settings.drag.currentBlockSettings != 'undefined') {
			DragSideBar.defaultBlockSettings = Drupal.settings.drag.currentBlockSettings;
			var obj_length = DragSideBar.functions.getObjLength(DragSideBar.defaultBlockSettings);
			for (var block_id in DragSideBar.defaultBlockSettings) {
				//get the block object
				var block = $('#' + block_id);
				//get the block settings
				var block_data = DragSideBar.defaultBlockSettings[block_id];
				//create our block wrapper
				var block_wrapper = '<div class="drag-block-container"';
				block_wrapper += ' drag-bid="' + block_data['drag-bid'] + '"';
				block_wrapper += ' drag-weight="' + block_data['drag-weight'] + '"';
				block_wrapper += ' drag-region="' + block_data['drag-region'] + '"';
				block_wrapper += ' drag-delta="' + block_data['drag-delta'] + '"';
				block_wrapper += ' id="' + block_data['drag-module'] + block_data['drag-delta'] + '">';
				//wrap the block with our custom wrapper
				block.wrapAll(block_wrapper);
			}
		}
		//displaying the sidebar
		$('.drag-list-title').click(function() {
			$('.region').addClass('drag');
			//the block id
			DragSideBar.block_id = '';
			//the block sorting order
			DragSideBar.block_order = '';
			//make our blocks sortable
			$(".region").sortable({
				tolerance: 'pointer',
				//only allow block items to be sortable
				items: '.drag-block-container',
				//connect the draggable ement with ethe region
				connectWith: ".region",
				//the placeholder that the draggabkel object can be dropped onto
				placeholder: "drag-region-placeholder",
				//set the wieght to 0 when they change sortable regions
				out: function(event, ui) {
					//$(ui.item).attr('drag-weight', 0);
				},
				//update function for when when they drop the draggablke element
				update: function(event, ui) {
					DragSideBar.functions.updateBlockWeight(this, ui);
				},
				//when they start a to drag a sortable element
				start: function(event, ui) {
					DragSideBar.block_id = $(ui.item).attr('id');
				}
			});
			if ($('.drag-module-list').hasClass('drag-list-active')) {
				$('.drag-module-list').removeClass('drag-list-active');
				$('.drag-block-region').removeClass('active');
			} else {
				$('.drag-module-list').addClass('drag-list-active');
				$('.drag-block-region').addClass('active')
			}
		});
		//update all the vlock weights
		DragSideBar.functions.updateBlockInRegion = function(blocks) {
			//loop thourgh the blocks
			var placeholder = 0;
			for (var id in blocks) {
				DragSideBar.BlockAttributes = {
					bid: $('#' + id).attr('drag-bid'),
					delta: $('#' + id).attr('drag-delta'),
					region: $('#' + id).attr('drag-region'),
					weight: $('#' + id).attr('drag-weight'),
				};
				//we dont get the updated region on the target block being dragged soon enough so we will get it here
				switch (id) {
				case DragSideBar.block_id:
					DragSideBar.BlockAttributes.region = Drupal.settings.drag.blockSettings.region;
					break
				default:
					break;
				}
				//switch the position number
				switch (placeholder) {
				case 0:
					DragSideBar.BlockAttributes.weight = -9;
					break;
				default:
					DragSideBar.BlockAttributes.weight = -9 + placeholder;
					break;
				}
				DragBlocks.functions.updateBlockSettings(DragSideBar.BlockAttributes, DragSideBar.BlockAttributes.weight);
				placeholder++;
			}
		}
		//function to update our block weight
		DragSideBar.functions.updateBlockWeight = function(element, ui) {
			DragSideBar.block_order = $(element).sortable('toArray');
			//find what position our block id is
			//array to store the block order and weight
			var blockOrder = [];
			var block_order_length = DragSideBar.block_order.length;
			for (var i = 0; i < block_order_length; i++) {
				//only if the id is not empty
				if (DragSideBar.block_order[i] != '') {
					var weight = $("#" + DragSideBar.block_order[i]).attr("drag-weight");
					if (weight !== undefined) {
						blockOrder[DragSideBar.block_order[i]] = weight;
					}
				}
			}
			//function to update the other blocks in that region's weights
			DragSideBar.functions.updateBlockInRegion(blockOrder);
		}
	});
})(jQuery);