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
        <table class="file_manager_table" style="width: 80%;">
            <tbody>
                <tr>
                    <th></th>
                    <th>File</th>
                    <th style="width: 200px;">Categories</th>
                    <?php
                    if ($settings['permissions']['use']) {
                        ?>
                        <th>Belt Access</th>
                        <th>Programs Access</th>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                foreach ($file_manager->attachments as $key => $value) {
                    $temp = explode(',', $settings['files'][$value->ID]['categories']);
                    foreach ($temp as $temp_key => &$temp_value) {
                        $temp_value = $settings['categories'][$temp_value]['name'];
                    }
                    $temp = implode(', ', $temp);
                    ?>
                    <tr>
                        <td><a href="plugins.php?page=file_manager&amp;id=<?php echo (int) $value->ID; ?>&amp;action=update_file#file_permissions"><span class="ui-icon ui-icon-pencil" style="position: relative; margin: 0 auto;"></span></a></td>
                        <td><?php esc_html_e($value->post_title); ?></td>
                        <td><?php esc_html_e($temp); ?></td>
                        <?php
                        if ($settings['permissions']['use']) {
                            $temp = array('belt_access' => explode(',', $settings['files'][$value->ID]['belt_access']), 'programs_access' => explode(',', $settings['files'][$value->ID]['programs_access']));

                            foreach ($temp['belt_access'] as $temp_key => &$temp_value) {
                                $temp_value = $permissions_settings['belts'][$temp_value]['name'];
                            }
                            foreach ($temp['programs_access'] as $temp_key => &$temp_value) {
                                $temp_value = $permissions_settings['programs'][$temp_value]['name'];
                            }

                            $temp['belt_access'] = implode(', ', $temp['belt_access']);
                            $temp['programs_access'] = implode(', ', $temp['programs_access']);
                            ?>
                            <td><?php esc_html_e($temp['belt_access']); ?></td>
                            <td><?php esc_html_e($temp['programs_access']); ?></td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
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
            <table class="file_manager_table" style="width: 1000px;">
                <tbody>
                    <tr>
                        <th style="width: 15px;"></th>
                        <th style="width: 150px;">Category</th>
                        <th style="width: 100px;">Sub-Categories</th>
                        <?php
                        if ($settings['permissions']['use']) {
                            ?>
                            <th style="width: 50px;">Belt Access</th>
                            <th style="width: 90px;">Programs Access</th>
                            <?php
                        }
                        ?>
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
                            <?php
                            if ($settings['permissions']['use']) {
                                $temp = explode(',', $value['programs_access']);
                                foreach ($temp as $temp_key => &$temp_value) {
                                    $temp_value = $permissions_settings['programs'][$temp_value]['name'];
                                }
                                $temp = implode(', ', $temp);
                                ?>
                                <td><?php esc_html_e($permissions_settings['belts'][$value['belt_access']]['name']); ?></td>
                                <td><?php esc_html_e($temp); ?></td>
                                <?php
                            }
                            ?>
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
            <label>Use Permissions</label>
                <input name="file_manager_settings[permissions][use]" type="checkbox" value="true" <?php echo ($settings['permissions']['use']) ? 'checked="checked"' : ''; ?> /> <br /><br />

            <label>Plugins Option Names (Used by User Permissions; Refer to plugin if you're unsure)</label> <br />
            <input style="width: 400px;" name="file_manager_settings[permissions][options_name]" type="text" value="<?php esc_html_e($settings['permissions']['options_name']); ?>" /> <br /><br />

            <input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" value="Save Changes" />
        </form>
    </div>

    <!--Help page-->
    <div id="help" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1>Help</h1>
        <label class="file_manager_label">Files</label>
        <p>
            If you want to do anything with files (aside from viewing available ones), you'll have to enabled 'Use Permissions' in the 'Settings' page.
        </p>

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
    if (is_numeric($_GET['id']) && $_GET['action'] === 'update_file') {
        $id = (int) $_GET['id'];
        ?>
        <script type="text/javascript">jQuery(document).ready(function(){jQuery('#update_file').dialog('open')});</script>
        <?php
    }
    ?>
    <div id="update_file" title="Edit File">
        <?php
        if (array_key_exists($id, $file_manager->attachments) && $_GET['action'] === 'update_file') {
            ?>
            <h2 style="text-align: center"><?php esc_html_e($file_manager->attachments[$id]->post_title); ?></h2>
            <form id="edit_file" action="options.php#file_permissions" method="post">
                <?php settings_fields('file_manager_settings'); ?>
                <input name="file_manager_settings[file_id]" type="hidden" value="<?php echo (int) $id; ?>" />
                <input type="hidden" name="_wp_http_referer" value="/wp-admin/plugins.php?page=file_manager&amp;action=update_file">

                <label class="file_manager_label">Categories</label> <br />
                <table>
                    <tbody>
                        <?php
                        foreach ($settings['categories'] as $category_key => $category_value) {
                            $checked = '';
                            array_walk(explode(',', $settings['files'][$id]['categories']), function($file_value, $file_key) use(&$checked, $category_value) {
                                if ($file_value == $category_value['id']) {
                                    $checked = 'checked="checked"';
                                }
                            });
                            ?>
                            <tr>
                                <td><?php esc_html_e($category_value['name']); ?></td>
                                <td><input name="file_manager_settings[file_categories][<?php echo (int) $category_value['id']; ?>]" type="checkbox" value="<?php echo (int) $category_value['id']; ?>" <?php echo $checked; ?> /></td>
                            </tr>
                            <?php
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
                        foreach ($permissions_settings['belts'] as $key => $value) {
                            $selected = ((!empty($settings['files'][$id]['belt_access']) || $settings['files'][$id]['belt_access'] === '0') && $settings['files'][$id]['belt_access'] == $value['id']) ? 'selected="selected"' : '';
                            echo '<option value="' . esc_html($value['id']) . '" ' . $selected . '>' . esc_html($value['name']) . '</option>';
                        }
                        ?>
                    </select> <br /><br />

                    <label class="file_manager_label">Programs With Access</label> <br />
                    <table>
                        <tbody>
                            <?php
                            $temp = (NULL !== $settings['files'][$id]['programs_access'] && False !== $settings['files'][$id]['programs_access']) ? explode(',', $settings['files'][$id]['programs_access']) : '';
                            foreach ($permissions_settings['programs'] as $key => $value) {
                                $checked = (is_array($temp) && in_array($value['id'], $temp)) ? 'checked="checked"' : '';
                                ?>
                                <tr>
                                    <td><?php esc_html_e($value['name']); ?></td>
                                    <td><input name="file_manager_settings[programs][<?php echo (int) $value['id']; ?>]" type="checkbox" value="<?php echo (int) $value['id']; ?>" <?php echo $checked; ?> /></td>
                                </tr>
                                <?php
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

           <?php
            if ($settings['permissions']['use']) {
                ?>
                <hr />
                <label class="file_manager_label">Belts With Access</label> <br />
                <select name="file_manager_settings[belt]">
                    <option value="">None</option>
                    <option disabled="disabled">-----------------</option>
                    <?php
                    foreach ($permissions_settings['belts'] as $key => $value) {
                        echo '<option value="' . esc_html($value['id']) . '">' . esc_html($value['name']) . '</option>';
                    }
                    ?>
                </select><br /><br />

                <label class="file_manager_label">Programs With Access</label> <br />
                <table>
                    <tbody>
                        <?php
                        foreach ($permissions_settings['programs'] as $key => $value) {
                            ?>
                            <tr>
                                <td><?php esc_html_e($value['name']); ?></td>
                                <td><input name="file_manager_settings[programs][<?php echo (int) $value['id']; ?>]" type="checkbox" value="<?php echo (int) $value['id']; ?>" /></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
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
    <div id="update_category" title="Update Category">
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
                        $temp = (NULL !== $settings['categories'][$id]['sub_categories'] && False !== $settings['categories'][$id]['sub_categories']) ? explode(',', $settings['categories'][$id]['sub_categories']) : '';
                        foreach ($settings['categories'] as $key => $value) {
                            $checked = (is_array($temp) && in_array($value['id'], $temp)) ? 'checked="checked"' : '';
                            ?>
                            <tr>
                                <td><?php esc_html_e($value['name']); ?></td>
                                <td><input name="file_manager_settings[sub_categories][<?php echo (int) $value['id']; ?>]" type="checkbox" value="<?php echo (int) $value['id']; ?>" <?php echo $checked; ?> /></td>
                            </tr>
                            <?php
                        }
                        ?>
                    <tbody>
                </table>

                <?php
                if ($settings['permissions']['use']) {
                    ?>
                    <hr />
                    <label class="file_manager_label">Belts With Access</label> <br />
                    <select name="file_manager_settings[belt]">
                        <option value="">None</option>
                        <option disabled="disabled">-----------------</option>
                        <?php
                        foreach ($permissions_settings['belts'] as $key => $value) {
                            $selected = ('' != $settings['categories'][$id]['belt_access'] && $settings['categories'][$id]['belt_access'] == $value['id']) ? 'selected="selected"' : '';
                            echo '<option value="' . esc_html($value['id']) . '" ' . $selected . '>' . esc_html($value['name']) . '</option>';
                        }
                        ?>
                    </select> <br /><br />

                    <label class="file_manager_label">Programs With Access</label> <br />
                    <table>
                        <tbody>
                            <?php
                            $temp = (NULL !== $settings['categories'][$id]['programs_access'] && False !== $settings['categories'][$id]['programs_access']) ? explode(',', $settings['categories'][$id]['programs_access']) : '';
                            foreach ($permissions_settings['programs'] as $key => $value) {
                                $checked = (is_array($temp) && in_array($value['id'], $temp)) ? 'checked="checked"' : '';
                                ?>
                                <tr>
                                    <td><?php esc_html_e($value['name']); ?></td>
                                    <td><input name="file_manager_settings[programs][<?php echo (int) $value['id']; ?>]" type="checkbox" value="<?php echo (int) $value['id']; ?>" <?php echo $checked; ?> /></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                }
                ?>
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
