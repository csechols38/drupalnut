<?php
/*
 * @file
 */

$files = array(
	'elements.inc',
	'form.inc',
	'menu.inc',
	'theme.inc',
);

function _fortified_dev_load($files) {
	$tp = drupal_get_path('theme', 'fortified_dev');
	$file = '';
	$dir = dirname(__FILE__);

	// Check file path and '.inc' extension
	foreach($files as $file) {
		$file_path = $dir . '/inc/' . $file;
		if ( strpos($file,'.inc') > 0 && file_exists($file_path)) {
			require_once($file_path);
		}
	}
}

_fortified_dev_load($files);

/**
 * Implements hook_html_head_alter().
 */
function fortified_dev_html_head_alter(&$head_elements) {
	//default meta tags	
	// HTML5 charset declaration.
	$head_elements['system_meta_content_type']['#attributes'] = array(
		'charset' => 'utf-8',
	);

	// Optimize mobile viewport.
	$head_elements['mobile_viewport'] = array(
		'#type' => 'html_tag',
		'#tag' => 'meta',
		'#attributes' => array(
			'name' => 'viewport',
			'content' => 'width=device-width',
		),
	);

	// Force IE to use Chrome Frame if installed.
	$head_elements['chrome_frame'] = array(
		'#type' => 'html_tag',
		'#tag' => 'meta',
		'#attributes' => array(
			'content' => 'ie=edge, chrome=1',
			'http-equiv' => 'x-ua-compatible',
		),
	);

	// Remove image toolbar in IE.
	$head_elements['ie_image_toolbar'] = array(
		'#type' => 'html_tag',
		'#tag' => 'meta',
		'#attributes' => array(
			'http-equiv' => 'ImageToolbar',
			'content' => 'false',
		),
	);
	
 }







/**
 * Implements theme_breadrumb().
 *
 * Print breadcrumbs as a list, with separators.
 */
function fortified_dev_breadcrumb($variables) {
	$breadcrumb = $variables['breadcrumb'];

	if (!empty($breadcrumb)) {
		// Provide a navigational heading to give context for breadcrumb links to
		// screen-reader users. Make the heading invisible with .element-invisible.
		$breadcrumbs = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

		$breadcrumbs .= '<ul class="breadcrumbs">';
		$args = arg();
		foreach ($breadcrumb as $key => $value) {
			if(!empty($args[1]) && !empty($breadcrumb[2])){
				switch($key){
				case'0':
				case'1':
					$breadcrumbs .= '<li>' . $value . '</li>';
					break;
				}
			}else{
				$breadcrumbs .= '<li>' . $value . '</li>';
			}
		}
		
		if(!empty($args[1]) && !empty($breadcrumb[2])){
			$fix = $breadcrumb[2];
			unset($breadcrumb[2]);
			$title = strip_tags($fix);
			$breadcrumbs .= '<li class="current"><a href="#">' . $title. '</a></li>';
			$breadcrumbs .= '</ul>';
		}else{
			$title = strip_tags(drupal_get_title());
			$breadcrumbs .= '<li class="current"><a href="#">' . $title. '</a></li>';
			$breadcrumbs .= '</ul>';
		}

		return $breadcrumbs;
	}
}


function fortified_dev_menu_breadcrumb_alter(&$active_trail, $item){
	if(isset($_GET['page'])){
		$path = current_path();
		$path_alias = drupal_lookup_path('alias', $path);
		$page = $_GET['page'];
		$number = !empty($_GET['delta']) ? ($_GET['delta']) : 0;
		$options = array(
			'query' => array(
				'page' => $page,
				'delta' => $number,
			),
		);
		$active_trail[] = array(
			'title' => $page,
			'href' => '',
			'localized_options' => array(),
		);

	}
	//$active_trail =  drupal_set_breadcrumb($active_trail);
	return $active_trail;

}

