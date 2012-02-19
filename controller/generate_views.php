<?php
class generate_views extends file_manager {
    public $attachments, $current_attachment, $current_category, $parent_page;

    function __construct() {
        add_action('admin_menu', array(&$this, 'add_admin_menu'));

        $temp = get_option('ma_accounts_settings');
        $this->attachments = get_children('post_parent=' . get_page_by_title($temp['login_page'])->ID . '&post_type=attachment&orderby=title&order=ASC');
    } //End __construct

    public function add_admin_menu() {
        add_plugins_page('Manage file manager options. Integrated with MA Accounts.', 'File Manager', 'administrator', 'file_manager', array(&$this, 'render_backend'));
    }

    public function render_backend() {
        global $file_manager;
        if (current_user_can('administrator')) {
            wp_enqueue_style('black-tie');
            wp_enqueue_style('fileManagerStyle');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('fileManagerScript');

            $settings = get_option('file_manager_settings');
            if ($settings['permissions']['use']) {
                $permissions_settings = get_option($settings['permissions']['options_name']);
            }
            include parent::__basepath__ . '/application/view/options.php';
        }
    } //End render_backend

    public function file_func() {
        global $file_manager, $current_user;
        $settings = get_option('file_manager_settings');
        wp_enqueue_style('fileManagerStyle');

        if (!empty($file_manager['generate_views']->attachments[get_query_var('fm_attachment')]) || $file_manager['generate_views']->attachments[get_query_var('fm_attachment')] === 0) {
            wp_enqueue_script('fileManagerJwplayer');
            $file_manager['generate_views']->current_attachment = $file_manager['generate_views']->attachments[get_query_var('fm_attachment')];
             if ($file_manager['main']->check_permissions($current_user->ID, $settings['files'][$file_manager['generate_views']->current_attachment->ID]['belt_access'], $settings['files'][$file_manager['generate_views']->current_attachment->ID]['programs_access'])) {
                ob_start();
                include parent::__basepath__ . '/application/view/attachment.php';
                return ob_get_clean();
            } else {
                echo '<meta http-equiv="refresh" content="0;url=' . esc_html($_SERVER['HTTP_REFERER']) . '">';
            }
        } else {
            $file_manager['generate_views']->current_category = get_query_var('fm_category');
            ob_start();
            include parent::__basepath__ . '/application/view/category.php';
            return ob_get_clean();
        }
    } //End file_func
} //End generate_views
?>
