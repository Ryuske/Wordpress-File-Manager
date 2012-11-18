<div id="option-tabs" style="clear:both; margin-right:20px;" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
            <a href="#file_permissions">
                <span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-note" style="float: left; margin-right: .3em;"></span></span>
                File Permissions
            </a>
        </li>
        <li class="ui-state-default ui-corner-top">
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
    <?php require_once(__DIR__ . '/file_permissions.php'); ?>

    <!--Categories page-->
    <?php require_once(__DIR__ . '/categories.php'); ?>

    <!--Settings page-->
    <div id="settings" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1>Settings</h1>
        <form id="update_settings" name="update_settings" action="options.php#settings" method="post">
            <?php settings_fields('file_manager_settings'); ?>
            <label>Attachment Page</label> <br />
                <select name="file_manager_settings[attachment_page]">
                    <option value="unset"></option>
                    <?php
                    $pages = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'post_title', 'post_type' => 'page', 'post_status' => 'publish,private,draft'));
                    if (is_array($pages)) {
                        array_walk($pages, function($page_value, $page_key) use($settings) {
                            echo '<option value="' . (int) $page_value->ID . '" ' . (($settings['attachment_page'] == $page_value->ID) ? 'selected="selected"' : '') . '>' . esc_html($page_value->post_title) . '</option>';
                        });
                    }
                    ?>
                </select> <br /><br />

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
        <p>
            <h2>Files</h2>
            <h5 style="line-height: 0;">Adding Files</h5>
                <ol>
                    <li>First upload the file under <a href="<?php echo admin_url(); ?>media-new.php" target="_blank">Media</a></li>
                    <li>You will now be asked to enter information</li>
                    <li>Click the <i>Save all changes</i> button</li>
                    <li>
                        You'll be redirected to a listing of all uploaded files.
                        <ol>
                            <li>Find the file you just uploaded</li>
                            <li>Click the blue <i>Attach</i> link</li>
                            <li>In the search box, type <i>Students-LoggedIn</i></i>
                            <li>You *must* click the Pages radio</li>
                            <li>Click the <i>Search</i> button</li>
                            <li>Click the radio in-front of the <i>Students-LoggedIn</i> listing</li>
                            <li>Click the <i>Select</i> button</li>
                        </ol>
                    </li>
                    <li>It can now be viewed under the <i>File Permissions</i> tab</li>
                </ol>
            <h5 style="line-height: 0; margin-top: 25px;">Modiying Files</h5>
                <ol>
                    <li>
                        Modifying meta-information
                        <ol>
                            <li>Go to the <a href="<?php echo admin_url(); ?>upload.php" target="_blank">WordPress Media</a> listings</li>
                            <li>Locate the file you want to modify</li>
                            <li>Hover over the file's listings, and click the blue <i>Edit</i> link</li>
                            <li>Update the information accordingly</li>
                            <li>Click the <i>Update Media</i> button</li>
                        </ol>
                    </li>
                    <li>
                        Modifying Category &amp; Permissions
                        <ol>
                            <li>Go to the <i>File Permissions</i> tab</li>
                            <li>Locate the file you want to modify</li>
                            <li>Click the <span class="ui-icon ui-icon-pencil" style="display: inline-block; position: relative; top: 3px;"></span> icon next to the file</li>
                            <li>A dialog will now appear; Edit information accordingly</li>
                            <li>Click the <i>Update</i> button</li>
                        </ol>
                    </li>
                </ol>
            <h5 style="line-height: 0; margin-top: 25px;">Remove Files</h5>
                <ol>
                    <li>Go to the <a href="" target="_blank">WordPress Media</a> listings</li>
                    <li>Locate the file you want to remove</li>
                    <li>Hover over the file's listings, and click the red <i>Delete Permanently</i> link</li>
                    <li>Click <i>OK</i> in the dialog box that pops up</li>
                </ol>
            For information on applying access restrictions to files, please refer to <a href="#help_settings">Settings</a>.
        </p><br />

        <p>
            <h2>Categories</h2>
            <h5 style="line-height: 0;">Adding Categories</h5>
                <ol>
                    <li>Go to the <i>Categories</i> tab</li>
                    <li>
                        Adding a main category
                        <ol>
                            <li>Click on <span class="ui-icon ui-icon-plusthick" style="display: inline-block; position: relative; top: 3px;"></span><b><u>Add</u></b></li>
                            <li>Enter the information in the popup dialog</li>
                            <li>Click the <i>Add</i> button</li>
                        </ol>
                    </li>
                    <li>
                        Adding a sub-category
                        <ol>
                            <li>Locate the category you want your sub-category to be listed under</li>
                            <li>Click the <span class="ui-icon ui-icon-plusthick" style="display: inline-block; position: relative; top: 3px;"></span> icon that is next to the category you want to list under</li>
                            <li>Enter the information in the popup dialog</li>
                            <li>Click the <i>Add</i> button</li>
                        </ol>
                    </li>
                </ol>
            <h5 style="line-height: 0; margin-top: 25px;">Modifying Categories</h5>
                <ol>
                    <li>Go to the <i>Categories</i> tab</li>
                    <li>Locate the category you want to modify</li>
                    <li>Click the <span class="ui-icon ui-icon-pencil" style="display: inline-block; position: relative; top: 3px;"></span> icon next to the categories name</li>
                    <li>Enter the information in the dialog box</li>
                    <li>Click the <i>Update</i> button</li>
                </ol>
            <h5 style="line-height: 0; margin-top: 25px;">Removing Categories</h5>
                <ol>
                    <li>Go to the <i>Categories</i> tab</li>
                    <li>Locate the category you want remove</li>
                    <li>Click the <span class="ui-icon ui-icon-trash" span="display: inline-block; position: relative; top: 3px;"></span> icon next to the categories name</li>
                    <li>In the dialog that pops up click the <i>Delete</i> button</li>
                </ol>
            For information on applying access restrictions to categories, please refer to <a href="#help_settings">Settings</a>.
        </p><br />

        <p>
            <h2><a name="help_settings">Settings</a></h2>
            <table class="file_manager_help_table">
                <tbody>
                    <tr>
                        <td>Use Permissions</td>
                        <td>This is used to enable the use of permissions (access restriction)</td>
                    </tr>
                    <tr>
                        <td>Plugins Options Name</td>
                        <td>This is used to integrate permissions plugin you want to use.<br />Please refer to the plugin you want to use to find out what goes here.<br />Currently the only supported plugin is <a href="https://github.com/Ryuske/Wordpress-Martial-Arts-Student-Manager" target="_blank">Martial Arts Accounts Manager</a></td>
                    </td>
                </tbody>
            </table>
        </p>
        <p>Check us out on GitHub to track the latest updates and releases: <a href="https://github.com/Ryuske/Wordpress-File-Manager" target="_blank">https://github.com/Ryuske/Wordpress-File-Manager</a>
    </div>

    <!--Start dialog HTML-->
    <?php require_once(__DIR__ . '/dialogs.php'); ?>
</div>
