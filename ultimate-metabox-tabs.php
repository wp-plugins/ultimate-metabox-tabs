<?php
/*
Plugin Name: Ultimate Metabox Tabs
Plugin URI: none
Description: Adds extendable metabox tabs to your posts.
Version: 0.9.5
Author: SilbinaryWolf
Author URI: none
License: GPLv2 or later
*/
/*
	Changelog:
	----------
	= 0.9.5 =
	* Added a select box extension, for easy configuration with ACF.
	* Added a new extension API command, so that custom metabox selections can be created.
	
	= 0.9.4 =
	* Added extensions API, which will allow for custom settings pages.
	* Added a patch extension (toggeable) which allows ACF's "Hide Content" option to work.
	* Allowed the toggling of the ACF Options Page metatabs, in case of users not wanting them there or future ACF update breaks.

	= 0.9.3 =
	* Fixed a bug in the umt-post.js that caused saving to work oddly.
	
	= 0.9.2 =
	* Fixed a bug in the javascript that stopped Firefox from working.
	
	= 0.9.1 = 
	* Fixed invalid script/style hooks in ACF Options Page.
	
	= 0.9.0 =
	* Internal Beta release.
*/
/*
	Control+F "#POTBUG" to find potential Metabox Tab Breaking code
*/

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'api.php');

// Setup Metabox Tab Object
global $sw_ultimateMetaboxTab;
$sw_ultimateMetaboxTab = new UltimateMetaboxTabs();

