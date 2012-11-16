<!--Update File dialog-->
<?php
if (is_numeric($_GET['id']) && $_GET['action'] === 'update_file') {
    $id = (int) $_GET['id'];
    ?>
    <script type="text/javascript">jQuery(document).ready(function(){jQuery('#update_file').dialog('open')});</script>
    <?php
}
?>
<div id="update_file" title="Edit File">
    <?php
    if (array_key_exists($id, $file_manager['generate_views']->attachments) && $_GET['action'] === 'update_file') {
	?>
	<h2 style="text-align: center"><?php esc_html_e($file_manager['generate_views']->attachments[$id]->post_title); ?></h2>
	<form id="edit_file" action="options.php#file_permissions" method="post">
	    <?php settings_fields('file_manager_settings'); ?>
	    <input name="file_manager_settings[file_id]" type="hidden" value="<?php echo (int) $id; ?>" />
	    <input type="hidden" name="_wp_http_referer" value="/wp-admin/plugins.php?page=file_manager&amp;action=update_file">

	    <label class="file_manager_label">Categories</label> <br />
	    <table>
		<tbody>
		    <?php
            if (!empty($settings['categories'])) {
                array_walk($settings['categories'], function($category_value, $category_key) use($settings, $id) {
                    $checked = '';
                    if (!empty($settings['files'][$id]['categories'])) {
                        array_walk(explode(',', $settings['files'][$id]['categories']), function($file_value, $file_key) use(&$checked, $category_value) {
                            if ($file_value != '' && $file_value == $category_value['id']) {
                                $checked = 'checked="checked"';
                            }
                        });
                    }
                    ?>
                    <tr>
                        <td><?php esc_html_e($category_value['name']); ?></td>
                        <td><input name="file_manager_settings[file_categories][<?php echo (int) $category_value['id']; ?>]" type="checkbox" value="<?php echo (int) $category_value['id']; ?>" <?php echo $checked; ?> /></td>
                    </tr>
                    <?php
                });
            }
		    ?>
		</tbody>
	    </table>

	    <?php if ($settings['permissions']['use']) { ?>
		<hr />
		<label class="file_manager_label">Belts With Access</label> <br />
		<select name="file_manager_settings[belt]">
		    <option value="">None</option>
		    <option disabled="disabled">-----------------</option>
            <?php
            if (!empty($permissions_settings['belts'])) {
                array_walk($permissions_settings['belts'], function($belt_value, $belt_key) use($settings, $id) {
                    $selected = ((!empty($settings['files'][$id]['belt_access']) || $settings['files'][$id]['belt_access'] === '0') && $settings['files'][$id]['belt_access'] == $belt_value['id']) ? 'selected="selected"' : '';
                    echo '<option value="' . esc_html($belt_value['id']) . '" ' . $selected . '>' . esc_html($belt_value['name']) . '</option>';
                });
            }
		    ?>
		</select> <br /><br />

		<label class="file_manager_label">Programs With Access</label> <br />
		<table>
		    <tbody>
			<?php
			$temp = (NULL !== $settings['files'][$id]['programs_access'] && False !== $settings['files'][$id]['programs_access']) ? explode(',', $settings['files'][$id]['programs_access']) : '';
            if (!empty($permissions_settings['programs'])) {
                array_walk($permissions_settings['programs'], function($program_value, $program_key) use($temp) {
                    $checked = (is_array($temp) && in_array($program_value['id'], $temp)) ? 'checked="checked"' : '';
                    ?>
                    <tr>
                    <td><?php esc_html_e($program_value['name']); ?></td>
                    <td><input name="file_manager_settings[programs][<?php echo (int) $program_value['id']; ?>]" type="checkbox" value="<?php echo (int) $program_value['id']; ?>" <?php echo $checked; ?> /></td>
                    </tr>
                    <?php
                });
            }
			?>
		    </tbody>
		</table>
	    <?php } ?>
	</form>
	<?php
    }
    ?>
</div>

