<?php
	if (isset($_REQUEST['options']))
	{
		$post_info = array();
		$post_name = "Global Options";
	}
	else if (isset($_REQUEST['settings']))
	{
		$post_info = array();
		$post_name = $this->extensions[$_REQUEST['settings']]['name'];
	}	
	else
	{
		$post_info = get_post_type_object($this->post_type);
		$post_name = $post_info->labels->name;
	}

	/*--------------------------------------------------------------------------------------
	*
	*	umt_div
	*	The div id input
	*
	*	@author Jake Bentvelzen
	*	@since 0.0.5
	* 
	*-------------------------------------------------------------------------------------
	*/
	function umt_div($div, $group)
	{
	?>
	<li class="div">
		<label for="post_divid"><?php echo __('DIV ID','umt'); ?>:</label>
		<input type="text" name="umt_div[<?php echo $group['id']; ?>][<?php echo $div['id']; ?>]" size="30" tabindex="1" value="<?php echo $div['name']; ?>" class="metabox-divid" autocomplete="off">
		<a href="#" class="metabox-divremove">( - )</a>
		<div class="ui-div-sort"></div>
	</li>
	<?php
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	umt_group
	*	The group to sort the div id tags into.
	*
	*	@author Jake Bentvelzen
	*	@since 0.0.5
	* 
	*-------------------------------------------------------------------------------------
	*/
	function umt_group($group)
	{
	?>
	<li class="group postbox" group-id="<?php echo $group['id']; ?>">
		<div id="titlediv">
			<h3 class="hndle">
				<?php echo __('Group','umt'); ?>
				<a href="#" class="metabox-groupremove">[X]</a>
			</h3>
		</div>
		<div class="inside">
		<div class="group_name">
			<label for="post_divid"><?php echo __('Group Name','umt'); ?>:</label>
			<input type="text" name="umt_group[<?php echo $group['id']; ?>]" size="30" tabindex="1" value="<?php echo $group['name']; ?>" id="title" autocomplete="off">
		</div>
		<ul class="div_sort">
		<?php if (isset($group['div'])): ?>
		<?php foreach ($group['div'] as $div): ?>
		<?php umt_div($div,$group); ?>
		<?php endforeach; ?>
		<?php endif; ?>
		</ul>
		<a class="metabox-lastdivid metabox-newdiv" href="#">+ <?php echo __('Add New DIV ID','umt'); ?></a>
		</div>
	</li>
	<?php
	}
?>

<div id="metabox-editor" class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2><?php echo __('Ultimate Metabox Tabs','umt'); ?> - <?php echo $post_name; ?></h2>
	<?php if ($save > 0): ?>
	<div id="message" class="updated below-h2">
		<p>
		<?php echo __("Saved successfully.","umt"); ?>
		</p>
	</div>
	<?php endif; ?>
	<div id="meta-data" style="display:none;">
		<div id="meta-newdiv">
			<?php umt_div(array('name' => '', 'id' => uniqid()),array('id' => uniqid())); ?>
		</div>
		<div id="meta-newgroup">
			<?php umt_group(array('name' => '', 'id' => uniqid())); ?>
		</div>
	</div>
	<div id="post-body">
		<form action="#" method="POST">
			<ul class="meta-group post-body-content">
				<?php foreach ($groups as $group): ?>
				<?php umt_group($group); ?>
				<?php endforeach; ?>
				<a id="metabox-lastgroup" class="metabox-newgroup" href="#">+ <?php echo __('Add New Group','umt'); ?></a>
			</ul>
			<div id="postbox-save">
				<div id="submitdiv" class="postbox">
					<h3 class="hndle"><span><?php echo __('Save'); ?></span></h3>
					<div class="inside">
						<input name="updateoption" type="submit" class="submit-button" id="publish" tabindex="5" accesskey="p" value="<?php echo __('Update'); ?>">
						<?php echo wp_nonce_field( -1, "_wpnonce", true ,false ); ?>
						<input type="hidden" name="umt_sent" value="1">
					</div>
				</div>
			</div>
		</form>
	</div>
</div>