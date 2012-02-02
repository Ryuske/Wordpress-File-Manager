<?php
$settings = get_option('file_manager_settings');
?>
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
                    <th>File</th>
                    <th>Belts With Access</th>
                    <th>Programs With Access</th>
                </tr>
                <tr>
                    <td>3rd Brown Testing Sheet</td>
                    <td>Purple</td>
                    <td>Swat</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!--Categories page-->
    <div id="categories" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1 style="display: inline">Categories</h1> <h3 style="display: inline; position: relative; bottom: 1px;"><a href="#belts_programs" onclick="jQuery('#add_belt').dialog('open')"><span class="ui-icon ui-icon-plusthick" style="display: inline-block; vertical-align: text-top;"></span>Add</a></h3>
        <table class="file_manager_table">
            <tbody>
                <tr>
                    <th>Category</td>
                    <th>Sub-Categories</th>
                </tr>
                <tr>
                    <td>My Cat</td>
                    <td>Something, Koala</td>
                </tr>
                <tr>
                    <td>My Cat-&gt;Something</td>
                    <td>Hazah</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!--Settings page-->
    <div id="settings" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1>Settings</h1>
        <form id="update_settings" name="update_settings" action="options.php#settings" method="post">
            <?php settings_fields('file_manager_settings'); ?>
            <label>User Permissions (ma_accounts)</label>
            <input name="file_manager_settings[permissions][use]" type="checkbox" value="true" checked="checked" /> <br /><br />

            <label>Plugins Option Names (Used by User Permissions; Refer to plugin if you're unsure)</label> <br />
            <input style="width: 400px;" name="file_manager_settings[permissions][options_name]" type="text" value="ma_accounts_settings" /> <br /><br />

            <input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="submit" value="Save Changes" />
        </form>
    </div>

    <!--Help page-->
    <div id="help" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
        <h1>Help</h1>
        <p>Write this (mention how stuff on Settings page works).</p>
        <p>Check us out on GitHub to track the latest updates and releases: <a href="https://github.com/Ryuske/Wordpress-File-Manager" target="_blank">https://github.com/Ryuske/Wordpress-File-Manager</a>
    </div>

    <!--Start dialog HTML-->
    <?php /*
    <?php
    if (is_numeric($_GET['id']) && $_GET['action'] === 'update_account') {
        $id = (int) $_GET['id'];
        ?>
        <script type="text/javascript">jQuery(document).ready(function(){jQuery('#update_account').dialog('open')});</script>
        <?php
    }
    ?>
    <div id="update_account" title="Edit Account">
        <?php
        $total_users = count_users();
        $total_users = $total_users['total_users'];

        if ($id <= $total_users && $id > 0) {
            $account = get_userdata($id);
            $name = '';
            if (isset($account->nickname)) {
                $name = $account->nickname;
                if (isset($account->last_name)) {
                    $name .= ' ' . $account->last_name;
                }
            } else if (isset($account->first_name) && isset($account->last_name)) {
                $name = $account->first_name . ' ' . $account->last_name;
            } else {
                $name = $account->display_name;
            }

            if (get_user_meta($id, 'ma_accounts_programs', true) !== '') {
                $programs_array = explode(',', get_user_meta($id, 'ma_accounts_programs', true));
                $temp = array();
                foreach ($programs_array as $value) {
                    $temp[$value] = $value;
                }
            }
            $programs_array = $temp;
            unset($temp);
            ?>
            <h2 style="text-align: center"><?php esc_html_e($name); ?></h2>
            <form id="edit_account" action="options.php#accounts" method="post">
                <?php settings_fields('ma_accounts_settings'); ?>
                <input name="ma_accounts_settings[update_account]" type="hidden" value="<?php echo $id; ?>" />
                <label class="ma_accounts_label">Belt</label>
                <span>
                    <select name="ma_accounts_settings[belts]">
                        <?php
                        foreach ($settings['belts'] as $belt) {
                            echo ($belt['id'] == get_user_meta($id, 'ma_accounts_belt', true)) ? '<option value="' . esc_html($belt['id']) . '" selected="selected">' . esc_html($belt['name']) . '</option>' : '<option value="' . esc_html($belt['id']) . '">' . esc_html($belt['name']) . '</option>';
                        }
                        ?>
                    </select>
                </span> <br /><br />
                <label class="ma_accounts_label">VIP Programs</label> <br />
                <table>
                    <tbody>
                        <?php
                        foreach ($settings['programs'] as $program) {
                            ?>
                            <tr>
                            <td><?php esc_html_e($program['name']); ?></td>
                            <td><?php echo (isset($programs_array[$program['id']])) ? '<input name="ma_accounts_settings[programs][' . esc_html($program['id']) . ']" type="checkbox" value="' . esc_html($program['id']) . '" checked="checked" />' : '<input name="ma_accounts_settings[programs][' . esc_html($program['id']) . ']" type="checkbox" value="' . esc_html($program['id']) . '" />'; ?></td>
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

    <!--Dialog HTML for Belts and Special Programs-->
    <!--Add Belt Dialog-->
    <div id="add_belt" title="Add Belt">
        <form id="add_belt_form" action="options.php#belts_programs" method="post">
            <?php settings_fields('ma_accounts_settings'); ?>
            <label class="ma_accounts_label">Title</label> <br />
            <input id="belt" name="ma_accounts_settings[belts]" type="text" />
        </form>
        <div id="add_belt_notification" class="ui-state-error ui-corner-all ma_accounts_notification" style="display: none; margin-top: 10px;"><span class="ui-icon ui-icon-info" style="float: left;"></span>&nbsp;Your forgot to add a belt!</div>
    </div>

    <!--Add Program Dialog-->
   <div id="add_program" title="Add Program">
        <form id="add_program_form" action="options.php#belts_programs" method="post">
            <?php settings_fields('ma_accounts_settings'); ?>
            <label class="ma_accounts_label">Title</label> <br />
            <input id="program" name="ma_accounts_settings[programs]" type="text" />
        </form>
        <div id="add_program_notification" class="ui-state-error ui-corner-all ma_accounts_notification" style="display: none; margin-top: 10px;"><span class="ui-icon ui-icon-info" style="float: left;"></span>&nbsp;Your forgot to add a program!</div>
    </div>


    <!--Delete Belt Dialog-->
    <?php
    if (is_numeric($_GET['id']) && $_GET['action'] === 'delete_belt') {
        $id = (int) $_GET['id'];
        ?>
        <script type="text/javascript">jQuery(document).ready(function(){jQuery('#delete_belt').dialog('open')});</script>
        <?php
    }
    ?>
    <div id="delete_belt" title="Delete Belt" style="text-align: center;">
        <?php
        if ($id <= count($settings['belts']) && $id > -1 && $_GET['action'] === 'delete_belt') {
            ?>
            Are you sure you want to delete the belt: <br />
            <?php esc_html_e($settings['belts'][$id]['name']); ?>
            <form id="delete_belt_form" action="options.php#belts_programs" method="post">
                <?php settings_fields('ma_accounts_settings'); ?>
                <input type="hidden" name="_wp_http_referer" value="/wp-admin/plugins.php?page=ma_accounts&amp;action=delete_belt">
                <input name="ma_accounts_settings[belt_id]" type="hidden" value="<?php echo $id; ?>" />
            </form>
            <?php
        }
        ?>
    </div>

    <!--Delete Program Dialog-->
    <?php
    if (is_numeric($_GET['id']) && $_GET['action'] === 'delete_program') {
        $id = (int) $_GET['id'];
        ?>
        <script type="text/javascript">jQuery(document).ready(function(){jQuery('#delete_program').dialog('open')});</script>
        <?php
    }
    ?>
    <div id="delete_program" title="Delete Program" style="text-align: center;">
        <?php
        if ($_GET['id'] <= count($settings['programs']) && $_GET['id'] > -1 && $_GET['action'] === 'delete_program') {
            ?>
            Are you sure you want to delete the program: <br />
            <?php esc_html_e($settings['programs'][$id]['name']); ?>
            <form id="delete_program_form" action="options.php#belts_programs" method="post">
                <?php settings_fields('ma_accounts_settings'); ?>
                <input type="hidden" name="_wp_http_referer" value="/wp-admin/plugins.php?page=ma_accounts&amp;action=delete_program">
                <input name="ma_accounts_settings[program_id]" type="hidden" value="<?php echo $id; ?>" />
            </form>
            <?php
        }
        ?>
    </div>
</div>
