<?php
/*--------------------------------------------------------------------------
*
*	qTranslate Support
*	
*
*	@author SilbinaryWolf
* 
*-------------------------------------------------------------------------*/

class umt_qtranslate_support
{
	var $umt;

	function __construct($parent)
	{
		$this->umt = $parent;
		
		// setup initiation procedure
		add_action('init',array($this,'init'),99);
	}

	function init()
	{
		// setup qtranslate
		if (isset($q_config))
		{
			
		}
		else
		{
			trigger_error("ACF Qtranslate support for Ultimate Metabox Tabs has become broken. Please contact the developer. For now, disable the extension.");
		}
	}
}
?>