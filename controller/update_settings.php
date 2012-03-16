<?php
class update_settings extends file_manager {
    function __construct() {
        add_action('admin_init', array($this, 'admin_settings'));
    } //End __construct

    public function admin_settings() {
        $permissions_settings = get_option(parent::$options['permissions']['options_name']);
        parent::$options = (!get_option('file_manager_settings')) ? parent::$options : get_option('file_manager_settings');
        $options_alias = &parent::$options;

        if (parent::$options['permissions']['use']) {
            //Make sure belts & programs exist in files
            array_walk($options_alias['files'], function($file_value, $file_key) use($permissions_settings, &$options_alias) {
                $temp_programs_access = explode(',', $file_value['programs_access']);

                if (!is_array($permissions_settings['belts']) || !array_key_exists($file_value['belt_access'], $permissions_settings['belts'])) {
                    $options_alias['files'][$file_key]['belt_access'] = '';
                }

                array_walk($temp_programs_access, function($program_value, $program_key) use($permissions_settings, &$temp_programs_access) {
                    if (!array_key_exists($program_value, $permissions_settings['programs'])) {
                        unset($temp_programs_access);
                    }
                });

                $temp_programs_access = implode(',', $temp_programs_access);
                $options_alias['files'][$file_key]['programs_access'] = $temp_programs_access;
            }); //End array_walk
            //End CodeBlock

            //Make sure belts & programs exist in categories
            array_walk($options_alias['categories'], function($category_value, $category_key) use($permissions_settings, &$options_alias) {
                $temp_programs_access = explode(',', $category_value['programs_access']);

                if (!is_array($permissions_settings['belts']) || !array_key_exists($category_value['belt_access'], $permissions_settings['belts'])) {
                    $options_alias['categories'][$category_key]['belt_access'] = '';
                }

                array_walk($temp_programs_access, function($program_value, $program_key) use($permissions_settings, &$temp_programs_access) {
                    if (!array_key_exists($program_value, $permissions_settings['programs'])) {
                        unset($temp_programs_access);
                    }
                });

                $temp_programs_access = implode(',', $temp_programs_access);
                $options_alias['categories'][$category_key]['programs_access'] = $temp_programs_access;
            }); //End array_walk
            //End CodeBlock
        }

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
                    $temp = $valid_options['categories'][$input['category_id']];
                    array_walk($valid_options['categories'], function($category_value, $category_key) use($temp, &$valid_options) {
                        if (preg_match('/' . $temp['name'] . '->/', $category_value['name'])) {
                            unset($valid_options['categories'][$category_value['id']]);
                        }
                    });
                    unset($valid_options['categories'][$temp['id']]);
                }
                //End deleting categories

                //CodeBlock adding/updating categories
                if (is_string($input['name'])) {
                    $temp = array('', '', '', '');

                    //Wether or not we're updating or adding.
                    switch ($input['category_action']) {
                        case 'subcategory': //Adding a subcategory
                            end($valid_options['categories']);
                            $temp[0] = key($valid_options['categories']);
                            reset($valid_options['categories']);
                            $temp[0]++;
                            $input['name'] = $valid_options['categories'][$input['category_id']]['name'] . '->' . $input['name'];
                            break;
                        case 'update':
                            $temp[0] = (array_key_exists($input['category_id'], $valid_options['categories'])) ? $input['category_id'] : '';
                            $input['name'] = $valid_options[$input['category_id']]['name'];
                            break;
                        default: //Add
                            end($valid_options['categories']);
                            $temp[0] = key($valid_options['categories']);
                            reset($valid_options['categories']);
                            $temp[0] = (isset($temp[0])) ? $temp[0]+1 : $temp[0] = 0;
                            break;
                    }

                    if ('' !== $temp[0]) {
                        if ($parent_options['permissions']['use']) {
                            if (array_key_exists($input['belt'], $permissions_settings['belts'])) {
                                $temp[2] = $input['belt'];
                            }

                            if (array_key_exists('programs', $input)) {
                                array_walk($input['programs'], function($program_value, $program_key) use($permissions_settings, &$temp) {
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
