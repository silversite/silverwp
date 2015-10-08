<?php
if ( ! $is_compact ) {
	echo VP_View::instance()
	            ->load( 'control/template_control_head', $head_info );
}
?>

	<select name="<?php echo $name; ?>" class="vp-input vp-js-select2"
	        autocomplete="off">
		<?php if ( $show_empty ) : ?>
			<option></option>
		<?php endif; ?>
		<?php foreach ( $items as $item ): ?>
			<option <?php if ( $item->value == $value
			                   || $item->value == $default
			)
				echo 'selected="selected"' ?>
				value="<?php echo $item->value; ?>"><?php echo $item->label; ?></option>
		<?php endforeach; ?>
	</select>

<?php if ( ! $is_compact ) {
	echo VP_View::instance()->load( 'control/template_control_foot' );
} ?>