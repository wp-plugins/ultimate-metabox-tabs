<?php

/*--------------------------------------------------------------------------------------
*
*	umt_add_extension
*	This will add an extension to Ultimate Metabox Tabs.
*
*	Do do so, hook add_action('umt_extension_loader','myfunction'), and place your
*	umt_add_extension functions there.
*
*	@author SilbinaryWolf
*	@since 1.0.0
* 
*-------------------------------------------------------------------------------------*/
function umt_add_extension($slug, $name, $description, $classname, $includedir = false)
{
	global $sw_ultimateMetaboxTab;
	return $sw_ultimateMetaboxTab->add_extension($slug, $name, $description, $classname, $includedir);
}

/*--------------------------------------------------------------------------------------
*
*	umt_register_settings_page
*	This will create a custom settings page on the Metabox Tabs menu. 
*
*	I recommend you do this from your extension, preferably in the admin_init action.
*	eg. add_action('admin_init','myfunction');
*
*	@author SilbinaryWolf
*	@since 1.0.0
* 
*-------------------------------------------------------------------------------------*/
function umt_register_settings_page($slug, $name)
{
	global $sw_ultimateMetaboxTab;
	return $sw_ultimateMetaboxTab->register_settings_page($slug, $name);
}


/*--------------------------------------------------------------------------------------
*
*	umt_register_div_types
*	This will register a list of selectable divs for the Metatab Editor.
*
*	$groupname = Name of the group of div ids. (eg. List of ACF metaboxes is called "ACF")
*
*	$list [array]: "name", "value", "class"
*			name = Option Name
*			value = DIV ID
*
*	@author SilbinaryWolf
*	@since 1.0.0
* 
*-------------------------------------------------------------------------------------*/
function umt_register_div_types($groupname,$list)
{
	global $sw_ultimateMetaboxTab;
	return $sw_ultimateMetaboxTab->register_div_types($groupname,$list);
}
?>