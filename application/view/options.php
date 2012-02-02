<div id="option-tabs" style="clear:both; margin-right:20px;" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
            <a href="#file_permissions">
                <span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-note" style="float: left; margin-right: .3em;"></span></span>
                File Permissions
            </a>
        </li>
        <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
            <a href="#categories">
                <span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-folder-collapsed" style="float: left; margin-right: .3em;"></span></span>
                Categories
            </a>
        </li>
        <li class="ui-state-default ui-corner-top">
            <a href="#settings">
                <span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-gear" style="float: left; margin-right: .3em;"></span></span>
                Settings
            </a>
        </li>
        <li class="ui-state-default ui-corner-top"><a href="#help">Help</a></li>
    </ul>
    <!--End Navigation-->

    <!--File permissions page-->
    <div id="file_permissions" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1>File Permissions</h1>
        <table class="file_manager_table">
            <tbody>
                <tr>
                    <th></th>
                    <th>File</th>
                    <th>Belt Access</th>
                    <th>Programs Access</th>
                </tr>
                <tr>
                    <td><a href="plugins.php?page=file_manager&amp;id=1&amp;action=update_permissions#file_permissions"><span class="ui-icon ui-icon-pencil" style="position: relative; margin: 0 auto;"></span></a></td>
                    <td>3rd Brown Testing Sheet</td>
                    <td>Purple</td>
                    <td>Swat</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!--Categories page-->
    <div id="categories" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1 style="display: inline">Categories</h1> <h3 style="display: inline; position: relative; bottom: 1px;"><a href="#categories" onclick="jQuery('#add_category').dialog('open')"><span class="ui-icon ui-icon-plusthick" style="display: inline-block; vertical-align: text-top;"></span>Add</a></h3>
        <?php
        if (count($settings['categories']) <= 0) {
            echo '<div>You haven\'t added any categories yet! <a href="#categories" onclick="jQuery(\'#add_category\').dialog(\'open\')">Add</a> one now.</div>';
        } else {
            ?>
            <table class="file_manager_table">
                <tbody>
                    <tr>
                        <th></th>
                        <th>Category</td>
                        <th>Sub-Categories</th>
                    </tr>
                    <?php

                    $file_manager->sort_array_by_element($settings['categories'], 'name');
                    foreach ($settings['categories'] as $key => $value) {
                        $temp = explode(',', $value['sub_categories']);
                        foreach ($temp as $temp_key => &$temp_value) {
                            $temp_value = $settings['categories'][$temp_value]['name'];
                        }
                        $temp = implode(', ', $temp);
                        ?>
                        <tr>
                            <td>
                                <a href="plugins.php?page=file_manager&amp;id=<?php echo (int) $value['id']; ?>&amp;action=update_category#categories"><span class="ui-icon ui-icon-pencil" style="float: left"></span></a>
                                <a href="plugins.php?page=file_manager&amp;id=<?php echo (int) $value['id']; ?>&amp;action=delete_category#categories"><span class="ui-icon ui-icon-trash"></span></a>
                            </td>
                            <td><?php esc_html_e($value['name']); ?></td>
                            <td><?php esc_html_e($temp); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
        ?>
    </div>

    <!--Settings page-->
    <div id="settings" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1>Settings</h1>
        <form id="update_settings" name="update_settings" action="options.php#settings" method="post">
            <?php settings_fields('file_manager_settings'); ?>
            <label>User Permissions</label>
                <input name="file_manager_settings[permissions][use]" type="checkbox" value="true" <?php echo ($settings['permissions']['use']) ? 'checked="checked"' : ''; ?> /> <br /><br />

            <label>Plugins Option Names (Used by User Permissions; Refer to plugin if you're unsure)</label> <br />
            <input style="width: 400px;" name="file_manager_settings[permissions][options_name]" type="text" value="<?php esc_html_e($settings['permissions']['options_name']); ?>" /> <br /><br />

            <input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" value="Save Changes" />
        </form>
    </div>

    <!--Help page-->
    <div id="help" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1>Help</h1>
        <label class="file_manager_label">Categories</label>
        <p>
            When adding a category, if you want to add sub-categories to a sub-category you show it's a sub-category with a -&gt;. <br />
            For example: Files is a category and Audio is a sub-category to that, and you want to give Audio another sub-category of mp3.<br />
            It would look like this, <span style="font-style: italics;">Files-&gt;Audio</span>. You can continue this pattern as far as you want (i.e. Files-&gt;Audio-&gt;mp3-&gt;Under 1 Minute)
        </p>

        <label class="file_manager_label">Settings</label>
        <p>
            You can use permissions inherited from another plugin (currently the only supported plugin is <a href="$value['sub_categories']">Martial Arts Student Manager</a>). <br />
            All you have to do is check the box letting us know you want to use permissions, and then type in the options name the plugin is<br />
            using for it's options. This should be documented on the 3rd party plugin somewhere.
        </p>
        <p>Check us out on GitHub to track the latest updates and releases: <a href="https://github.com/Ryuske/Wordpress-File-Manager" target="_blank">https://github.com/Ryuske/Wordpress-File-Manager</a>
    </div>

    <!--Start dialog HTML-->
    <?php
    if (is_numeric($_GET['id']) && $_GET['action'] === 'update_permissions') {
        $id = (int) $_GET['id'];
        ?>
        <script type="text/javascript">jQuery(document).ready(function(){jQuery('#update_permissions').dialog('open')});</script>
        <?php
    }
    ?>
    <div id="update_permissions" title="Edit Permissions">
        <?php
        if ($id <= 2 && $id > 0) {
            ?>
            <h2 style="text-align: center">3rd Brown Testing Sheet</h2>
            <form id="edit_permissions" action="options.php#file_permissions" method="post">
                <?php settings_fields('file_manager_settings'); ?>
                <input name="file_manager_settings[update_account]" type="hidden" value="<?php echo $id; ?>" />
                <label class="file_manager_label">Belt</label>
                <span>
                    <select name="file_manager_settings[belts]">
                        <?php
                        foreach ($permissions_settings['belts'] as $belt) {
                            echo ($belt['id'] == get_user_meta($id, 'ma_accounts_belt', true)) ? '<option value="' . esc_html($belt['id']) . '" selected="selected">' . esc_html($belt['name']) . '</option>' : '<option value="' . esc_html($belt['id']) . '">' . esc_html($belt['name']) . '</option>';
                        }
                        ?>
                    </select>
                </span> <br /><br />
                <label class="file_manager_label">VIP Programs</label> <br />
                <table>
                    <tbody>
                        <?php
                        foreach ($permissions_settings['programs'] as $program) {
                            ?>
                            <tr>
                            <td><?php esc_html_e($program['name']); ?></td>
                            <td><?php echo (isset($programs_array[$program['id']])) ? '<input name="file_manager_settings[programs][' . esc_html($program['id']) . ']" type="checkbox" value="' . esc_html($program['id']) . '" checked="checked" />' : '<input name="file_manager_settings[programs][' . esc_html($program['id']) . ']" type="checkbox" value="' . esc_html($program['id']) . '" />'; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
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

            <label class="file_manager_label">Sub-Categories</label>
            <table>
                <tbody>
                    <?php
                    foreach ($settings['categories'] as $key => $value) {
                        ?>
                        <tr>
                            <td><?php esc_html_e($value['name']); ?></td>
                            <td><input name="file_manager_settings[sub_categories][<?php echo (int) $value['id']; ?>]" type="checkbox" value="<?php echo (int) $value['id']; ?>" /></td>
                        </tr>
                        <?php
                    }
                    ?>
                <tbody>
            </table>
        </form>
        <div id="add_category_notification" class="ui-state-error ui-corner-all ma_accounts_notification" style="display: none; margin-top: 10px;"><span class="ui-icon ui-icon-info" style="float: left;"></span>&nbsp;Your forgot to name your category!</div>
    </div>

    <!--Update Category Dialog-->
    <?php
    if (is_numeric($_GET['id']) && $_GET['action'] === 'update_category') {
        $id = (int) $_GET['id'];
        ?>
        <script type="text/javascript">jQuery(document).ready(function(){jQuery('#update_category').dialog('open')});</script>
        <?php
    }
    ?>
    <div id="update_category" title="Update Category" style="text-align: center;">
        <?php
        if (array_key_exists($id, $settings['categories']) && $_GET['action'] === 'update_category') {
            ?>
            <form id="update_category_form" action="options.php#categories" method="post">
                <?php settings_fields('file_manager_settings'); ?>
                <input type="hidden" name="_wp_http_referer" value="/wp-admin/plugins.php?page=file_manager&amp;action=update_category">
                <input name="file_manager_settings[category_id]" type="hidden" value="<?php echo $id; ?>" />

                <label class="file_manager_label">Name</label> <br />
                <input id="category" name="file_manager_settings[name]" type="text" value="<?php esc_html_e($settings['categories'][$id]['name']); ?>" /> <br /><br />

                <label class="file_manager_label">Sub-Categories</label>
                <table>
                    <tbody>
                        <?php
                        $temp = (!empty($settings['categories'][$id]['sub_categories'])) ? explode(',', $settings['categories'][$id]['sub_categories']) : '';
                        foreach ($settings['categories'] as $key => $value) {
                            ?>
                            <tr>
                                <td><?php esc_html_e($value['name']); ?></td>
                                <?php
                                if (is_array($temp) && in_array($value['id'], $temp)) {
                                    echo '<td><input name="file_manager_settings[sub_categories][' . (int) $value['id'] . ']" type="checkbox" value="' . (int) $value['id'] . '" checked="checked" /></td>';
                                } else {
                                    echo '<td><input name="file_manager_settings[sub_categories][' . (int) $value['id'] . ']" type="checkbox" value="' . (int) $value['id'] . '" /></td>';
                                }
                                ?>
                            </tr>
                            <?php
                        }
                        ?>
                    <tbody>
                </table>
            </form>
            <?php
        }
        ?>
    </div>

    <!--Delete Category Dialog-->
    <?php
    if (is_numeric($_GET['id']) && $_GET['action'] === 'delete_category') {
        $id = (int) $_GET['id'];
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
</div>
