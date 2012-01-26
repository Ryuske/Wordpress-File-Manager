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

    function __construct() {
        add_filter('query_vars', array(&$this, 'add_query_vars'));
        //add_action('generate_rewrite_rules', array(&$this, 'add_rewrite_rules'));
        $this->get_attachments();
    } //End __construct

    public function add_rewrite_rules($rewrite) {
        //Need to add this.. Need to get rewriting working for pretty URLs
        $rewrite->rules = array('^(students)' => 'index.php?fm_attachment=koala') + $rewrite->rules;
        print_r($rewrite);
    } //End add_rewrite_rules

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
