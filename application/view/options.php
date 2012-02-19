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
                    <th style="width: 350px;">Categories</th>
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
                array_walk($file_manager['generate_views']->attachments, function($attachment_value, $attachment_key) use($settings, $permissions_settings) {
                    $temp = explode(',', $settings['files'][$attachment_value->ID]['categories']);
                    array_walk($temp, function($temp_value, $temp_key) use(&$temp, $settings) {
                        $temp[$temp_key] = $settings['categories'][$temp_value]['name'];
                    });
                    $temp = implode(', ', $temp);
                    ?>
                    <tr>
                        <td><a href="plugins.php?page=file_manager&amp;id=<?php echo (int) $attachment_value->ID; ?>&amp;action=update_file#file_permissions"><span class="ui-icon ui-icon-pencil" style="position: relative; margin: 0 auto;"></span></a></td>
                        <td><?php esc_html_e($attachment_value->post_title); ?></td>
                        <td><?php esc_html_e($temp); ?></td>
                        <?php
                        if ($settings['permissions']['use']) {
                            $temp = array('belt_access' => explode(',', $settings['files'][$attachment_value->ID]['belt_access']), 'programs_access' => explode(',', $settings['files'][$attachment_value->ID]['programs_access']));

                            array_walk($temp['belt_access'], function($temp_value, $temp_key) use(&$temp, $permissions_settings) {
                                $temp['belt_access'][$temp_key] = $permissions_settings['belts'][$temp_value]['name'];
                            });
                            array_walk($temp['programs_access'], function($temp_value, $temp_key) use(&$temp, $permissions_settings) {
                                $temp['programs_access'][$temp_key] = $permissions_settings['programs'][$temp_value]['name'];
                            });

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
                });
                ?>
            </tbody>
        </table>
    </div>

    <!--Categories page-->
    <div id="categories" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1 style="display: inline">Categories</h1> <h3 style="display: inline; position: relative; bottom: 1px;"><a href="#categories" onclick="jQuery('#add_category').dialog('open')"><span class="ui-icon ui-icon-plusthick" style="display: inline-block; vertical-align: text-top;"></span>Add</a></h3><br /><br />
        <?php
        if (count($settings['categories']) <= 0) {
            echo '<div>You haven\'t added any categories yet! <a href="#categories" onclick="jQuery(\'#add_category\').dialog(\'open\')">Add</a> one now.</div>';
        } else {
            ?>
                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        jQuery('.accordion').each(function() {
                            if(jQuery.trim(jQuery(this).children().children().eq(1).children().eq(1).text()) == "") {
                                jQuery(this).children().children().eq(1).children().css("display", "block");
                            }
                        });
                    });
                </script>
            <div class="accordion">
                <?php
                $settings['categories'] = $file_manager['main']->sort_array_by_element($settings['categories'], 'name');
                array_walk($settings['categories'], function($category_value, $category_key) use($settings, $permissions_settings) {
                    if (!preg_match('/->/', $category_value['name'])) {
                        $programs_access = '';
                        $belt_access = '';

                        if ($settings['permissions']['use']) {
                            $belt_access = (!empty($permissions_settings['belts'][$category_value['belt_access']]['name'])) ? $permissions_settings['belts'][$category_value['belt_access']]['name']  : 'N/A';

                            $programs_access = explode(',', $category_value['programs_access']);
                            array_walk($programs_access, function($program_value, $program_key) use(&$programs_access, $permissions_settings) {
                                $programs_access[$program_key] = $permissions_settings['programs'][$program_value]['name'];
                            });
                            $programs_access = (!empty($programs_access[0])) ? implode(', ', $programs_access) : 'N/A';
                        }

                        ?>
                        <div>
                            <h3>
                            <a class="update" style="position: absolute; top: 7px; left: 19px;" href="plugins.php?page=file_manager&amp;id=<?php echo (int) $category_value['id']; ?>&amp;action=update_category#categories"><span class="ui-icon ui-icon-pencil"></span></a>
                            <a class="delete" style="position: absolute; top: 7px; left: 34px;" href="plugins.php?page=file_manager&amp;id=<?php echo (int) $category_value['id']; ?>&amp;action=delete_category#categories"><span class="ui-icon ui-icon-trash"></span></a>
                                <a class="accordion-href" href="#">
                                    <span style="padding-left: 35px;"><?php echo esc_html($category_value['name']); ?> </span>
                                    <?php echo ($settings['permissions']['use']) ? '<br /> Belt Access: ' . esc_html($belt_access)  . ' &bull; Programs Access: ' . esc_html($programs_access) : ''; ?>
                                </a>
                            </h3>
                            <div>
                            <div style="display: none">You haven't set any sub-categories! <a href="plugins.php?page=file_manager&amp;id=<?php echo (int) $category_value['id']; ?>&amp;action=add_subcategory">Add</a> one now.</div>
                                <?php
                                $sub_category_array = array();
                                $temp_category_info = array('base' => $category_value['name'], 'current' => '', 'display_name' => '', 'next' => '');
                                $current_level = 0;
                                $start_level = True;
                                $iterations = 0;

                                array_walk($settings['categories'], function($temp_category_value, $temp_category_key) use(&$sub_category_array, $category_value) {
                                    if (preg_match_all('/' . $category_value['name'] . '->/', $temp_category_value['name'], $matches)) {
                                        $sub_category_array[] = $temp_category_value;
                                    }
                                });



                                array_walk($sub_category_array, function($sub_category_value, $sub_category_key) use($sub_category_array, $temp_category_info, &$start_level, &$current_level, &$iterations, $settings, $permissions_settings) {
                                    $programs_access = '';
                                    $belt_access = '';

                                    if ($settings['permissions']['use']) {
                                        $belt_access = (!empty($permissions_settings['belts'][$sub_category_value['belt_access']]['name'])) ? $permissions_settings['belts'][$sub_category_value['belt_access']]['name']  : 'N/A';

                                        $programs_access = explode(',', $sub_category_value['programs_access']);
                                        array_walk($programs_access, function($program_value, $program_key) use(&$programs_access, $permissions_settings) {
                                            $programs_access[$program_key] = $permissions_settings['programs'][$program_value]['name'];
                                        });
                                        $programs_access = (!empty($programs_access[0])) ? implode(', ', $programs_access) : 'N/A';
                                    }

                                    $temp_category_info['current'] = $sub_category_value['name'];
                                    $display_name = array_reverse(explode('->', $temp_category_info['current']));
                                    $display_name = esc_html($display_name[0]);
                                    $display_name = '
                                    <a class="update" style="position: absolute; top: 7px; left: 19px;" href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=update_category#categories"><span class="ui-icon ui-icon-pencil"></span></a>
                                    <a class="delete" style="position: absolute; top: 7px; left: 34px;" href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=delete_category#categories"><span class="ui-icon ui-icon-trash"></span></a>
                                <a class="accordion-href" href="#">
                                    <span style="padding-left: 35px;">' . esc_html($display_name) . ' </span>';
                                    $display_name .= ($settings['permissions']['use']) ? '<br /> Belt Access: ' . esc_html($belt_access)  . ' &bull; Programs Access: ' . esc_html($programs_access) . '</a>' : '</a>';
                                    $display_empty_category_text = 'You haven\'t set any sub-categories! <a href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=add_subcategory">Add</a> one now.';
                                    $temp_next_array = explode('->', $temp_category_info['current']);
                                    array_pop($temp_next_array);
                                    $temp_category_info['next'] = (!empty($sub_category_array[$sub_category_key+1]['name'])) ? $sub_category_array[$sub_category_key+1]['name'] : implode('->', $temp_next_array);
                                    $iterations++;

                                    //Yes it is intetionally that there is two of these!
                                    if (True === $start_level) {
                                        $start_level = False;
                                        ?>
                                        <div class="accordion">
                                        <?php
                                    }

                                    if (!preg_match('/' . $temp_category_info['current'] . '/', $temp_category_info['next']) && (preg_match('/' . $temp_category_info['base'] . '/', $temp_category_info['next']) && $iterations < count($sub_category_array))) { //Stay at recursion level
                                        ?>
                                        <div>
                                            <h3><?php echo $display_name; ?></h3>
                                            <div><div style="display: none"><?php echo $display_empty_category_text; ?></div></div>
                                        </div>
                                        <?php
                                    } else if (preg_match('/' . $temp_category_info['current'] . '/', $temp_category_info['next'])) { //Go down a recursion level
                                        $start_level = True;
                                        $current_level++;
                                        $temp_category_info['base'] = $temp_category_info['current'];
                                        ?>
                                        <div>
                                            <h3><?php echo $display_name; ?></h3>
                                            <div><div style="display: none"><?php echo $display_empty_category_text; ?></div>
                                        <?php
                                    } else { //Go up a recursion level
                                        $current_level--;
                                        ?>
                                        </div></div></div>
                                        <div>
                                        <h3><?php echo $display_name; ?></h3>
                                        <div><div><?php echo $display_empty_category_text; ?></div>
                                        <?php
                                    }

                                    if (True === $start_level) {
                                        $start_level = False;
                                        ?>
                                        <div class="accordion">
                                        <?php
                                    }
                                });

                                if ($current_level > 0) {
                                    for ($i=0; $i<=$current_level; $i++) {
                                        echo '</div></div></div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                        if ($settings['permissions']['use']) {
                            $temp = explode(',', $category_value['programs_access']);
                            array_walk($temp, function($temp_value, $temp_key) use(&$temp, $permissions_settings) {
                                $temp[$temp_key] = $permissions_settings['programs'][$temp_value]['name'];
                            });
                            $temp = implode(', ', $temp);
                        }
                    }
                });
                ?>
            </div>
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
                        array_walk($settings['categories'], function($category_value, $category_key) use($settings, $id) {
                            $checked = '';
                            array_walk(explode(',', $settings['files'][$id]['categories']), function($file_value, $file_key) use(&$checked, $category_value) {
                                if ($file_value != '' && $file_value == $category_value['id']) {
                                    $checked = 'checked="checked"';
                                }
                            });
                            ?>
                            <tr>
                                <td><?php esc_html_e($category_value['name']); ?></td>
                                <td><input name="file_manager_settings[file_categories][<?php echo (int) $category_value['id']; ?>]" type="checkbox" value="<?php echo (int) $category_value['id']; ?>" <?php echo $checked; ?> /></td>
                            </tr>
                            <?php
                        });
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
                        array_walk($permissions_settings['belts'], function($belt_value, $belt_key) use($settings, $id) {
                            $selected = ((!empty($settings['files'][$id]['belt_access']) || $settings['files'][$id]['belt_access'] === '0') && $settings['files'][$id]['belt_access'] == $belt_value['id']) ? 'selected="selected"' : '';
                            echo '<option value="' . esc_html($belt_value['id']) . '" ' . $selected . '>' . esc_html($belt_value['name']) . '</option>';
                        });
                        ?>
                    </select> <br /><br />

                    <label class="file_manager_label">Programs With Access</label> <br />
                    <table>
                        <tbody>
                            <?php
                            $temp = (NULL !== $settings['files'][$id]['programs_access'] && False !== $settings['files'][$id]['programs_access']) ? explode(',', $settings['files'][$id]['programs_access']) : '';
                            array_walk($permissions_settings['programs'], function($program_value, $program_key) use($temp) {
                                $checked = (is_array($temp) && in_array($program_value['id'], $temp)) ? 'checked="checked"' : '';
                                ?>
                                <tr>
                                    <td><?php esc_html_e($program_value['name']); ?></td>
                                    <td><input name="file_manager_settings[programs][<?php echo (int) $program_value['id']; ?>]" type="checkbox" value="<?php echo (int) $program_value['id']; ?>" <?php echo $checked; ?> /></td>
                                </tr>
                                <?php
                            });
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
                    array_walk($permissions_settings['belts'], function($belt_value, $belt_key) {
                        echo '<option value="' . esc_html($belt_value['id']) . '">' . esc_html($belt_value['name']) . '</option>';
                    });
                    ?>
                </select><br /><br />

                <label class="file_manager_label">Programs With Access</label> <br />
                <table>
                    <tbody>
                        <?php
                        array_walk($permissions_settings['programs'], function($program_value, $program_key) {
                            ?>
                            <tr>
                                <td><?php esc_html_e($program_value['name']); ?></td>
                                <td><input name="file_manager_settings[programs][<?php echo (int) $program_value['id']; ?>]" type="checkbox" value="<?php echo (int) $program_value['id']; ?>" /></td>
                            </tr>
                            <?php
                        });
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
                        array_walk($settings['categories'], function($category_value, $category_key) use($settings, $id, $temp) {
                            if (preg_match('/' . $settings['categories'][$id]['name'] . '->/', $category_value['name'])) {
                            $checked = (is_array($temp) && in_array($category_value['id'], $temp)) ? 'checked="checked"' : '';
                            ?>
                            <tr>
                                <td><?php esc_html_e(substr($category_value['name'], strlen($settings['categories'][$id]['name'])+2)); ?></td>
                                <td><input name="file_manager_settings[sub_categories][<?php echo (int) $category_value['id']; ?>]" type="checkbox" value="<?php echo (int) $category_value['id']; ?>" <?php echo $checked; ?> /></td>
                            </tr>
                            <?php
                            }
                        });
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
                        array_walk($permissions_settings['belts'], function($belt_value, $belt_key) use($settings, $id) {
                            $selected = ('' != $settings['categories'][$id]['belt_access'] && $settings['categories'][$id]['belt_access'] == $belt_value['id']) ? 'selected="selected"' : '';
                            echo '<option value="' . esc_html($belt_value['id']) . '" ' . $selected . '>' . esc_html($belt_value['name']) . '</option>';
                        });
                        ?>
                    </select> <br /><br />

                    <label class="file_manager_label">Programs With Access</label> <br />
                    <table>
                        <tbody>
                            <?php
                            $temp = (NULL !== $settings['categories'][$id]['programs_access'] && False !== $settings['categories'][$id]['programs_access']) ? explode(',', $settings['categories'][$id]['programs_access']) : '';
                            array_walk($permissions_settings['programs'], function($program_value, $program_key) use($temp) {
                                $checked = (is_array($temp) && in_array($program_value['id'], $temp)) ? 'checked="checked"' : '';
                                ?>
                                <tr>
                                    <td><?php esc_html_e($program_value['name']); ?></td>
                                    <td><input name="file_manager_settings[programs][<?php echo (int) $program_value['id']; ?>]" type="checkbox" value="<?php echo (int) $program_value['id']; ?>" <?php echo $checked; ?> /></td>
                                </tr>
                                <?php
                            });
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