function fortified_dev_field($variables) {
	$output = '';

	// Render the label, if it's not hidden.
	if (!$variables['label_hidden']) {
		$output .= '<div ' . $variables['title_attributes'] . '>' . $variables['label'] . ':&nbsp;</div>';
	}

	foreach ($variables['items'] as $delta => $item) {
		$output .= drupal_render($item);
	}

	// Render the top-level DIV.
	$output = '<div class="' . $variables['classes'] . '"' . $variables['attributes'] . '>' . $output . '</div>';

	return $output;
}

/**
 * Implements theme_field__field_type().
 */
function fortified_dev_field__taxonomy_term_reference($variables) {
	$output = '';

	// Render the label, if it's not hidden.
	if (!$variables['label_hidden']) {
		$output .= '<h2 class="field-label">' . $variables['label'] . ': </h2>';
	}

	// Render the items.
	$output .= ($variables['element']['#label_display'] == 'inline') ? '<ul class="links inline">' : '<ul class="links">';
	foreach ($variables['items'] as $delta => $item) {
		$output .= '<li class="taxonomy-term-reference-' . $delta . '"' . $variables['item_attributes'][$delta] . '>' . drupal_render($item) . '</li>';
	}
	$output .= '</ul>';

	// Render the top-level DIV.
	$output = '<div class="' . $variables['classes'] . (!in_array('clearfix', $variables['classes_array']) ? ' clearfix' : '') . '">' . $output . '</div>';

	return $output;
}


/**
 * Implements theme_links() targeting the main menu specifically
 * Outputs Foundation Top Bar http://foundation.zurb.com/docs/navigation.php
 */
function fortified_dev_links__system_main_menu($variables) {
	// Get all the main menu links
	$name = $variables['name'];
	$machine = $variables['machine'];
	$menu_links = menu_tree_output(menu_tree_all_data(variable_get(''.$name.'_links_source', $machine)));
	//$main_menu_links = menu_tree_output(menu_tree_all_data(variable_get('main_menu_links_source', 'main-menu')));
	// Initialize some variables to prevent errors
		//dpm($menu_links);
	
	$output = '';
	$sub_menu = '';
	$small_link = '';

	foreach ($menu_links as $key => $link) {
		// Add special class needed for Foundation dropdown menu to work
		$small_link = $link; //duplicate version that won't get the dropdown class, save for later
		!empty($link['#below']) ? $link['#attributes']['class'][] = 'has-dropdown' : '';

		// Render top level and make sure we have an actual link
		if (!empty($link['#href'])) {

			$output .= '<li' . drupal_attributes($link['#attributes']) . '>' . l($link['#title'], $link['#href']);
			// Uncomment if we don't want to repeat the links under the dropdown for large-screen
			// $small_link['#attributes']['class'][] = 'show-for-small';
			$sub_menu = '<li' . drupal_attributes($small_link['#attributes']) . '>' . l($link['#title'], $link['#href']);
			// Get sub navigation links if they exist
			foreach ($link['#below'] as $key => $sub_link) {
				if (!empty($sub_link['#href'])) {
					$sub_menu .= '<li>' . l($sub_link['#title'], $sub_link['#href']) . '</li>';
				}
			}
			$output .= !empty($link['#below']) ? '<ul class="dropdown">' . $sub_menu . '</ul>' : '';

			// Reset dropdown to prevent duplicates
			unset($sub_menu);
			unset($small_link);
			$small_link = '';
			$sub_menu = '';

			$output .=  '</li>';
		}
	}
	return '<ul class="right '.$machine.'">' . $output . '</ul>';
}
// end Top Bar */


/**
 * Implements hook_preprocess_block()
 */
function fortified_dev_preprocess_block(&$variables) {
	// Convenience variable for block headers.
	$title_class = &$variables['title_attributes_array']['class'];

	// Generic block header class.
	$title_class[] = 'block-title';

	// In the header region visually hide block titles.
	if ($variables['block']->region == 'header') {
		$title_class[] = 'element-invisible';
	}

	// Add a unique class for each block for styling.
	$variables['classes_array'][] = $variables['block_html_id'];

	// Add classes based on region.
	switch ($variables['elements']['#block']->region) {
		// Add a striping class
	case 'header':
		$variables['classes_array'][] = 'header';
		break;

	default;
	}
}
/**
 * Implements template_preprocess_field().
 */
