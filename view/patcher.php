<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<div class="title-wrapper">
		<h2> <?php echo __('Ultimate Metabox Tabs','umt'); ?> </h2><p>v<?php echo $this->version; ?></p>
	</div>
	<div class="clearfix"></div>
	<h2 class="nav-tab-wrapper">
		<a href="<?php echo $this->menu_url; ?>" class="nav-tab"><?php echo __('General','umt'); ?></a>
		<a href="<?php echo add_query_arg('subpage', 'extension' , $this->menu_url); ?>" class="nav-tab"><?php echo __('Extensions','umt'); ?></a>
		<a href="<?php echo add_query_arg('subpage', 'patcher' , $this->menu_url); ?>" class="nav-tab nav-tab-active"><?php echo __('Patches','umt'); ?></a>
	</h2>
	
	<?php if (isset($_REQUEST['apply_patch'])): ?>
	<?php $result = $this->patches[$_REQUEST['apply_patch']]->install(); ?>
	<div id="message" class="updated below-h2">
		<p>
		<?php if (is_string($result)) : ?>
		<?php echo __($result,"umt"); ?>
		<?php else: ?>
		<?php if ($result > 0) : ?>
		<?php echo __("Patch applied successfully.","umt"); ?>
		<?php else: ?>
		<?php echo __("An error occurred patching. Make sure you CMOD settings for wp-admin are writeable (777)","umt"); ?>
		<?php endif; ?>
		<?php endif; ?>
		</p>
	</div>
	<?php endif; ?>
	
	<?php if (isset($_REQUEST['uninstall_patch'])): ?>
	<?php $result = $this->patches[$_REQUEST['uninstall_patch']]->uninstall(); ?>
	<div id="message" class="updated below-h2">
		<p>
		<?php if (is_string($result)) : ?>
		<?php echo __($result,"umt"); ?>
		<?php else: ?>
		<?php if ($result > 0) : ?>
		<?php echo __("Patch uninstalled successfully.","umt"); ?>
		<?php else: ?>
		<?php echo __("An error occurred uninstalling. Make sure you CMOD settings for wp-admin are writeable (777)","umt"); ?>
		<?php endif; ?>
		<?php endif; ?>
		</p>
	</div>
	<?php endif; ?>
	
	<div class="extension-list patcher">
		<div class="extension">
			<?php if (count($this->patches)<=0) : ?>
			<h3><?php echo __('No patches available.','umt'); ?></h3>
			<?php endif; ?>
			<?php foreach ($this->patches as $slug => $patch): ?>
			<?php if ($patch->status === true) { $status_class = " ok"; } else if ($patch->status === false) { $status_class = " not_patched"; } else { $status_class = " error"; } ?>
			<?php
				if ($patch->status<=0)
				{
					$url = add_query_arg('subpage', 'patcher' , $this->menu_url);
					$url = add_query_arg('apply_patch', $slug , $url);
					$url_name = __("Apply Patch","umt");
				}
				else
				{
					$url = add_query_arg('subpage', 'patcher' , $this->menu_url);
					$url = add_query_arg('uninstall_patch', $slug , $url);
					$url_name = __("Uninstall Patch","umt");
				}
			?>
			<h3><?php echo $patch->name; ?></h3>
			<div class="extension_description">
			<p><?php echo str_replace('\n','<br/>',$patch->description); ?></p>
			</div>
			<div class="patch_status">
			<p><?php echo __("Patch Status:","umt"); ?></p><p class="status<?php echo $status_class; ?>"><?php if ($patch->status === true) { echo __('OK',"umt"); } else if ($patch->status === false) { echo __('NOT PATCHED',"umt"); } else { echo __('ERROR PATCHING',"umt"); } ?></p>
			</div>
			<a href="<?php echo $url; ?>"><?php echo $url_name; ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</div>