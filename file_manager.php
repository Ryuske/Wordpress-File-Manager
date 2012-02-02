<?php
/*
Plugin Name: File Manager
Description: Simple file manager, with custom permissions
Version: 1.0
Author: Kenyon Haliwell
License: GPL2
 */
class file_manager {
    public $attachments, $current_attachment;
    public $options = array();

    function __construct() {
        /*
         * Used for "pretty" urls
         */
        //add_filter('query_vars', array(&$this, 'add_query_vars'));
        //add_action('generate_rewrite_rules', array(&$this, 'add_rewrite_rules'));

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-dialog');
        wp_register_style('black-tie', plugins_url('application/view/css/jquery-ui.css', __FILE__));
        wp_register_style('fileManagerStyle', plugins_url('application/view/css/file_manager.css', __FILE__));
        wp_register_script('fileManagerScript', plugins_url('application/view/js/admin.js', __FILE__));

        //register_activation_hook(__FILE__, array(&$this, 'activate_plugin'));

        add_action('admin_menu', array(&$this, 'add_admin_menu'));
        add_action('admin_init', array(&$this, 'admin_settings'));

        $this->get_attachments();
    } //End __construct

    public function add_admin_menu() {
        add_plugins_page('Manage file manager options. Integrated with MA Accounts.', 'File Manager', 'administrator', 'file_manager', array(&$this, 'render_backend'));
    } //End add_admin_menu

    public function admin_settings() {
        $this->options = (!get_option('file_manager_settings')) ? $this->options : get_option('file_manager_settings');
        if (current_user_can('administrator')) {
            register_setting('file_manager_settings', 'file_manager_settings', array(&$this, 'validate_settings'));
        }
    } //End admin_settings

    public function validate_settings($input) {
    } //End validate_settings

    /*
     * Used for "pretty" urls
     */
    public function add_rewrite_rules($rewrite) {
        //Need to add this.. Need to get rewriting working for pretty URLs
        $rewrite->rules = array('^(students)' => 'index.php?fm_attachment=koala') + $rewrite->rules;
        print_r($rewrite);
    } //End add_rewrite_rules

    /*
     * Used for "pretty" urls
     */
    public function add_query_vars($qvars) {
        $qvars[] = 'fm_attachment';
        return $qvars;
    } //End add_query_vars

    private function get_attachments() {
        $current_page = get_page_by_title('Students-LoggedIn');
        $this->attachments = get_children('post_parent=' . $current_page->ID . '&post_type=attachment');
    } //End get_attachments

    public function return_attachments() {
        $return = '';
        foreach ($this->attachments as $attachment) {
            switch ($attachment->post_mime_type) {
                case 'image/jpeg':
                    $type = 'Image';
                    break;
                case 'audio/mpeg':
                    $type = 'Audio';
                    break;
                case 'video/mpeg':
                    $type = 'Video';
                    break;
                default:
                    $type = 'Text';
            }
            $return .= '
                <tr>
                <td class="td"><a href="?fm_attachment=' . $attachment->ID . '">' . $attachment->post_title . '</a></td>
                <td class="td">' . $type . '</td>
                </tr>
                ';
        }
        return $return;
    } //End return_attachments

    public function render_backend() {
        if (current_user_can('administrator')) {
            wp_enqueue_style('black-tie');
            wp_enqueue_style('fileManagerStyle');
            wp_enqueue_script('fileManagerScript');
            include dirname(__FILE__) . '/application/view/options.php';
        }
    } //End render_backend

    public function file_func() {
        global $file_manager;
        if (!empty($file_manager->attachments[get_query_var('fm_attachment')])) {
            $file_manager->current_attachment = $file_manager->attachments[get_query_var('fm_attachment')];
            return include dirname(__FILE__) . '/application/view/attachment.php';
        } else {
            return include dirname(__FILE__) . '/application/view/main.php';
        }
    }
} //End file_manager

$file_manager = new file_manager;
add_shortcode('file', array('file_manager', 'file_func'));
?>