function fortified_dev_preprocess_field(&$variables) {
	$variables['title_attributes_array']['class'][] = 'field-label';

	// Edit classes for taxonomy term reference fields.
	if ($variables['field_type_css'] == 'taxonomy-term-reference') {
		$variables['content_attributes_array']['class'][] = 'comma-separated';
	}

	// Convinence variables
	$name = $variables['element']['#field_name'];
	$bundle = $variables['element']['#bundle'];
	$mode = $variables['element']['#view_mode'];
	$classes = &$variables['classes_array'];
	$title_classes = &$variables['title_attributes_array']['class'];
	$content_classes = &$variables['content_attributes_array']['class'];
	$item_classes = array();

	// Global field classes
	$classes[] = 'field-wrapper';
	$content_classes[] = 'field-items';
	$item_classes[] = 'field-item';

	// Uncomment the lines below to see variables you can use to target a field
	// print '<strong>Name:</strong> ' . $name . '<br/>';
	// print '<strong>Bundle:</strong> ' . $bundle  . '<br/>';
	// print '<strong>Mode:</strong> ' . $mode .'<br/>';

	// Add specific classes to targeted fields
	if(isset($field)) {
		switch ($mode) {
			// All teasers
		case 'teaser':
			switch ($field) {
				// Teaser read more links
			case 'node_link':
				$item_classes[] = 'more-link';
				break;
				// Teaser descriptions
			case 'body':
			case 'field_description':
				$item_classes[] = 'description';
				break;
			}
			break;
		}
	}
	// Check if exists
	//  switch ($field) {
	//    case 'field_authors':
	//      $title_classes[] = 'inline';
	//      $content_classes[] = 'authors';
	//      $item_classes[] = 'author';
	//      break;
	//  }

	// Apply odd or even classes along with our custom classes to each item
	foreach ($variables['items'] as $delta => $item) {
		$item_classes[] = $delta % 2 ? 'odd' : 'even';
		$variables['item_attributes_array'][$delta]['class'] = $item_classes;
	}

	// Add class to a specific fields across content types.
	switch ($variables['element']['#field_name']) {
	case 'body':
		$variables['classes_array'] = array('body');
		break;

	case 'field_summary':
		$variables['classes_array'][] = 'text-teaser';
		break;

	case 'field_link':
	case 'field_date':
		// Replace classes entirely, instead of adding extra.
		$variables['classes_array'] = array('text-content');
		break;

	case 'field_image':
		// Replace classes entirely, instead of adding extra.
		$variables['classes_array'] = array('image');
		break;

	default:
		break;
	}
	// Add classes to body based on content type and view mode.
	if ($variables['element']['#field_name'] == 'body') {

		// Add classes to Foobar content type.
		if ($variables['element']['#bundle'] == 'foobar') {
			$variables['classes_array'][] = 'text-secondary';
		}

		// Add classes to other content types with view mode 'teaser';
		elseif ($variables['element']['#view_mode'] == 'teaser') {
			$variables['classes_array'][] = 'text-secondary';
		}

		// The rest is text-content.
		else {
			$variables['classes_array'][] = 'field';
		}
	}
}
/**
 * Implements template_preprocess_html().
 *
 * Adds additional classes
 */
