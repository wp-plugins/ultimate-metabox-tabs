<?php
$post_types=get_post_types('','objects'); 
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<div class="title-wrapper">
		<h2> <?php echo __('Ultimate Metabox Tabs','umt'); ?> </h2><p>v<?php echo $this->version; ?></p>
	</div>
	
	<table class="wp-list-table widefat fixed posts" cellspacing="0">
	<thead>
		<tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th><th scope="col" id="title" class="manage-column column-title sortable desc" style=""><span><?php echo __('Title','umt'); ?></span></th>
			<th scope="col" id="date" class="manage-column column-date sortable asc" style=""><span><?php echo __('Slug','umt'); ?></span></th>	
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox"></th><th scope="col" class="manage-column column-title sortable desc" style=""><span><?php echo __('Title','umt'); ?></span></th><th scope="col" class="manage-column column-date sortable asc" style=""><span><?php echo __('Slug','umt'); ?></span></th>	
		</tr>
	</tfoot>

	<tbody id="the-list">
		<?php $posttype_url = add_query_arg('options', '1', $this->menu_url); ?>
		<tr valign="top">
			<th scope="row" class="check-column"></th>
			<td class="post-title page-title column-title">
				<strong>
					<a class="row-title" href="" title="Edit"><?php echo __('Global Options','umt'); ?></a>
				</strong>
				<div class="row-actions">
					<span class="edit"><a href="<?php echo $posttype_url; ?>" title="Edit this item"><?php echo __('Edit','umt'); ?></a></span>
				</div>
			</td>
			<td>
				<?php //echo $post_type->name; ?>
			</td>
		</tr>
		<?php foreach ($post_types as $post_type ): ?>
		<?php if ($this->post_types_ignored($post_type->name)) { continue; } ?>
		<?php
			$posttype_url = add_query_arg('posttype', $post_type->name, $this->menu_url);
		?>
		<tr valign="top">
			<th scope="row" class="check-column"></th>
			<td class="post-title page-title column-title">
				<strong>
					<a class="row-title" href="" title="Edit"><?php echo $post_type->label; ?></a>
				</strong>
				<div class="row-actions">
					<span class="edit"><a href="<?php echo $posttype_url; ?>" title="Edit this item"><?php echo __('Edit','umt'); ?></a></span>
				</div>
			</td>
			<td class="date-title column-author">
				<?php echo $post_type->name; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>