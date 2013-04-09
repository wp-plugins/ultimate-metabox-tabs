<?php 
/*--------------------------------------------------------------------------
*
*	umt_acf_options_page
*
*	@author SilbinaryWolf
*	@acf_options_page author Elliot Condon
* 
*-------------------------------------------------------------------------*/
 
if (class_exists('acf_options_page'))
{
 
class umt_acf_options_page extends acf_options_page 
{
	var $umt;
	
	function __construct($parent)
	{
		$this->umt = $parent;
		
		// Setup Addon
		global $acf;
		if (isset($acf->options_page))
		{
			// call the original acf_options_page constructor
			parent::__construct($acf);
		
			// setup initiation procedure
			add_action('init',array($this,'init'),99);
			
			// setup register options page init
			add_action('admin_init',array($this,'umt_admin_init'));
		}
		else
		{
			trigger_error("ACF Options Page support for Ultimate Metabox Tabs has become broken. Please contact the developer. For now, disable the extension.");
		}
	}
	
	function init()
	{
		global $acf;
		// remove the admin menu hook
		remove_action('admin_menu', array($acf->options_page,'admin_menu'));
		
		// override the original options page
		$acf->options_page = $this;
	}
	
	function admin_body_class($classes)
	{
		$classes = str_replace(" umt_no_margin","",$classes);
		return $classes;
	}
	
	function umt_admin_init()
	{
		// register metatabs page
		umt_register_settings_page($this->umt_slug,$this->umt_name);
	}
	
	function admin_head() {
		$this->umt->metatab_load_settings_page($this->umt_slug);
  		$this->umt->admin_head();
		
		add_filter('admin_body_class',array($this,'admin_body_class'));
		
		parent::admin_head();
	}

	function admin_print_scripts() {
		$this->umt->metatab_load_settings_page($this->umt_slug);
		parent::admin_print_scripts();
		// remove original filter, only if on the 'options page'
		remove_filter('richedit_pre', array($this->umt,'richedit_pre'),99);
		
  		$this->umt->admin_print_scripts();
	}
	
	function admin_print_styles() {
		parent::admin_print_styles();
		$this->umt->admin_print_styles();
	}

	function html()
	{
		parent::html();	
		// append metabox tabs
		$this->umt->metatab_create();
	}
}
}
?>