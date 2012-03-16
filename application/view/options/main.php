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
    <?php require_once(__DIR__ . '/dialogs.php'); ?>
</div>
