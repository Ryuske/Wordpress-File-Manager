<?php
class update_settings extends file_manager {
    function __construct() {
        register_activation_hook(__FILE__, array($this, 'activate_plugin'));
        add_action('admin_init', array($this, 'admin_settings'));
    } //End __construct

    public function admin_settings() {
        parent::$options = (!get_option('file_manager_settings')) ? parent::options : get_option('file_manager_settings');
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
        $permissions_settings = get_option(parent::$options['permissions']['options_name']);

        foreach($valid_options as $key => &$value) {
            $value = (!array_key_exists($key, $input)) ? parent::$options[$key] : $value;
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
                    $value['options_name'] = parent::$options['permissions']['options_name'];
                }
                break;
            case 'files':
                if (array_key_exists('file_id', $input)) {
                    $temp = array('', '', '');

                    if (array_key_exists('file_categories', $input)) {
                        foreach ($input['file_categories'] as $category_value) {
                            if (array_key_exists($category_value, parent::$options['categories'])) {
                                $temp[0] .= $category_value . ',';
                            }
                        }
                        $temp[0] = substr($temp[0], 0, -1);
                    }

                    if (parent::$options['permissions']['use']) {
                        if (array_key_exists($input['belt'], $permissions_settings['belts'])) {
                            $temp[1] = $input['belt'];
                        }

                        if (array_key_exists('programs', $input)) {
                            foreach ($input['programs'] as $program_value) {
                                if (array_key_exists($program_value, $permissions_settings['programs'])) {
                                    $temp[2] .=  $program_value . ',';
                                }
                            }
                            $temp[2] = substr($temp[2], 0, -1);
                        }
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

                    if (array_key_exists('sub_categories', $input)) {
                        foreach ($input['sub_categories'] as $cat_key => $cat_value) {
                            $temp[1] .= $cat_key . ',';
                        }
                        $temp[1] = substr($temp[1], 0, -1);
                    }

                    if (parent::$options['permissions']['use']) {
                        if (array_key_exists($input['belt'], $permissions_settings['belts'])) {
                            $temp[2] = $input['belt'];
                        }

                        if (array_key_exists('programs', $input)) {
                            foreach ($input['programs'] as $program_key) {
                                if (array_key_exists($program_key, $permissions_settings['programs'])) {
                                    $temp[3] .=  $program_key . ',';
                                }
                            }
                            $temp[3] = substr($temp[3], 0, -1);
                        }
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
} //End update_settings
?>