function fortified_dev_preprocess_html(&$variables) {
	global $language;

	// Clean up the lang attributes
	$variables['html_attributes'] = 'lang="' . $language->language . '" dir="' . $language->dir . '"';

	// Add language body class.
	if (function_exists('locale')) {
		$variables['classes_array'][] = 'lang-' . $variables['language']->language;
	}

	//  @TODO Custom fonts from Google web-fonts
	//  $font = str_replace(' ', '+', theme_get_setting('fortified_dev_font'));
	//  if (theme_get_setting('fortified_dev_font')) {
	//    drupal_add_css('http://fonts.googleapis.com/css?family=' . $font , array('type' => 'external', 'group' => CSS_THEME));
	//  }
	if($variables['is_front']){
		variable_set('page_desc', '');
		Variable_set('page_title', '');
	}

	// Classes for body element. Allows advanced theming based on context
	if (!$variables['is_front']) {
		// Add unique class for each page.
		$path = drupal_get_path_alias($_GET['q']);
		// Add unique class for each website section.
		list($section, ) = explode('/', $path, 2);
		$arg = explode('/', $_GET['q']);
		if ($arg[0] == 'node' && isset($arg[1])) {
			if ($arg[1] == 'add') {
				$section = 'node-add';
			}
			elseif (isset($arg[2]) && is_numeric($arg[1]) && ($arg[2] == 'edit' || $arg[2] == 'delete')) {
				$section = 'node-' . $arg[2];
			}
		}
		$variables['classes_array'][] = drupal_html_class('section-' . $section);
	}

	// Store the menu item since it has some useful information.
	$variables['menu_item'] = menu_get_item();
	if ($variables['menu_item']) {
		switch ($variables['menu_item']['page_callback']) {
		case 'views_page':
			$variables['classes_array'][] = 'views-page';
			break;
		case 'page_manager_page_execute':
		case 'page_manager_node_view':
		case 'page_manager_contact_site':
			$variables['classes_array'][] = 'panels-page';
			break;
		}
	}
	/*
   * Zepto Fallback
   *   Use with caution
   */
	// drupal_add_js('document.write(\'<script src=/' . drupal_get_path('theme', 'fortified_dev') .'/js/vendor/\'
	//       + (\'__proto__\' in {} ? \'zepto\' : \'jquery\')
	//       + \'.js><\/script>\');',
	//       'inline', array('group',JS_LIBRARY));
}

/**
 * Implements template_preprocess_node
 *
 * Add template suggestions and classes
 */
function fortified_dev_preprocess_node(&$variables) {

	// Add node--node_type--view_mode.tpl.php suggestions
	$variables['theme_hook_suggestions'][] = 'node__' . $variables['type'] . '__' . $variables['view_mode'];

	// Add node--view_mode.tpl.php suggestions
	$variables['theme_hook_suggestions'][] = 'node__' . $variables['view_mode'];

	// Add a class for the view mode.
	if (!$variables['teaser']) {
		$variables['classes_array'][] = 'view-mode-' . $variables['view_mode'];
	}

	$variables['title_attributes_array']['class'][] = 'node-title';

	//  // Add classes based on node type.
	//  switch ($variables['type']) {
	//    case 'news':
	//    case 'pages':
	//      $variables['attributes_array']['class'][] = 'content-wrapper';
	//      $variables['attributes_array']['class'][] = 'text-content';
	//      break;
	//  }
	//
	//  // Add classes & theme hook suggestions based on view mode.
	//  switch ($variables['view_mode']) {
	//    case 'block_display':
	//      $variables['theme_hook_suggestions'][] = 'node__aside';
	//      $variables['title_attributes_array']['class'] = array('title-block');
	//      $variables['attributes_array']['class'][] = 'block-content';
	//      break;
	//  }
}

/**
 * Implements template_preprocess_page
 *
 * Add convenience variables and template suggestions
 */