class UltimateMetaboxTabs
{
	var $dir,
		$url,
		$version,
		$developer_email,
		$option_autoload,
		$post_database_prefix,
		$option_database_prefix,
		$extension_database_prefix,
		$metatab_info,
		$menu_parent,
		$menu_slug,
		$menu_url,
		$extensions,
		$settings_pages,
		$div_options,
		$metatabs_post_loaded,
		$metatabs_options_loaded,
		$metatab_custom_settings_loaded,
		$metatabs_created,
		$post_type,
		$settings_type;

	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function __construct()
	{
		// vars
		$this->dir = plugin_dir_path(__FILE__);
		$this->url = plugins_url('',__FILE__);
		$this->developer_email = "doogie1012@gmail.com";
		$this->version = '0.9.5';
		
		// The array where the metabox tabs are loaded into.
		$this->metatab_info = array();
		
		// These arrays store extensions and pages registered to give the plugin extra functionalitys
		$this->extensions = array();
		$this->settings_pages = array();
		$this->div_options = array();
		
		// Whether the metabox tabs have been loaded and/or created.
		$this->metatabs_post_loaded = false;
		$this->metatabs_options_loaded = false;
		$this->metatab_custom_settings_loaded = array();
		$this->metatabs_created = false;
		
		// Database Settings
		$this->option_autoload = true;
		$this->extension_database_prefix = "sw_extension_metatab_";
		$this->post_database_prefix = "sw_post_metatab_";
		$this->option_database_prefix = "sw_option_metatab";
		
		// 
		$this->settings_database_prefix = "sw_";
		$this->settings_database_suffix = "_metatab";
		
		// Menu Settings
		$this->menu_parent = 'options-general.php';
		$this->menu_slug = 'metabox-tabs';
		$this->css_enable_class = 'umt_enabled';

		// URL Setting
		$this->menu_url = admin_url($this->menu_parent.'?page='.$this->menu_slug);
		
		// actions/filters
		add_filter("plugin_action_links_" . plugin_basename(__FILE__), array($this,'plugin_settings_link') );
		
		// 
		add_action('admin_head-post.php', array($this, 'admin_head'));
		add_action('admin_print_styles-post.php', array($this, 'admin_print_styles'));
		add_action('admin_print_scripts-post.php', array($this, 'admin_print_scripts'));
		
		add_action('admin_head-post-new.php', array($this, 'admin_head'));
		add_action('admin_print_styles-post-new.php', array($this, 'admin_print_styles'));
		add_action('admin_print_scripts-post-new.php', array($this, 'admin_print_scripts'));
		
		add_action('admin_print_scripts-settings_page_' . $this->menu_slug, array($this, 'admin_menu_print_scripts'));
		add_action('admin_print_styles-settings_page_' . $this->menu_slug, array($this, 'admin_menu_print_styles'));
		
		add_action('admin_menu', array($this,'admin_menu'));

		add_filter('richedit_pre', array($this,'richedit_pre'),99); // Creates the metabox tabs
		add_filter('admin_body_class', array($this,'admin_body_class'));
		
		// custom actions
		add_action('umt_template', array($this, 'metatab_template'), 10, 1);
		
		// Custom DIV Command(s)
		add_action('umt_custom_inactive-the_content', array($this, 'metatab_custom_inactive_the_content'));
		add_action('umt_custom_active-the_content', array($this, 'metatab_custom_active_the_content'));
		
		// Extend the support to specific plugins
		// include ACF options page support if ACF exists
		global $acf;
		if (isset($acf))
		{
			$acf_addon_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'acf' . DIRECTORY_SEPARATOR;
			// Add ACF Options Support
			$this->add_extension(	"acf-options", 
									__("ACF Options","umt"), 
									__("Overrides the ACF Options page class and gives it Metabox Tabs.","umt"),
									"umt_acf_options_page",
									$acf_addon_dir . 'options_page_mod.php');
									
			// Add ACF Post List Support
			$this->add_extension(	"acf-post-list", 
									__("ACF Post Lists","umt"), 
									__("Adds ACF posts as a selectable div to the metatab editor.","umt"),
									"umt_acf_post_list",
									$acf_addon_dir . 'acf_post_list.php');
	
			// Add ACF Hide Content Support
			$this->add_extension(	"acf-hide-content", 
									__("ACF Hide Content Patch","umt"), 
									__("Patches the 'admin_head' (and ajax call) of the ACF input controller so it hides the content in an alternative way.\n I recommend you disable this if you don't use 'Hide Content' and metabox tabs on the same page for maximum ACF compatiblity.","umt"),
									"umt_acf_hide_content_patch",
									$acf_addon_dir . 'hide_content_patch.php');
		}
		do_action('umt_extension_loader');
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	richedit_pre
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function richedit_pre($pre)
	{
		$this->metatab_create();
		return $pre;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	add_extension
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function add_extension($slug, $name, $description, $object, $includedir = false)
	{
		if (isset($this->extensions[$slug]) === false)
		{
			// check for error
			if (is_object($object))
			{
				trigger_error('Ultimate Metabox Tabs requires you type the $object as a string.');
				return false;
			}
		
			// load if extension is enabled (by default, it is)
			$enabled = get_option($this->extension_database_prefix . $slug,true);
			
			// setup extension
			$this->extensions[$slug] = array();
			$this->extensions[$slug]['name'] = $name;
			$this->extensions[$slug]['description'] = $description;
			$this->extensions[$slug]['object'] = false;
			$this->extensions[$slug]['enabled'] = $enabled;
			
			// Only create the extension object if it's enabled.
			if ($enabled)
			{
				// include only if enabled
				if ($includedir !== false)
				{
					include_once($includedir);
				}
				// if the input is a string, make it create the object
				if (is_string($object))
				{
					if (class_exists($object))
					{
						$object = new $object($this);
					}
					else
					{
						trigger_error("Invalid Ultimate Metabox Tabs extension, class does not exist.");
						$this->extensions[$slug]['description'] = "- Invalid Ultimate Metabox Tabs extension, class does not exist. -" . $this->extensions[$slug]['description'];
						// Disable Extension
						$enabled = false;
						$this->extensions[$slug]['enabled'] = $enabled;
					}
				}
			
				// set the object
				$this->extensions[$slug]['object'] = $object;
				
				// setup additional included variables
				$object->umt = $this;
				$object->umt_name = $name;
				$object->umt_description = $description;
				$object->umt_slug = $slug;
				$object->umt_enabled = $enabled;
			}
			return true;
		}
		return false;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	register_settings_page
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function register_settings_page($slug, $name)
	{	
		if (isset($this->settings_pages[$slug]) == false)
		{
			$this->settings_pages[$slug] = array();
			$this->settings_pages[$slug]['name'] = $name;
		}
		return false;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	register_div_types
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function register_div_types($groupname,$list)
	{
		$group = array();
		$group['name'] = $groupname;
		$group['div'] = $list;
		
		array_push($this->div_options,$group);
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	extension_save
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function extension_save($slug, $enable)
	{
		if ($slug != NULL && $slug != false && $slug != "")
		{
			// Wordpress can't store exactly false, so make it numerical.
			if ($enable === false)
			{
				$enable = 0;
			}
			update_option( $this->extension_database_prefix . $slug, $enable );
		}
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	post_types_ignored
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function post_types_ignored($post)
	{
		if ($post == "nav_menu_item" || $post == "revision" || $post == "attachment" || $post == "acf")
		{
			return true;
		}
		return false;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_menu
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_menu()
	{
		add_submenu_page( $this->menu_parent, __('Ultimate Metabox Tabs','umt'), __('Metabox Tabs','umt'), 'administrator', $this->menu_slug, array($this,'admin_view') );
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_head()
	{
		if ($this->metatab_load())
		{
			$output = "";
			$i = 0;
			echo '<style type="text/css" id="umt_style">';
			foreach($this->metatab_info as $metatab)
			{
				$output .= $this->umt_get_div_ids($metatab);
			}
			echo $output;
			echo "</style>";
		}
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_body_class
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_body_class($classes)
	{
		if ($this->metatab_load())
		{
			// Validate the tabs, if they cant be found don't add styling.
			if ($this->metatab_validate() == false)
			{
				return $classes;
			}
			/*
				If all metabox tabs were deleted due to no found metaboxes
				disable the metabox tab spacing.
			*/

			if (count($this->metatab_info)>0)
			{
				$ref = $this->metatab_info;
				$first_metatab = reset($ref);
				if (count($this->metatab_info)>1)
				{
					$classes .= " umt_group_" . $first_metatab['id'] . "_class " . $this->css_enable_class;
				}
				else
				{
					$classes .= " umt_group_" . $first_metatab['id'] . "_class";
				}
			}
		}
		return $classes;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	umt_get_div_ids
	*
	*	Returns a list of the div ids NOT in the specified group with commas
	*	so that the data can be used to override the style sheet with ease.
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function umt_get_div_ids($group)
	{
		$inactive_output = "";
		$active_output = "";
		$custom_metatab_command = array();
		
		// Setup the classes and code for when the tab is unselected.
		$pre = "." . $this->css_enable_class . " ";
		foreach ($group['div'] as $div)
		{
			if (substr($div['name'], 0, 1) !== "+")
			{
				// If output exists before it, split use a comma so they can share
				// the same styling.
				if ($inactive_output != "")
				{
					$inactive_output .= ",";
				}
				// Set the output
				$inactive_output .= $pre . "#" . $div['name'];
			}
			else
			{
				$hook_name = 'umt_custom_inactive-'.substr($div['name'], 1);
				if (has_action($hook_name))
				{
					array_push($custom_metatab_command,apply_filters($hook_name,$pre));
				}
			}
		}
		
		// If there is output, add the necessary styling.
		if ($inactive_output != "")
		{
			$inactive_output .= " { position:absolute; left:-6000px; height:0px; opacity:0; } \n";
		}
		
		// Custom DIV Commands Hooks
		foreach ($custom_metatab_command as $command)
		{
			$inactive_output .= $command . "\n";
		}
		
		// Reset array
		$custom_metatab_command = array();
		
		// Setup the classes and code for when the tab is selected.
		$pre = ".umt_group_" . $group['id'] . "_class ";
		foreach ($group['div'] as $div)
		{
			if (substr($div['name'], 0, 1) !== "+")
			{
				// If output exists before it, split use a comma so they can share
				// the same styling.
				if ($active_output != "")
				{
					$active_output .= ",";
				}
				// Set the output
				$active_output .= $pre . " #" . $div['name'];
			}
			else
			{
				$hook_name = 'umt_custom_active-'.substr($div['name'], 1);
				if (has_action($hook_name))
				{
					array_push($custom_metatab_command,apply_filters($hook_name,$pre));
				}
			}
		}
		// If there is output, add the necessary styling.
		if ($active_output != "")
		{
			$active_output .= " { position:inherit; left:inherit; height:inherit; opacity:inherit; } \n";
		}
		
		// Custom DIV Commands Hooks
		foreach ($custom_metatab_command as $command)
		{
			$active_output .= $command . "\n";
		}
		return $inactive_output . $active_output;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_styles
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_print_styles()
	{
		wp_register_style('ultimate-metabox-tabs-css',$this->url . '/css/umt-general.css');
		wp_enqueue_style('ultimate-metabox-tabs-css');
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_scripts()
	{
		if ($this->metatab_load())
		{
			wp_enqueue_script('jquery');
			wp_register_script('ultimate-metabox-tabs-script',$this->url . '/js/umt-general.js');
			wp_enqueue_script('ultimate-metabox-tabs-script');
		}	
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_menu_print_styles
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_menu_print_styles()
	{
		do_action('umt_admin_menu_print_styles');
		
		wp_register_style('ultimate-metabox-tabs-editor-css',$this->url . '/css/umt-editor.css');
		wp_enqueue_style('ultimate-metabox-tabs-editor-css');
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_menu_print_scripts
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_menu_print_scripts()
	{
		do_action('umt_admin_menu_print_scripts');
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		
		wp_register_script('ultimate-metabox-tabs-post-script',$this->url . '/js/umt-post.js');
		wp_enqueue_script('ultimate-metabox-tabs-post-script');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_load
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_load()
	{
		// Choose general post metabox or option metaboxes
		if ($this->post_type !== NULL)
		{
			$option_name = $this->post_database_prefix . $this->post_type;
		}
		else if ($this->settings_type == "options")
		{
			$option_name = $this->option_database_prefix;
		}
		else if ($this->settings_type != NULL)
		{
			$option_name = $this->settings_database_prefix . $this->settings_type . $this->settings_database_suffix;
		}
		else
		{
			return NULL;
		}
		/* Get the values */
		$value = get_option($option_name,NULL);
		if ($value === NULL || $value === false)
		{
			/* Setup Default Options */
			$value = array();
			
			/* Create Group */
			$group = array();
			
			/* Create DIV */
			$divid = array();
			return $value;
		}
		else
		{
			return $value;
		}
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	metatab_load_settings_page
	* 	loads the metatabs for a specific metatab page.
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function metatab_load_settings_page($slug)
	{
		if (isset($this->metatab_custom_settings_loaded[$slug]) == false || $this->metatab_custom_settings_loaded[$slug] == false)
		{
			$option_name = $this->settings_database_prefix . $slug . $this->settings_database_suffix;
			$metatab_settings = get_option($option_name,array());
			if ($metatab_settings === NULL || $metatab_settings === false)
			{
				$metatab_settings = array();
			}
			if (count($this->metatab_info)>0)
			{
				// Make sure metabox tabs with the same group name are fused together
				foreach($this->metatab_info as $key => $metatab)
				{
					foreach ($metatab_settings as $key_post => $metatab_setting)
					{
						if ($metatab['name'] == $metatab_setting['name'])
						{
							$this->metatab_info[$key]['div'] = array_merge($this->metatab_info[$key]['div'],$metatab_settings[$key_post]['div']);
						}
					}
				}
				// Merge unmatching group names together
				$this->metatab_info = array_merge($metatab_settings,$this->metatab_info);
			}
			else
			{
				$this->metatab_info = $metatab_settings;
			}
			$this->metatab_custom_settings_loaded[$slug] = true;
		}
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	metatab_load
	* 	loads the relevant metabox tab information for the post type
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function metatab_load()
	{
		if ($this->metatabs_options_loaded != true)
		{
			$option_name = $this->option_database_prefix;
			// Load the metatab information
			$metatab_options = get_option($option_name,array());
			if ($metatab_options === NULL || $metatab_options === false)
			{
				$metatab_options = array();
			}
			if (count($this->metatab_info)>0)
			{
				// Make sure metabox tabs with the same group name are fused together
				foreach($this->metatab_info as $key => $metatab)
				{
					foreach ($metatab_options as $key_post => $metatab_option)
					{
						if ($metatab['name'] == $metatab_option['name'])
						{
							$this->metatab_info[$key]['div'] = array_merge($this->metatab_info[$key]['div'],$metatab_options[$key_post]['div']);
						}
					}
				}
				// Merge unmatching group names together
				$this->metatab_info = array_merge($metatab_options,$this->metatab_info);
			}
			else
			{
				$this->metatab_info = $metatab_options;
			}
			
			// enable loaded flag
			$this->metatabs_options_loaded = true;
		}
		if ($this->metatabs_post_loaded != true)
		{
			// Don't attempt to load if there is no post type
			if (get_post_type() !== '')
			{
				// Setup defaults
				$this->metatabs_post_loaded = true;
				$this->post_type = get_post_type();
				$option_name = $this->post_database_prefix . $this->post_type;
				
				// Load the metatab information
				$metatab_posts = get_option($option_name,NULL);
				if ($metatab_posts === NULL || $metatab_posts === false)
				{
					$metatab_posts = array();
				}
				if (count($this->metatab_info)>0)
				{
					// Make sure metabox tabs with the same group name are fused together
					foreach($this->metatab_info as $key => $metatab)
					{
						foreach ($metatab_posts as $key_post => $metatab_post)
						{
							if ($metatab['name'] == $metatab_post['name'])
							{
								$this->metatab_info[$key]['div'] = array_merge($this->metatab_info[$key]['div'],$metatab_posts[$key_post]['div']);
							}
						}
					}
					// Merge unmatching group names together
					$this->metatab_info = array_merge($metatab_posts,$this->metatab_info);
				}
				else
				{
					$this->metatab_info = $metatab_posts;
				}
			}
		}
		if (count($this->metatab_info)>1)
		{
			return true;
		}
		return false;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	metatab_validate
	* 	makes sure the tabs are shown on the page
	*
	*	return: false/true
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function metatab_validate()
	{
		/*
			Validate the metabox tabs by checking to see if the metaboxes
			exist.
		*/
		global $wp_meta_boxes;
		$the_wp_meta_boxes = -1;
		// Get the current screen
		$screen = get_current_screen()->id;
		if (isset($wp_meta_boxes[$screen]))
		{
			$the_wp_meta_boxes = array();
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
		else
		{
			// If the current screen isn't found, find the first value in the metaboxes.
			if (isset($wp_meta_boxes))
			{
				// The Loop ensures the LAST screen in the array is used
				// however, it could end up using the WRONG screen. Be wary.
				// Potential Bug #POTBUG
				foreach ($wp_meta_boxes as $the_screen => $data)
				{
					$screen = $the_screen;
					echo $the_screen;
				}
				// If key can be found on the metaboxes
				if (isset($wp_meta_boxes[$screen]))
				{
					$the_wp_meta_boxes = array();
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
						if (isset($wp_meta_boxes[$screen]['side']['high']))
						{
							$the_wp_meta_boxes = array_merge($the_wp_meta_boxes,$wp_meta_boxes[$screen]['side']['high']);
						}
					}
				}
			}
		}
		// If Metaboxes are loaded and found
		if ($the_wp_meta_boxes !== -1)
		{
			foreach ($this->metatab_info as $metatab_key => $metatab)
			{
				foreach ($metatab['div'] as $div_key => $div)
				{
					if (isset($the_wp_meta_boxes[$div_key]))
					{
						if (isset($the_wp_meta_boxes[$div_key]['args']))
						{
							if (isset($the_wp_meta_boxes[$div_key]['args']['show']))
							{
								if ($the_wp_meta_boxes[$div_key]['args']['show'] === 'false')
								{
									if (substr($this->metatab_info[$metatab_key]['div'][$div_key]['name'],0,1) !== "+")
									{
										unset($this->metatab_info[$metatab_key]['div'][$div_key]);
									}
								}
							}
						}
					}
					else
					{
						if (substr($this->metatab_info[$metatab_key]['div'][$div_key]['name'],0,1) !== "+")
						{
							unset($this->metatab_info[$metatab_key]['div'][$div_key]);
						}
					}
				}
				if (count($this->metatab_info[$metatab_key]['div'])<=0)
				{
					unset($this->metatab_info[$metatab_key]);
				}
			}
			return true;
		}
		return false;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	plugin_settings_link
	* 	adds a settings hyperlink to the plugin menu
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function plugin_settings_link($links)
	{
		$settings_link = '<a href="' . $this->menu_parent . '?page=' . $this->menu_slug . '">Settings</a>';
		array_unshift($links, $settings_link); 
		return $links; 
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	metatab_create
	* 	creates the desired metatabs
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function metatab_create()
	{
		if ($this->metatabs_created === false)
		{
			// Only create the metabox tabs if there is more than one tab to select.
			if (count($this->metatab_info)>1)
			{
				if (wp_style_is('ultimate-metabox-tabs-css','queue'))
				{
					do_action('umt_template',$this->metatab_info);
					$this->metatabs_created = true;
					return true;
				}
			}
			wp_deregister_style('ultimate-metabox-tabs-css');
			return false;
		}
		return true;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	metatab_template
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function metatab_template($metatab_list)
	{
	?>
		<div id="sw-ultimate-metabox-tab-list-container">
			<ul id="sw-ultimate-metabox-tab-list" class="metabox-tabs" style="z-index:99;">
				<?php $i = 0; ?>
				<?php foreach ($metatab_list as $metatab):?>
				<?php $class = ($i == 0) ? 'class="active"' : ''; ?>
				<li id="<?php echo $metatab['id']; ?>">
					<a href="#" <?php echo $class; ?>><?php echo $metatab['name']; ?></a>
				</li>
				<?php $i++; endforeach; ?>
			</ul>
		</div>
	<?php
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	metatab_custom
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function metatab_custom_inactive_the_content($id)
	{
		return $id . '#wp-content-editor-container , ' . $id . '#post-status-info { position:absolute; left:-6000px; height:0px; } ' . $id . '#wp-content-editor-tools { opacity:0; }  ' . $id . '#post-body-content { height:75px; } '."\n";
	}
	
	function metatab_custom_active_the_content($id)
	{
		$id = "." . $this->css_enable_class . "" . $id;
		return $id . '#wp-content-editor-container , ' . $id . '#post-status-info { position:relative; left:inherit; height:auto; } ' . $id . '#wp-content-editor-tools { opacity:inherit; } ' . $id . '#post-body-content { height:auto; } '."\n";
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_save
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_save()
	{
		$all_groups = array();
		if (isset($_POST['umt_group']))
		{
			foreach ($_POST['umt_group'] as $group_key => $group_name)
			{
				// If the group has a valid name
				if ($group_name !== "")
				{
					// If the group has divs
					if (isset($_POST['umt_div'][$group_key]))
					{
						$group = array();
						$group['name'] = $group_name;
						$group['id'] = $group_key;
						$group['div'] = array();
						foreach ($_POST['umt_div'][$group_key] as $div_key => $div_name)
						{
							if ($div_key !== -1 || $div_key !== "-1")
							{
								if ($div_name !== "")
								{
									$div = array();
									$div['name'] = $div_name;
									$div['id'] = $div_key;
									$group['div'][$div_name] = $div;
								}
							}
						}
						if (count($group)>0 && count($group['div'])>0)
						{
							$all_groups[$group['name']] = $group;
						}
					}
				}
			}
		}
		// debug save data
		//print_r($all_groups);
		if ($this->post_type !== NULL)
		{
			$option_name = $this->post_database_prefix . $this->post_type;
		}
		else if ($this->settings_type == 'options')
		{
			$option_name = $this->option_database_prefix;
		}
		else
		{
			$option_name = $this->settings_database_prefix . $this->settings_type . $this->settings_database_suffix;
		}
		update_option( $option_name, $all_groups );
		return true;
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_view
	*
	*	@author SilbinaryWolf
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_view()
	{
		if (isset($_REQUEST['posttype']) || isset($_REQUEST['options']) || isset($_REQUEST['settings']))
		{
			// Check if it's a post type page or options page
			if (isset($_REQUEST['posttype']))
			{
				$this->post_type = $_REQUEST['posttype'];
			}
			else if (isset($_REQUEST['options']))
			{
				$this->post_type = NULL;
				$this->settings_type = 'options';
			}
			else if (isset($_REQUEST['settings']))
			{
				$this->post_type = NULL;
				$this->settings_type = $_REQUEST['settings'];
			}
			$save = false;
			if (isset($_POST['umt_sent']))
			{
				$save = $this->admin_save();
			}
			$groups = $this->admin_load();
			include($this->dir . "/view/post.php");
		}
		else if (isset($_REQUEST['enable']) || isset($_REQUEST['disable']))
		{
			$slug = "<unknown>";
			$enable = true;
			
			if (isset($_REQUEST['enable']))
			{
				$slug = $_REQUEST['enable'];
				$enable = true;
				$this->extension_save($_REQUEST['enable'],true);
			}
			else if (isset($_REQUEST['disable']))
			{
				$slug = $_REQUEST['disable'];
				$enable = false;
				$this->extension_save($_REQUEST['disable'],false);
			}
			include($this->dir . "/view/extension-update.php");
		}
		else
		{
			include($this->dir . "/view/index.php");
		}
	}
}
?>