<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<div class="title-wrapper">
		<h2> <?php echo __('Ultimate Metabox Tabs','umt'); ?> </h2><p>v<?php echo $this->version; ?></p>
	</div>
	<div class="clearfix"></div>
	<div class="extension-list">
	<h3>Extension '<?php echo $slug; ?>' is now <?php echo $enable ? 'enabled' : 'disabled'; ?>.</h3>
	<a href="<?php echo add_query_arg('subpage', 'extension', $this->menu_url); ?>"><< <?php echo __('Go Back'); ?></a>
	</div>
</div>