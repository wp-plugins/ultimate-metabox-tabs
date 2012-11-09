<?php
/*--------------------------------------------------------------------------
*
*	Edit-Form-Advanced.php Patcher
*
*	This adds a new action hook to "wp-admin/edit-form-advanced.php" allowing markup
*	to be inserted between the page title and post title.
*
*	status - -1 = failed to patch
*	status - FALSE = not patched
*	status - TRUE = patched
*
*	@author SilbinaryWolf
* 
*-------------------------------------------------------------------------*/

class umt_edit_form_advanced_patcher
{
	var $umt, $status, $description, $save_key, $save_data;

	function __construct($parent)
	{
		$this->umt = $parent;
		$this->save_key = "umt_edit_form_advanced_patch_status";
		$this->wp_version = get_bloginfo('version');
		
		$this->save_data = get_option($this->save_key,array('version' => $this->wp_version, 'status' => false));
		$this->status = $this->save_data['status'];
		// Check for change in WP version
		if ($this->save_data['version'] != $this->wp_version)
		{
			// If patch is meant to be applied
			if ($this->status > 0)
			{
				// Install patch since Wordpress has updated since.
				if ($this->install() == false)
				{
					// If the patch failed to install, show the patch as uninstalled.
					$this->status = false;
					update_option($this->save_key,array('version' => $this->wp_version, 'status' => $this->status));
				}
			}
		}
		
		$this->name = 'Edit-Form-Advanced Patcher';
		$this->description = 'Patches "wp-admin/edit-form-advanced.php" to add a new hook, so that the metabox tabs can be inserted \nbetween the page title and post title without CSS hacks. Can increase support for plugins that hide or modify the content.';
	}

	function init()
	{
		$this->status = $this->is_patched();
	}
	
	// Checks if the patch has been applied.
	function is_patched()
	{
		$edit_form_advanced = file_get_contents(ABSPATH . "wp-admin/edit-form-advanced.php");
		if (strstr($edit_form_advanced,'do_action("post_header_html");') === FALSE)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// Called in the "Patch" menu when "Apply Patch" is clicked
	function install()
	{
		$edit_form_advanced = file_get_contents(ABSPATH . "wp-admin/edit-form-advanced.php");
		if (strstr($edit_form_advanced,'do_action("post_header_html");') === FALSE)
		{
			$edit_form_advanced = str_replace('<form name="post" action="post.php"','<?php /* UMT Patch */ do_action("post_header_html"); ?>'."\n".'<form name="post" action="post.php"',$edit_form_advanced);
			file_put_contents(ABSPATH . "wp-admin/edit-form-advanced.php",$edit_form_advanced);
			if ($this->is_patched() == false)
			{
				$this->status = -1;
				return 'An error occurred patching. Make sure you CMOD settings for "wp-admin/edit-form-advanced.php" are writeable (CHMOD 777)';
			}
			$this->status = true;
		}
		update_option($this->save_key,array('version' => $this->wp_version, 'status' => true));
		return true;
	}
	
	// Called in the "Patch" menu when "Uninstall Patch" is clicked
	function uninstall()
	{
		$edit_form_advanced = file_get_contents(ABSPATH . "wp-admin/edit-form-advanced.php");
		if (strstr($edit_form_advanced,'do_action("post_header_html");') !== FALSE)
		{
			$edit_form_advanced = str_replace('<?php /* UMT Patch */ do_action("post_header_html"); ?>'."\n",'',$edit_form_advanced);
			file_put_contents(ABSPATH . "wp-admin/edit-form-advanced.php",$edit_form_advanced);
			if ($this->is_patched() == true)
			{
				$this->status = 1;
				return 'An error occurred patching. Make sure you CMOD settings for "wp-admin/edit-form-advanced.php" are writeable (CHMOD 777)';
			}
			update_option($this->save_key,array('version' => $this->wp_version, 'status' => false));
			$this->status = false;
		}
		return true;
	}
}
?>