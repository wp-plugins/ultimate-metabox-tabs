<?php
/*--------------------------------------------------------------------------
*
*	umt_acf_post_list
*
*	
*
*	@author SilbinaryWolf
*	@acf_options_page author Elliot Condon
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
			add_action('umt_admin_menu_print_styles',array($this,'admin_menu_print_styles'));
		}
		else
		{
			trigger_error("ACF Post list support for Ultimate Metabox Tabs has become broken. Please contact the developer. For now, disable the extension.");
		}
	}
	
	function admin_menu_print_styles()
	{
		$posts = array();
		$query = new WP_Query("post_type=acf");
		while($query->have_posts()) 
		{
			$query->the_post();
			$new_post = array();
			$new_post['name'] = get_the_title();
			$new_post['value'] = "acf_" . get_the_ID();
			array_push($posts,$new_post);
		}
		wp_reset_query();
		
		//
		umt_register_div_types("ACF",$posts);
	}
}
?>