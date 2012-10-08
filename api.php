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

?>