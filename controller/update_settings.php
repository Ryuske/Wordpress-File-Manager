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
                if (array_key_exists('new_order', $input)) {
                    $category_by_id = array();
                    array_walk_recursive($vo_value, function($array_value, $array_key) use(&$category_by_id) {
                        $category_by_id[$array_value['id']] = $array_value;
                    });
                    $new_order = array();
                    $new_order_ids = array();
                    $new_order_array = array();

                    preg_match_all('/<div\ id="(.*?)"/', $input['new_order'], $new_order_ids);
                    $new_order_ids = $new_order_ids[1];

                    array_walk($new_order_ids, function($array_value, $array_key) use(&$new_order_array, $category_by_id) {
                        if (preg_match('/_/', $array_value)) {
                            $temp_temp_array = array();
                            $temp_array = array();
                            $array_to_walk = array_reverse(explode('_', $array_value));
                            array_walk(array_reverse(explode('_', $array_value)), function($recursive_array_value, $recursive_array_key) use(&$temp_temp_array, &$temp_array, &$array_to_walk, $category_by_id) {
                                if (empty($temp_array)) {
                                    $temp_temp_array[implode('_', $array_to_walk)] = $category_by_id[implode('_', $array_to_walk)];
                                } else {
                                    array_shift($array_to_walk);
                                    $temp_temp_array[implode('_', $array_to_walk)] = $temp_array;
                                    $temp_temp_array = array_reverse($temp_temp_array, True);
                                    array_pop($temp_temp_array);
                                }
                                $temp_array = $temp_temp_array;
                            });
                            $new_order_array = array_merge_recursive($new_order_array, $temp_array);
                        } else {
                            $new_order_array[(int) $array_value] = $category_by_id[(int) $array_value];
                        }
                    });

                    $valid_options['categories'] = $new_order;
                }

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
                            end($vo_value);
                            $temp[0] = key($vo_value);
                            reset($vo_value);
                            $temp[0] = explode('_', $temp[0]);
                            $temp[0] = array_reverse($temp[0]);
                            $temp[0] = $temp[0][0];
                            $temp[0]++;
                            $temp[0] = (array_key_exists($input['category_id'], $vo_value)) ? $input['category_id'] . '_' . $temp[0] : '';
                            //echo $temp[0];
                            //die();
                            break;
                        case 'update': //Updating a category
                            $temp[0] = (array_key_exists($input['category_id'], $vo_value) && '' !== trim($input['name'])) ? $input['category_id'] : '';
                            print_r($vo_value);
                            //$input['name'] = $vo_value[$temp[0]]['name'];
                            //var_dump($temp[0]);
                            //echo $input['name'];
                            //die();
                            break;
                        default: //Add
                            end($vo_value);
                            $temp[0] = key($vo_value);
                            reset($vo_value);
                            $temp[0] = (isset($temp[0])) ? $temp[0]+1 : $temp[0] = 0;
                            break;
                    }

                    //Update $valid_options based on $temp[0] as defined above
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
