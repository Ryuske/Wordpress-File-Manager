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
        $parent_options = &parent::$options;
        $valid_options = array(
            'permissions' => array(
                'use' => trim($input['permissions']['use']),
                'options_name' => trim($input['permissions']['options_name'])
            ),
            'files' => array(),
            'categories' => array()
        );
        $permissions_settings = get_option(parent::$options['permissions']['options_name']);

        array_walk($valid_options, function($vo_value, $vo_key) use(&$valid_options, $input, $parent_options) {
            $valid_options[$vo_key] = (!array_key_exists($vo_key, $input)) ? $parent_options[$vo_key] : $vo_value;
        });

        array_walk($valid_options, function($vo_value, $vo_key) use(&$valid_options, $parent_options, $input, $temp, $permissions_settings) {
            switch ($vo_key) {
            case 'permissions':
                if (!empty($vo_value['use'])) {
                    $valid_options[$vo_key]['use'] = True;
                } else {
                    $valid_option[$vo_key]['use'] = False;
                }

                if (!get_option($value['options_name'])) {
                    $valid_options[$vo_key]['options_name'] = $parent_options['permissions']['options_name'];
                }
                break;
            case 'files':
                if (array_key_exists('file_id', $input)) {
                    $temp = array('', '', '');

                    if (array_key_exists('file_categories', $input)) {
                        array_walk($input['file_categories'], function($category_value, $category_key) use($parent_options, &$temp) {
                            if (array_key_exists($category_value, $parent_options['categories'])) {
                                $temp[0] .= $category_value . ',';
                            }
                        });
                        $temp[0] = substr($temp[0], 0, -1);
                    }

                    if ($parent_options['permissions']['use']) {
                        if (array_key_exists($input['belt'], $permissions_settings['belts'])) {
                            $temp[1] = $input['belt'];
                        }

                        if (array_key_exists('programs', $input)) {
                            array_walk($input['programs'], function($program_value, $program_key) use($permissions_settings, &$temp) {
                                if (array_key_exists($program_value, $permissions_settings['programs'])) {
                                    $temp[2] .=  $program_value . ',';
                                }
                            });
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
                    array_walk($valid_options['categories'], function($category_value, $category_key) use($temp, &$vo_value, $input) {
                        $temp = (!empty($vo_value['sub_categories'])) ? explode(',', $vo_value['sub_categories']) : '';
                        if (!empty($temp)) {
                            array_walk($temp, function($temp_value, $temp_key) use($input, &$temp) {
                                if ($temp_value === $input['category_id']) {
                                    unset($temp[$temp_key]);
                                }
                            });
                            $vo_value['sub_categories'] = implode(',', $temp);
                        }
                    });
                }
                //End deleting categories

                //CodeBlock adding/updating categories
                if (is_string($input['name'])) {
                    $temp = array('', '', '', '');

                    //Wether or not we're updating or adding.
                    switch ($input['category_action']) {
                        case 'subcategory':
                            $temp[0] = (array_key_exists($input['category_id'], $valid_options['categories'])) ? $input['category_id'] : '';
                            $input['name'] = $valid_options['categories'][$input['category_id']]['name'] . '->' . $input['name'];
                            break;
                        case 'update':
                            $temp[0] = (array_key_exists($input['category_id'], $valid_options['categories'])) ? $input['category_id'] : '';
                            $input['name'] = $valid_options[$input['category_id']]['name'];
                            break;
                        default: //Add
                            end($valid_options['categories']);
                            $temp[0] = key($valid_options['categories']);
                            $temp[0] = (isset($temp[0])) ? $temp[0]+1 : $temp[0] = 0;
                            break;
                    }

                    if ('' !== $temp[0]) {
                        if ($parent_options['permissions']['use']) {
                            if (array_key_exists($input['belt'], $permissions_settings['belts'])) {
                                $temp[2] = $input['belt'];
                            }

                            if (array_key_exists('programs', $input)) {
                                print_r($permissions_settings);
                                array_walk($input['programs'], function($program_value, $program_key) use($permissions_settings, &$temp) {
                                    print_r($permissions_settings);
                                    if (array_key_exists($program_key, $permissions_settings['programs'])) {
                                        $temp[3] .=  $program_key . ',';
                                    }
                                });
                                $temp[3] = substr($temp[3], 0, -1);
                            }
                        }

                        $valid_options['categories'][$temp[0]] = array('id' => $temp[0], 'name' => trim($input['name']), 'belt_access' => $temp[2], 'programs_access' => $temp[3]);
                    }
                }
                //End adding/updating categories
                break;
            default:
                break;
            }
        });

        return $valid_options;
    } //End validate_settings
} //End update_settings
?>
