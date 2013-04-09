<?php
	if (isset($_REQUEST['options']))
	{
		$post_info = array();
		$post_name = __('Global Options','umt');
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
	function umt_div($umt, $div, $group, $div_options)
	{
	?>
	<li class="div">
		<table class="umt editpage">
			<tbody>
				<tr>
					<td class="group_name">
					</td>
					<td class="div_name">
						<div class="td_content">
							<input type="text" name="umt_div[<?php echo $group['id']; ?>][<?php echo $div['id']; ?>]" size="30" tabindex="1" value="<?php echo $div['name']; ?>" class="metabox-divid" autocomplete="off">
							<?php if (count($div_options)>0): ?>
							<select class="post_divselect" selected="<?php echo $div['name']; ?>">
								<option name="none" value="">- <?php echo __('None'); ?> -</option>
								<?php foreach ($div_options as $optiongroup): ?>
								<optgroup label="<?php echo $optiongroup['name']; ?>">
									<?php foreach ($optiongroup['div'] as $optiondiv): ?>
									<?php 
										$class = isset($optiondiv['class']) ? ' class="'.$optiondiv["class"].'"' : "";
										$selected = $div['name'] == $optiondiv['value'] ? ' selected="selected"' : '';
									?>
									<option name="<?php echo $optiondiv['value']; ?>" value="<?php echo $optiondiv['value']; ?>" <?php echo $class . $selected; ?>><?php echo $optiondiv['name']; ?></option>
									<?php endforeach; ?>
								<?php endforeach; ?>
								</optgroup>
							</select>
							<?php endif; ?>
						</div>
					</td>
					<td class="div_remove">
						<div class="td_content">
							<a href="#" class="metabox-divremove">[x]</a>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
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
	function umt_group($umt, $group, $div_options)
	{
	?>
	<li class="group postbox" group-id="<?php echo $group['id']; ?>">
		<table class="umt editpage">
			<thead>
				<tr>
					<td class="group_name">
						<div class="td_content">
							<?php echo __('Tab Name','umt'); ?>
						</div>
					</td>
					<td class="div_name">
						<div class="td_content">
							<?php echo __('Div ID','umt'); ?>
						</div>
					</div>
					<td class="div_remove">
						<div class="td_content">
							<?php echo __('Delete'); ?>
						</div>
					</td>
				</tr>
			</thead>
		</table>
		<div class="information">
			<table class="umt editpage">
				<tbody>
					<tr>
						<td class="group_name">
							<div class="td_content">
								<input class="group_name_input" type="text" name="umt_group[<?php echo $group['id']; ?>]" size="30" tabindex="1" value="<?php echo $group['name']; ?>" id="title" autocomplete="off">
							</div>
						</td>
						<td class="div_name">
						</div>
						<td class="div_remove">
							<a href="#" class="metabox-groupremove">[x]</a>
						</td>
					</tr>
				</tbody>
			</table>
			<ul class="div_sort">
			<?php if (isset($group['div'])): ?>
			<?php foreach ($group['div'] as $div): ?>
			<?php umt_div($umt,$div,$group,$div_options); ?>
			<?php endforeach; ?>
			<?php endif; ?>
			</ul>
		</div>
		<div class="table_footer">
			<div class="col_2">
				<a class="metabox-lastdivid metabox-newdiv" href="#">+ <?php echo __('New Metabox','umt'); ?></a>
			</div>
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
	<noscript>
		<div id="message" class="updated below-h2">
			<p>
				<?php echo __('Javascript must be enabled to modify the Ultimate Metabox Tabs settings.','umt'); ?>
			</p>
		</div>
	</noscript>
	<div id="meta-data" style="display:none;">
		<div id="meta-newdiv">
			<?php umt_div($this,array('name' => '', 'id' => uniqid()),array('id' => uniqid()),$this->div_options); ?>
		</div>
		<div id="meta-newgroup">
			<?php umt_group($this,array('name' => '', 'id' => uniqid()),$this->div_options); ?>
		</div>
	</div>
	<div id="post-body">
		<form action="#" method="POST">
			<ul class="meta-group post-body-content">
				<?php foreach ($groups as $group): ?>
				<?php umt_group($this,$group,$this->div_options); ?>
				<?php endforeach; ?>
				<a id="metabox-lastgroup" class="metabox-newgroup" href="#">+ <?php echo __('Add New Tab Group','umt'); ?></a>
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