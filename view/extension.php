<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<div class="title-wrapper">
		<h2> <?php echo __('Ultimate Metabox Tabs','umt'); ?> </h2><p>v<?php echo $this->version; ?></p>
	</div>
	
	<div class="clearfix"></div>
	<h2 class="nav-tab-wrapper">
		<a href="<?php echo $this->menu_url; ?>" class="nav-tab"><?php echo __('General','umt'); ?></a>
		<a href="<?php echo add_query_arg('subpage', 'extension' , $this->menu_url); ?>" class="nav-tab nav-tab-active"><?php echo __('Extensions','umt'); ?></a>
		<a href="<?php echo add_query_arg('subpage', 'patcher' , $this->menu_url); ?>" class="nav-tab"><?php echo __('Patches','umt'); ?></a>
	</h2>
	
	<div class="extension-list">
		<div class="extension">
			<?php if (count($this->extensions)<=0) : ?>
			<h3><?php echo __('No extensions available. ACF Extensions will remain hidden unless ACF is active.','umt'); ?></h3>
			<?php endif; ?>
			<?php foreach ($this->extensions as $slug => $extension): ?>
			<?php $description_array = explode("\n",$extension['description']); ?>
			<?php $url = $extension['enabled'] ? $this->menu_url . "&disable=" . $slug : $this->menu_url . "&enable=" . $slug ; ?>
			<h3><?php echo $extension['name']; ?></h3>
			<div class="extension_description">
			<?php foreach ($description_array as $description) : ?>
			<p><?php echo $description; ?></p>
			<?php endforeach; ?>
			</div>
			<a href="<?php echo $url; ?>"><?php echo $extension['enabled'] ? 'Disable' : 'Enable' ; ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</div>