function fortified_dev_preprocess_page(&$variables) {
	// Add page--node_type.tpl.php suggestions
	
	if (!empty($variables['node'])) {
		$variables['theme_hook_suggestions'][] = 'page__' . $variables['node']->type;
	}

	$variables['logo_img'] = '';
	if (!empty($variables['logo'])) {
		$variables['logo_img'] = theme('image', array(
				'path'  => $variables['logo'],
				'alt'   => strip_tags($variables['site_name']) . ' ' . t('logo'),
				'title' => strip_tags($variables['site_name']) . ' ' . t('Home'),
				'attributes' => array(
					'class' => array('logo'),
				),
			));
	}

	$variables['linked_logo']  = '';
	if (!empty($variables['logo_img'])) {
		$variables['linked_logo'] = l($variables['logo_img'], '<front>', array(
				'attributes' => array(
					'rel'   => 'home',
					'title' => strip_tags($variables['site_name']) . ' ' . t('Home'),
				),
				'html' => TRUE,
			));
	}
	
	$variables['search_form'] = module_invoke('search', 'block_view', 'search');

	$variables['linked_site_name'] = '';
	
	if (!empty($variables['site_name'])) {
		$variables['linked_site_name'] = l($variables['site_name'], '<front>', array(
				'attributes' => array(
					'rel'   => 'home',
					'title' => strip_tags($variables['site_name']) . ' ' . t('Home'),
				),
			));
	}

	// Site navigation links.
	$variables['base_menu_links'] = '';
	if (isset($variables['base_menu_links'])) {
		$variables['base_menu_links'] = theme('links__system_main_menu', array(
				'links' => $variables['base_menu_links'],
				'attributes' => array(
					'id' => 'main-menu',
					'class' => array('main-nav'),
				),
				'heading' => array(
					'text' => t('Main menu'),
					'level' => 'h2',
					'class' => array('element-invisible'),
				),
				'name' => 'menu_base_menu',
				'machine' => 'menu-base-menu',
			));
							
	}

	$variables['main_menu_links'] = '';
	if (isset($variables['main_menu_links'])) {
		$variables['main_menu_links'] = theme('links__system_main_menu', array(
				'links' => $variables['main_menu'],
				'attributes' => array(
					'id'    => 'secondary-menu',
					'class' => array('secondary', 'link-list'),
				),
				'heading' => array(
					'text' => t('Secondary menu'),
					'level' => 'h2',
					'class' => array('element-invisible'),
				),
				'name' => 'main_menu',
				'machine' => 'main-menu',
			));
	}
}

/**
 * Implements template_preprocess_panels_pane().
 *
 */
function fortified_dev_preprocess_panels_pane(&$variables) {
}

/**
 * Implements template_preprocess_views_views_fields().
 */
/* Delete me to enable
function THEMENAME_preprocess_views_view_fields(&$variables) {
 if ($variables['view']->name == 'nodequeue_1') {

   // Check if we have both an image and a summary
   if (isset($variables['fields']['field_image'])) {

     // If a combined field has been created, unset it and just show image
     if (isset($variables['fields']['nothing'])) {
       unset($variables['fields']['nothing']);
     }

   } elseif (isset($variables['fields']['title'])) {
     unset ($variables['fields']['title']);
   }

   // Always unset the separate summary if set
   if (isset($variables['fields']['field_summary'])) {
     unset($variables['fields']['field_summary']);
   }
 }
}
// */
/**
 * Implements template_preprocess_views_view().
 */
function fortified_dev_preprocess_views_view(&$variables) {

}

/**
 * Implements hook_css_alter()
 */
function fortified_dev_css_alter(&$css) {
	// Remove defaults.css file.
	//dsm(drupal_get_path('module', 'system') . '/system.menus.css');
	unset($css[drupal_get_path('module', 'system') . '/system.menus.css']);
}

/**
 * Implements hook_js_alter()
 */
function fortified_dev_js_alter(&$js) {
	if (!module_exists('jquery_update')) {
		// Swap out jQuery to use an updated version of the library.
		// $js['misc/jquery.js']['data'] = drupal_get_path('theme', 'fortified_dev') . '/js/vendor/jquery.js';
		$js['misc/jquery.js']['version'] = '1.8.2';
	}

	// @TODO moving scripts to footer possibly remove?
	// foreach ($js as $key => $js_script) {
	//   $js[$key]['scope'] = 'footer';
	// }
}

