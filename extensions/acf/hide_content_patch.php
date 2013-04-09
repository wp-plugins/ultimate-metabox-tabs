<?php
/*--------------------------------------------------------------------------
*
*	umt_acf_hide_content_patch
*
*	Patches ACF's "Hide Content" to hide in in a way that doesn't affect UMT.
*
*	@author SilbinaryWolf
*	@acf_options_page author Elliot Condon
* 
*-------------------------------------------------------------------------*/
/* 
	If there is no plugin support or this patch has become broken simply
	copy and paste the acf->input controllers 'admin_head' code over this 
	admin_head.
	
	The only area of the code patched is where it says 'patch CSS start'
	and ends at 'patch CSS end', so just CTRL+F to that.
	
	Last time I checked ACF had a 'controllers' folder, the file should be
	called 'input.php'
*/

class umt_acf_hide_content_patch 
{
	var $umt, $input, $patch_string;

	function __construct($parent)
	{
		$this->umt = $parent;
		
		// Only use this extension if the patch isn't in use.
		if ($this->umt->using_top_page_hook <= 0)
		{
			// Setup Addon
			global $acf;
			if (isset($acf->input))
			{
				$this->patch_string = '#wp-content-editor-container , #post-status-info { position:absolute !important; left:-6000px !important; height:0px !important; } #wp-content-editor-tools { opacity:0 !important; } #post-body-content { height:75px !important; } ';
			
				// setup initiation procedure
				add_action('init',array($this,'init'));
			}
			else
			{
				trigger_error("ACF 'Hide Content Patch' support for Ultimate Metabox Tabs has become broken. Please contact the developer. For now, disable the extension.");
			}
		}
	}

	function init()
	{
		global $acf;
		
		// store input controller
		$this->input = $acf->input;
		// remove the admin menu hook
		remove_action('admin_head', array($acf->input,'admin_head'));
		remove_action('wp_ajax_get_input_style', array($acf->input, 'ajax_get_input_style'));
		// add a custom admin menu hook
		add_action('admin_head',array($this,'admin_head'));
		add_action('wp_ajax_get_input_style', array($this, 'ajax_get_input_style'));
	}

	function ajax_get_input_style()
	{
		// overrides
		if(isset($_POST['acf_id']))
		{
			$style = $this->input->get_input_style($_POST['acf_id']);
			$style = str_replace('#postdivrich {display: none;}',$this->patch_string,$style);
			echo $style;
		}
		
		die;
	}
	
	function admin_head()
	{
		// validate page
		if( ! $this->input->validate_page() ) return;
		
		// globals
		global $post, $pagenow, $typenow;
		
		
		// shopp
		if( $pagenow == "admin.php" && isset( $_GET['page'] ) && $_GET['page'] == "shopp-products" && isset( $_GET['id'] ) )
		{
			$typenow = "shopp_product";
		}
		
		
		// vars
		$post_id = 0;
		
		if( $post )
		{
			$post_id = $post->ID;
		}
		
			
		// get style for page
		$metabox_ids = $this->input->parent->get_input_metabox_ids( array( 'post_id' => $post_id, 'post_type' => $typenow ), false);
		
		// patch CSS start
		$style = isset($metabox_ids[0]) ?  $this->input->get_input_style($metabox_ids[0]) : '';
		$style = str_replace('#postdivrich {display: none;}',$this->patch_string,$style);
		// patch CSS end
		echo '<style type="text/css" id="acf_style" >' .$style . '</style>';
		

		// Style
		echo '<style type="text/css">.acf_postbox, .postbox[id*="acf_"] { display: none; }</style>';
		
		
		// Javascript
		echo '<script type="text/javascript">acf.post_id = ' . $post_id . '; acf.nonce = "' . wp_create_nonce( 'acf_nonce' ) . '";</script>';
		
		
		// add user js + css
		do_action('acf_head-input');
		
		
		// get acf's
		$acfs = $this->input->parent->get_field_groups();
		
		if($acfs)
		{
			foreach($acfs as $acf)
			{
				// hide / show
				$show = in_array($acf['id'], $metabox_ids) ? "true" : "false";
				$priority = 'high';
				if( $acf['options']['position'] == 'side' )
				{
					$priority = 'core';
				}
				
				// add meta box
				add_meta_box(
					'acf_' . $acf['id'], 
					$acf['title'], 
					array($this->input, 'meta_box_input'), 
					$typenow, 
					$acf['options']['position'], 
					$priority, 
					array( 'fields' => $acf['fields'], 'options' => $acf['options'], 'show' => $show, 'post_id' => $post->ID )
				);
				
			}
			// foreach($acfs as $acf)
		}
		// if($acfs)
	}
}
?>