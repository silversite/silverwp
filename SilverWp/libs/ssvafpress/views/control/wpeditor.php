<?php

if (!$is_compact) {
    echo VP_View::instance()->load('control/template_control_head', $head_info);
}

// prepare value for tinyMCE editor
$value = \html_entity_decode($value, ENT_COMPAT, 'UTF-8');
if (\has_filter('the_editor_content')) {
    $value = \apply_filters('the_editor_content', $value);
} else {
    $value = \format_for_editor($value);
}
?>
<div class="customEditor">
    
<?php 
if ($is_media_button) :
    ?>    
    <div class="wp-editor-tools">
        <div class="custom_upload_buttons hide-if-no-js wp-media-buttons"><?php do_action('media_buttons'); ?></div>
    </div>
    <?php
endif;
?>
    <textarea class="vp-input vp-js-wpeditor" id="<?php echo $name . '_ce'; ?>" data-vp-opt="<?php echo $opt; ?>" rows="10" cols="50" name="<?php echo $name; ?>" rows="3">
        <?php echo $value; ?>
    </textarea>
</div>

<?php 
if (!$is_compact) {
    echo VP_View::instance()->load('control/template_control_foot');
}
