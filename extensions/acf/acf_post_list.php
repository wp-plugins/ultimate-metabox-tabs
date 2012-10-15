<?php
/*--------------------------------------------------------------------------
*
*	umt_acf_post_list
*	
*
*	@author SilbinaryWolf
* 
*-------------------------------------------------------------------------*/

class umt_acf_post_list
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
		// setup acf
		if (post_type_exists('acf'))
		{
			// hooks to the admin_men_print_styles if its used by Ultimate Metabox tabs
			add_action('umt_admin_menu_print_styles',array($this,'admin_menu_print_styles'));
		}
		else
		{
			trigger_error("ACF Post list support for Ultimate Metabox Tabs has become broken. Please contact the developer. For now, disable the extension.");
		}
	}
	
	function admin_menu_print_styles()
	{
		// Reads the ACF posts, which act as metaboxes
		$posts = array();
		$query = new WP_Query("post_type=acf&showposts=-1");
		while($query->have_posts()) 
		{
			$query->the_post();
			$new_post = array();
			$new_post['name'] = get_the_title();
			$new_post['value'] = "acf_" . get_the_ID();
			array_push($posts,$new_post);
		}
		wp_reset_query();
		
		// Places the posts into a div group list
		umt_register_div_types(__( 'Advanced Custom Fields', 'acf' ),$posts);
	}
}
?>