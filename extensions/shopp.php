<?php
/*--------------------------------------------------------------------------
*
*	umt_shopp_support
*	Adds Shopp support to metabox tabs
*
*	@author SilbinaryWolf
* 
*-------------------------------------------------------------------------*/

class umt_shopp_support
{
	var $umt, $input;

	function __construct($parent)
	{
		$this->umt = $parent;

		// setup initiation procedure
		add_action('init',array($this,'init'),99);
	}

	function init()
	{
		// setup shopp
		if (post_type_exists('shopp_product'))
		{
			add_action('admin_head', array($this, 'admin_head'));
			add_action('umt_admin_menu_print_styles-shopp_product',array($this,'admin_menu_print_styles'));
		}
	}
	
	function admin_head()	
	{
		if (isset($_GET['id']) && isset($_GET['page']) && isset($_GET['page']) == "shopp-products")
		{
			$this->umt->post_type = "shopp_product";
			$this->umt->admin_head();
			$this->umt->admin_print_styles();
			$this->umt->admin_print_scripts();
			
			// Filter metaboxes if on Shopp page.
			add_filter('umt_filter_metabox_screen', array($this, 'filter_metabox_screen'),10,1);
			
			// Use the 'notices' hook of Shopp for the Metabox tabs area
			$this->using_top_page_hook = true;
			remove_filter('richedit_pre', array($this->umt,'richedit_pre'),99);
			add_action('shopp_admin_notice', array($this->umt, 'post_header_html'),99);
		}
	}
	
	function filter_metabox_screen($the_wp_meta_boxes = array())
	{
		// Get the current screen
		global $wp_meta_boxes;
		$screen = get_current_screen()->id;
		
		// If on the Shopp product page, make sure to get the ACF fields too.
		if ($screen == "toplevel_page_shopp-products")
		{
			$screen = "shopp_product";
			if (isset($wp_meta_boxes[$screen]['normal']['core']))
			{
				$the_wp_meta_boxes = $wp_meta_boxes[$screen]['normal']['core'];
			}
			if (isset($wp_meta_boxes[$screen]['normal']['high']))
			{
				$the_wp_meta_boxes = array_merge($the_wp_meta_boxes,$wp_meta_boxes[$screen]['normal']['high']);
			}
			if (isset($wp_meta_boxes[$screen]['side']))
			{
				if (isset($wp_meta_boxes[$screen]['side']))
				{
					if (isset($wp_meta_boxes[$screen]['side']['high']))
					{
						$the_wp_meta_boxes = array_merge($the_wp_meta_boxes,$wp_meta_boxes[$screen]['side']['high']);
					}
				}
			}
		}
		
		return $the_wp_meta_boxes;
	}
	
	function admin_menu_print_styles()
	{
		// Reads the ACF posts, which act as metaboxes
		$metaboxes = array();
		
		$new_post = array('name' => "Summary", 'value' => "product-summary");
		array_push($metaboxes,$new_post);
		
		$new_post = array('name' => "Details & Specs", 'value' => "product-details-box");
		array_push($metaboxes,$new_post);
		
		$new_post = array('name' => "Product Images", 'value' => "product-images");
		array_push($metaboxes,$new_post);
		
		$new_post = array('name' => "Pricing", 'value' => "product-pricing-box");
		array_push($metaboxes,$new_post);
		
		$new_post = array('name' => "Catalog Categories", 'value' => "shopp_category-box");
		array_push($metaboxes,$new_post);
		
		$new_post = array('name' => "Catalog Tags", 'value' => "shopp_tag-box");
		array_push($metaboxes,$new_post);
		
		$new_post = array('name' => "Settings", 'value' => "product-settings");
		array_push($metaboxes,$new_post);

		// Places the posts into a div group list
		umt_register_div_types(__( 'Shopp Metaboxes', 'shopp' ),$metaboxes);
	}
}
?>