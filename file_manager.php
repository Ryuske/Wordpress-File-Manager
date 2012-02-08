<?php
/*
Plugin Name: File Manager
Description: Simple file manager, with custom permissions
Version: 1.0
Author: Kenyon Haliwell
License: GPL2
 */
class file_manager {
    public $attachments, $current_attachment, $current_category;
    public $options = array(
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
        'sub_categories' => array('Something', 'Koala'),
        'belt_access' => 0, //Id of a white belt
        'programs_access' => 0 //If of Swat program
        ***Another entry into the array***
        'id' => 1
        'name' => 'My Cat->Something',
        'sub_categories' => array('Hazah'),
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

        register_activation_hook(__FILE__, array(&$this, 'activate_plugin'));

        add_action('admin_menu', array(&$this, 'add_admin_menu'));
        add_action('admin_init', array(&$this, 'admin_settings'));

        $this->get_attachments();
    } //End __construct

    /*
     * Purpose: To sort an array by a specific element
     * Param: array $array
     * Param: string $element
     * Param: int $sort_flags
     * Return: None
     */
    public function sort_array_by_element(&$array, $element, $sort_flags=SORT_REGULAR) {
        $temp_array = $array;
        $new_array = array();

        foreach ($temp_array as $key => $value) {
            unset($temp_array[$key]);
            $temp_array[] = $value[$element];
        }

        sort($temp_array, $sort_flags);

        foreach ($temp_array as $array_element) {
            foreach ($array as $key => &$value) {
                if ($array_element === $value[$element]) {
                    $new_array[$key] = $value;
                    break;
                }
            }
        }

        $array = $new_array;
    } //End sort_array_by_element

    public function activate_plugin() {
        update_option('file_manager_settings', $this->options);
    } //End activate_plugin

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
        $temp = '';
        $valid_options = array(
            'permissions' => array(
                'use' => trim($input['permissions']['use']),
                'options_name' => trim($input['permissions']['options_name'])
            ),
            'files' => array(),
            'categories' => array()
        );
        $permissions_settings = get_option($this->options['permissions']['options_name']);

        foreach($valid_options as $key => &$value) {
            $value = (!array_key_exists($key, $input)) ? $this->options[$key] : $value;
        }

        foreach ($valid_options as $key => &$value) {
            switch ($key) {
            case 'permissions':
                if (!empty($value['use'])) {
                    $value['use'] = True;
                } else {
                    $value['use'] = False;
                }

                if (!get_option($value['options_name'])) {
                    $value['options_name'] = $this->options['permissions']['options_name'];
                }
                break;
            case 'files':
                if (array_key_exists('file_id', $input)) {
                    $temp = array('', '', '');

                    foreach ($input['file_categories'] as $category_value) {
                        if (array_key_exists($category_value, $this->options['categories'])) {
                            $temp[0] .= $category_value . ',';
                        }
                    }
                    $temp[0] = substr($temp[0], 0, -1);

                    if ($this->options['permissions']['use']) {
                        if (array_key_exists($input['belt'], $permissions_settings['belts'])) {
                            $temp[1] = $input['belt'];
                        }

                        foreach ($input['programs'] as $program_value) {
                            if (array_key_exists($program_value, $permissions_settings['programs'])) {
                                $temp[2] .=  $program_value . ',';
                            }
                        }
                        $temp[2] = substr($temp[2], 0, -1);
                    }

                    $valid_options['files'][(int) $input['file_id']] = array('id' => (int) $input['file_id'], 'categories' => $temp[0], 'belt_access' => $temp[1], 'programs_access' => $temp[2]);
                }
                break;
            case 'categories':
                //CodeBlock deleting categories
                if (array_key_exists('category_id', $input) && array_key_exists($input['category_id'], $valid_options['categories']) && !array_key_exists('name', $input)) {
                    unset($valid_options['categories'][$input['category_id']]);
                    foreach ($valid_options['categories'] as $key => &$value) {
                        $temp = (!empty($value['sub_categories'])) ? explode(',', $value['sub_categories']) : '';
                        if (!empty($temp)) {
                            foreach ($temp as $temp_key => $temp_value) {
                                if ($temp_value === $input['category_id']) {
                                    unset($temp[$temp_key]);
                                }
                            }
                            $value['sub_categories'] = implode(',', $temp);
                        }
                    }
                }
                //End deleting categories

                //CodeBlock adding/updating categories
                if (is_string($input['name'])) {
                    $temp = array('', '', '', '');

                    //Wether or not we're updating or adding.
                    if (array_key_exists('category_id', $input) && array_key_exists($input['category_id'], $valid_options['categories'])) { //Upating
                        $temp[0] = $input['category_id'];
                    } else { //Adding
                        end($valid_options['categories']);
                        $temp[0] = key($valid_options['categories']);
                        if (isset($temp[0])) {
                            $temp[0]++;
                        } else {
                            $temp[0] = 0;
                        }
                    }

                    foreach ($input['sub_categories'] as $cat_key => $cat_value) {
                        $temp[1] .= $cat_key . ',';
                    }
                    $temp[1] = substr($temp[1], 0, -1);

                    if ($this->options['permissions']['use']) {
                        if (array_key_exists($input['belt'], $permissions_settings['belts'])) {
                            $temp[2] = $input['belt'];
                        }

                        foreach ($input['programs'] as $program_key) {
                            if (array_key_exists($program_key, $permissions_settings['programs'])) {
                                $temp[3] .=  $program_key . ',';
                            }
                        }
                        $temp[3] = substr($temp[3], 0, -1);
                    }

                    $valid_options['categories'][$temp[0]] = array('id' => $temp[0], 'name' => trim($input['name']), 'sub_categories' => $temp[1], 'belt_access' => $temp[2], 'programs_access' => $temp[3]);
                }
                //End adding/updating categories
                break;
            default:
                break;
            }
        }

        return $valid_options;
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
        $qvars[] = 'fm_category';
        return $qvars;
    } //End add_query_vars

    private function get_attachments() {
        $current_page = get_page_by_title('Students-LoggedIn');
        $this->attachments = get_children('post_parent=' . $current_page->ID . '&opst_type=attachment&order=ASC');
    } //End get_attachments

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
        global $file_manager;
        if (current_user_can('administrator')) {
            wp_enqueue_style('black-tie');
            wp_enqueue_style('fileManagerStyle');
            wp_enqueue_script('fileManagerScript');

            $settings = get_option('file_manager_settings');
            if ($settings['permissions']['use']) {
                $permissions_settings = get_option($settings['permissions']['options_name']);
            }
            include dirname(__FILE__) . '/application/view/options.php';
        }
    } //End render_backend

    public function file_func() {
        global $file_manager, $current_user;
        $settings = get_option('file_manager_settings');
        if (!empty($file_manager->attachments[get_query_var('fm_attachment')])) {
            $file_manager->current_attachment = $file_manager->attachments[get_query_var('fm_attachment')];
            ob_start();
            include dirname(__FILE__) . '/application/view/attachment.php';
            return ob_get_clean();
        } else {
            $file_manager->current_category = get_query_var('fm_category');
            ob_start();
            include dirname(__FILE__) . '/application/view/main.php';
            return ob_get_clean();
        }
    }
} //End file_manager

$file_manager = new file_manager;
add_shortcode('file', array('file_manager', 'file_func'));
?>
