<?php



//class containing misc function used in drag.module
class DragLayout{

	public $theme_key;
	public $regions;
	public $block_region_list;
	public $block_list;

	//get the themekey
	public function getThemeKey(){
		global $theme_key;
		$this->theme_key = $theme_key;
		$admin_theme = variable_get('admin_theme', '');
		if($theme_key == $admin_theme){
			return FALSE;
		}else{
			return TRUE;
		}
	}

	//get our regions
	public function getRegions($theme_key = NULL){
		$theme_key = !empty($theme_key) ? $theme_key : $this->theme_key;
		if(isset($theme_key)){
			$this->regions = system_region_list($theme_key);
		}
	}


	//get a list of blocks and regions
	public function getBlockRegionList($regions = NULL){
		$blocks = array();
		$regions = !empty($regions) ? $regions : $this->regions;
		if(isset($regions)){
			foreach($regions as $machine_name => $block_title) {
				$blocks += block_list($machine_name);
			}
		}
		$this->block_region_list = $blocks;
	}


	//get a list of all the blocks
	public function getBLockList($theme = NULL, $exclude = FALSE){
		//theme key
		$theme = !empty($theme) ? $theme : $this->theme_key;
		$blocks = new StdClass();
		$blocks->blocks = array();
		//query all blocks in the database
		$query = db_select('block', 'b');
		//the current theme
		$query->condition('b.theme', $theme, '=');
		//if we want to exclude all the blocks current being displayed on the page
		if($exclude){
			$deltas = array();
			//loop through the regions and get all the block's deltas that are being displayed on the current page
			foreach($this->block_region_list as $delta => $val){
				$deltas[] = $val->delta;
			}
			//condition to only grab blocks not being rendered on the page
			$query->condition('b.delta', $deltas, 'NOT IN');
		}
		//block fields to return
		$query->fields('b', array('bid', 'module', 'delta', 'theme', 'status', 'weight', 'region', 'custom', 'visibility', 'pages', 'title', 'cache'));
		//execute
		$result = $query->execute();
		//loop through the returned blocks
		foreach($result as $block){
			if(empty($block->title)){
				$block->title = $block->module .'-'. $block->delta;
			}
			$blocks->blocks[] = $block;
		}
		//insert blocks into our object array
		$this->blocks_list = $blocks;
	}

	//function to format our block list
	public static function formatBlockList($blocks){
		$items = array();
		if(!empty($blocks->blocks)){
			//loop through our blocks
			foreach($blocks->blocks as $delta => $block){
				//make sure the block is not already being displayed on the page
				//if(!in_array($blocks[$delta]->region, ))
				foreach($block as $key => $value){

					if(!empty($block->title)){
						$title = $block->title;
					}else{
						$title = $block->module .'-'. $block->delta;
					}
					switch($key){
					case 'delta':
						if(isset($value)){
							$items[$delta]['data'] = l($title, 'drag/ajax/block/nojs/'. $value .'/'. $block->module .'/'. $block->bid .'/'. $block->weight .'', array('attributes' => array('class' => array('use-ajax'))));
						}
						break;
					case 'weight':
						if(isset($value)){
							$items[$delta]['drag-weight'] = $value;
						}
						break;
					case 'region':
						if(isset($value)){
							$items[$delta]['drag-region'] = $value;
						}
						break;
					case 'bid':
						if(isset($value)){
							$items[$delta]['drag-bid'] = $value;
						}
						break;
						$items[$delta]['class'] = array('drag-li-block');
					}
				}
			}
		}
		return $items;
	}


	//update our block settings in the database
	public static function updateBlockSettings($bid, $region, $weight){
		$update = db_update('block')
		->fields(array(
				//'module' => 'system',
				//'delta' => 'main-menu', // block delta, find in database or module that defines it
				//'theme' => 'mytheme', // theme to configure
				'region' => $region, // region declared in  theme
				'status' => 1,
				//'pages' => '',
				'weight' => $weight,
			)
		)
		->condition('bid', $bid, '=')
		//execute the update
		->execute();
		if($update){
		}else{
			return FALSE;
		}
	}
	
	//give the blocks being rendered on the page attributes so we know the weights and other settings
	public function buildDefaultBlockSettings($blocks = NULL){
		$blocks = !empty($blocks) ? $blocks : $this->block_region_list;
		$js_block_settings = array();
		foreach($blocks as $block_delta => $block_data){
			$js_block_name = explode('_', $block_delta);
			$js_block_name = implode('-', $js_block_name);
			$js_block_id = 'block-'. $js_block_name;
			$js_block_settings[$js_block_id] = array(
				'drag-bid' => $block_data->bid,
				'drag-weight' => $block_data->weight,
				'drag-region' => $block_data->region,
				'drag-delta' => $block_data->delta,
				'drag-module' => $block_data->module,
			);
		}
		drupal_add_js(array('drag' => array('currentBlockSettings' => $js_block_settings)), 'setting');
	}
}