<!--Dialog HTML for Categories-->
<!--Add Category Dialog-->
<div id="add_category" title="Add Cateory">
    <form id="add_category_form" action="options.php#categories" method="post">
        <?php settings_fields('file_manager_settings'); ?>
        <label class="file_manager_label">Name</label> <br />
        <input id="category" name="file_manager_settings[name]" type="text" /> <br /><br />

        <?php
        if ($settings['permissions']['use']) {
            ?>
            <hr />
            <label class="file_manager_label">Belts With Access</label> <br />
            <select name="file_manager_settings[belt]">
                <option value="">None</option>
                <option disabled="disabled">-----------------</option>
                <?php
                if (!empty($permissions_settings['belts'])) {
                    array_walk($permissions_settings['belts'], function($belt_value, $belt_key) {
                        echo '<option value="' . esc_html($belt_value['id']) . '">' . esc_html($belt_value['name']) . '</option>';
                    });
                }
                ?>
            </select><br /><br />

            <label class="file_manager_label">Programs With Access</label> <br />
            <table>
                <tbody>
                    <?php
                    if (!empty($permissions_settings['programs'])) {
                        array_walk($permissions_settings['programs'], function($program_value, $program_key) {
                            ?>
                            <tr>
                                <td><?php esc_html_e($program_value['name']); ?></td>
                                <td><input name="file_manager_settings[programs][<?php echo (int) $program_value['id']; ?>]" type="checkbox" value="<?php echo (int) $program_value['id']; ?>" /></td>
                            </tr>
                            <?php
                        });
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
        ?>
    </form>
    <div id="add_category_notification" class="ui-state-error ui-corner-all file_manager_notification" style="display: none; margin-top: 10px;"><span class="ui-icon ui-icon-info" style="float: left;"></span>&nbsp;Your forgot to name your category!</div>
</div>

<!--Add Sub-Category Dialog-->
<?php
if (isset($_GET['id']) && $_GET['action'] === 'add_subcategory') {
    $id = $_GET['id'];
    ?>
    <Script type="text/javascript">jQuery(document).ready(function(){jQuery('#add_subcategory').dialog('open')});</script>
    <?php
}
?>
<div id="add_subcategory" title="Add Sub-Category">
    <?php
    $true_id = false;
    if (!empty($settings['categories'])) {
        array_walk_recursive($settings['categories'], function($category_value, $category_key) use($id, &$true_id) {
            if ($category_value == $id) {
                $true_id = true;
            }
        });
    }

    if ($true_id === true && $_GET['action'] === 'add_subcategory') {
        ?>
        <form id="add_subcategory_form" action="options.php#categories" method="post">
            <?php settings_fields('file_manager_settings'); ?>
            <input name="_wp_http_referer" type="hidden" value="/wp-admin/plugins.php?page=file_manager&amp;action=add_subcategory" />
            <input name="file_manager_settings[category_action]" type="hidden" value="subcategory" />
            <input name="file_manager_settings[category_id]" type="hidden" value="<?php echo $id; ?>" />

            <label class="file_manager_label">Name</label> <br />
            <input id="category" name="file_manager_settings[name]" type="text" /><br /><br />

            <?php
            if ($settings['permissions']['use']) {
                ?>
                <hr />
                <label class="file_manager_label">Belts With Access</label> <br />
                <select name="file_manager_settings[belt]">
                    <option value="">None</option>
                    <option disabled="disabled">-----------------</option>
                    <?php
                    if (!empty($permissions_settings['belts'])) {
                        array_walk($permissions_settings['belts'], function($belt_value, $belt_key) use($settings, $id) {
                            echo '<option value="' . esc_html($belt_value['id']) . '">' . esc_html($belt_value['name']) . '</option>';
                        });
                    }
                    ?>
                </select> <br /><br />

                <label class="file_manager_label">Programs With Access</label> <br />
                <table>
                    <tbody>
                    <?php
                    if (!empty($permissions_settings['programs'])) {
                        array_walk($permissions_settings['programs'], function($program_value, $program_key) use($temp) {
                            ?>
                            <tr>
                            <td><?php esc_html_e($program_value['name']); ?></td>
                            <td><input name="file_manager_settings[programs][<?php echo (int) $program_value['id']; ?>]" type="checkbox" value="<?php echo (int) $program_value['id']; ?>" /></td>
                            </tr>
                            <?php
                        });
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </form>
        <div id="add_subcategory_notification" class="ui-state-error ui-corner-all file_manager_notification" style="display: none; margin-top: 10px;"><span class="ui-icon ui-icon-info" style="float: left;"></span>&nbsp;You forgot to name your category!</div>
        <?php
    }
    ?>
</div>

<!--Update Category Dialog-->
<?php
if (isset($_GET['id']) && $_GET['action'] === 'update_category') {
    $id = $_GET['id'];
    ?>
    <script type="text/javascript">jQuery(document).ready(function(){jQuery('#update_category').dialog('open')});</script>
    <?php
}
?>
<div id="update_category" title="Update Category">
    <?php
    if (array_key_exists($id, $settings['categories']) && $_GET['action'] === 'update_category') {
        $display_name = array_reverse(explode('->', $settings['categories'][$id]['name']));
        $display_name = $display_name[0];
        ?>
        <form id="update_category_form" action="options.php#categories" method="post">
            <?php settings_fields('file_manager_settings'); ?>
            <input name="_wp_http_referer" type="hidden" value="/wp-admin/plugins.php?page=file_manager&amp;action=update_category">
            <input name="file_manager_settings[category_action]" type="hidden" value="update" />
            <input name="file_manager_settings[category_id]" type="hidden" value="<?php echo $id; ?>" />

            <label class="file_manager_label">Name</label> <br />
            <input id="category" name="file_manager_settings[name]" type="text" value="<?php esc_html_e($display_name); ?>" /> <br /><br />

            <?php
            if ($settings['permissions']['use']) {
                ?>
                <hr />
                <label class="file_manager_label">Belts With Access</label> <br />
                <select name="file_manager_settings[belt]">
                    <option value="">None</option>
                    <option disabled="disabled">-----------------</option>
                    <?php
                    if (!empty($permissions_settings['belts'])) {
                        array_walk($permissions_settings['belts'], function($belt_value, $belt_key) use($settings, $id) {
                            $selected = ('' !== $settings['categories'][$id]['belt_access'] && $settings['categories'][$id]['belt_access'] == $belt_value['id']) ? 'selected="selected"' : '';
                            echo '<option value="' . esc_html($belt_value['id']) . '" ' . $selected . '>' . esc_html($belt_value['name']) . '</option>';
                        });
                    }
                    ?>
                </select> <br /><br />

                <label class="file_manager_label">Programs With Access</label> <br />
                <table>
                    <tbody>
                    <?php
                    $temp = (!empty($settings['categories'][$id]['programs_access']) || 0 === $settings['categories'][$id]['programs_access']) ? explode(',', $settings['categories'][$id]['programs_access']) : '';
                    if (!empty($permissions_settings['programs'])) {
                        array_walk($permissions_settings['programs'], function($program_value, $program_key) use($temp) {
                            $checked = (is_array($temp) && in_array($program_value['id'], $temp)) ? 'checked="checked"' : '';
                            ?>
                            <tr>
                            <td><?php esc_html_e($program_value['name']); ?></td>
                            <td><input name="file_manager_settings[programs][<?php echo (int) $program_value['id']; ?>]" type="checkbox" value="<?php echo (int) $program_value['id']; ?>" <?php echo $checked; ?> /></td>
                            </tr>
                            <?php
                        });
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </form>
        <div id="update_category_notification" class="ui-state-error ui-corner-all file_manager_notification" style="display: none; margin-top: 10px;"><span class="ui-icon ui-icon-info" style="float: left;"></span>&nbsp;Your forgot to name your category!</div>
        <?php
    }
    ?>
</div>

<!--Delete Category Dialog-->
<?php
if (isset($_GET['id']) && $_GET['action'] === 'delete_category') {
    $id = $_GET['id'];
    ?>
    <script type="text/javascript">jQuery(document).ready(function(){jQuery('#delete_category').dialog('open')});</script>
    <?php
}
?>
<div id="delete_category" title="Delete Category" style="text-align: center;">
    <?php
    if (array_key_exists($id, $settings['categories']) && $_GET['action'] === 'delete_category') {
	?>
	Are you sure you want to delete the category: <br />
	<?php esc_html_e($settings['categories'][$id]['name']); ?>
	<form id="delete_category_form" action="options.php#categories" method="post">
	    <?php settings_fields('file_manager_settings'); ?>
	    <input type="hidden" name="_wp_http_referer" value="/wp-admin/plugins.php?page=file_manager&amp;action=delete_category">
	    <input name="file_manager_settings[category_id]" type="hidden" value="<?php echo $id; ?>" />
	</form>
	<?php
    }
    ?>
</div>
