<?php
/*
Plugin Name: File Manager
Description: Simple file manager, with custom permissions (To be used with PHP >=5.3)
Version: 1.5
Author: Kenyon Haliwell
License: GPL2
 */

require_once(__DIR__ . '/controller/update_settings.php');
require_once(__DIR__ . '/controller/generate_views.php');

class file_manager {
    const __basepath__ = __DIR__;
    static public $options = array(
        'permissions' => array(
            'use' => True,
            'options_name' => 'ma_accounts_settings'
        ),
        'files' => array(/*****EXAMPLE*****
        'id' => 102, //The id of the file
        'categories' => array('id' => 0), //
        'belt_access' => 5, //Id of purple belt
        'programs_access' => 0 //Id of Swat program
         */),
        'categories' => array(/*****EXAMPLE*****
        'id' => 0,
        'name' => 'My Cat',
        'belt_access' => 0, //Id of a white belt
        'programs_access' => 0 //If of Swat program
        ***Another entry into the array***
        'id' => 1
        'name' => 'My Cat->Something',
        'belt_access' => '', //Not specificed, i.e. public
        'programs_access' => '' //Not specified, i.e. public
         */)
     );

    function __construct() {
        /*
         * Used for "pretty" urls
         */
        add_filter('query_vars', array(&$this, 'add_query_vars'));
        //add_action('generate_rewrite_rules', array(&$this, 'add_rewrite_rules'));

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-dialog');
        wp_register_style('black-tie', plugins_url('application/view/css/jquery-ui.css', __FILE__));
        wp_register_style('fileManagerStyle', plugins_url('application/view/css/file_manager.css', __FILE__));
        wp_register_script('fileManagerScript', plugins_url('application/view/js/admin.js', __FILE__));
        wp_register_script('fileManagerJwplayer', plugins_url('application/view/js/jwplayer.js', __FILE__));

        register_activation_hook(__FILE__, array(&$this, 'activate_plugin'));
    } //End __construct

    public function activate_plugin() {
        update_option('file_manager_settings', $this->options);
    } //End activate_plugin

    /*
     * Purpose: To sort an array by a specific element
     * Param: array $array
     * Param: string $element
     * Param: int $sort_flags
     * Return: array $new_array
     */
    public function sort_array_by_element($array, $element, $sort_flags=SORT_REGULAR) {
        $temp_array = '';
        $new_array = array();

        array_walk($array, function($array_value, $array_key) use(&$temp_array, $element) {
            $temp_array[] = $array_value[$element];
        });

        sort($temp_array, $sort_flags);

        array_walk($temp_array, function($temp_value, $temp_key) use($array, $element, &$new_array) {
            array_walk($array, function($array_value, $array_key) use($temp_value, $element, &$new_array) {
                if ($temp_value === $array_value[$element]) {
                    $new_array[$array_key] = $array_value;
                    //break;
                }
            });
        });

        return $new_array;
    } //End sort_array_by_element

    /*
     * Purpose: To dertermin if a specific user has access to a category or file
        * Param: int $user
        * Param: int $belt
        * Param: string $programs
        * Return: Boolean
     */
    public function check_permissions($user, $belt, $programs) {
        if (!$this->options['permissions']['use']) {
            return True;
        }

        $temp = array(explode(',', get_user_meta($user, 'ma_accounts_programs', true)), False);
        $temp[0] = ($temp[0][0] != '') ? $temp[0] : '';
        if (!empty($programs) || $programs === '0') {
            array_walk(explode(',', $programs), function($programs_value, $programs_key) use(&$temp) {
                if (is_array($temp[0]) && in_array($programs_value, $temp[0])) {
                    $temp[1] = True;
                }
            });
        }

        if (
        (
            (
                (!empty($belt) || $belt === '0') &&
                $belt <= get_user_meta($user, 'ma_accounts_belt', true)
            ) ||
            True === $temp[1]
        ) ||
        (
            $belt == '' &&
            $programs == ''
        )
        ) { //End if
            return True;
        }

        return False;
    } //End check_permissions

    public function get_subcategories($category) {
        $categories = get_option('file_manager_settings');
        $categories = $categories['categories'];
        $return = array();
        array_walk($categories, function($category_value, $category_key) use ($category, &$return) {
            if (preg_match('/' . $category . '/', $category_value['name']) && $category_value['name'] !== $category) {
                $return[] = $category_value['id'];
            }
        });
        return $return;
    } //End get_subcategories

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
        $qvars[] = 'fm_category';
        return $qvars;
    } //End add_query_vars
} //End file_manager

$file_manager = array('main' => '', 'update_settings' => '', 'generate_views' => '');
$file_manager['main'] = new file_manager;
$file_manager['update_settings'] = new update_settings;
$file_manager['generate_views'] = new generate_views;
$generate_views = $file_manager['generate_views'];
add_shortcode('file', array('generate_views', 'file_func'));
?>
