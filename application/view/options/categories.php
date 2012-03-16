<div id="categories" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
    <h1 style="display: inline">Categories</h1> <h3 style="display: inline; position: relative; bottom: 1px;"><a href="#categories" onclick="jQuery('#add_category').dialog('open')"><span class="ui-icon ui-icon-plusthick" style="display: inline-block; vertical-align: text-top;"></span>Add</a></h3><br /><br />
    <?php
    if (count($settings['categories']) <= 0) {
        echo '<div>You haven\'t added any categories yet! <a href="#categories" onclick="jQuery(\'#add_category\').dialog(\'open\')">Add</a> one now.</div>';
    } else {
	?>
	    <script type="text/javascript">
		jQuery(document).ready(function() {
		    jQuery('.jquery_accordion_content').each(function() {
                if(jQuery.trim(jQuery(this).children().eq(1).text()) == "") {
                    jQuery(this).children().eq(0).css("display", "block");
                }
		    });
		});
        </script>
    <form id="update_category_order" action="post.php#categories" method="post">
        <?php settings_fields('file_manager_settings'); ?>
        <input id="new_order" name="file_manager_settings[new_order]" type="hidden" />
    </form>
    <div id="#accordion_content" class="accordion">
        <?php
        //Beginning accordion
	    $settings['categories'] = $file_manager['main']->sort_array_by_element($settings['categories'], 'name');
        array_walk($settings['categories'], function($category_value, $category_key) use($settings, $permissions_settings) {
            if (!preg_match('/->/', $category_value['name'])) {
                $programs_access = '';
                $belt_access = '';

                if ($settings['permissions']['use']) {
                    $belt_access = (!empty($permissions_settings['belts'][$category_value['belt_access']]['name'])) ? $permissions_settings['belts'][$category_value['belt_access']]['name']  : 'N/A';

                    $programs_access = explode(',', $category_value['programs_access']);
                    array_walk($programs_access, function($program_value, $program_key) use(&$programs_access, $permissions_settings) {
                        $programs_access[$program_key] = $permissions_settings['programs'][$program_value]['name'];
                    });
                    $programs_access = (!empty($programs_access[0])) ? implode(', ', $programs_access) : 'N/A';
                }

                ?>
                <div>
                    <h3>
                        <a class="update" style="position: absolute; top: 7px; left: 21px;" title="Update Category" href="plugins.php?page=file_manager&amp;id=<?php echo (int) $category_value['id']; ?>&amp;action=update_category#categories"><span class="ui-icon ui-icon-pencil"></span></a>
                        <a class="delete" style="position: absolute; top: 7px; left: 36px;" title="Delete Category &amp; All Sub-Categories" href="plugins.php?page=file_manager&amp;id=<?php echo (int) $category_value['id']; ?>&amp;action=delete_category#categories"><span class="ui-icon ui-icon-trash"></span></a>
                        <a class="add" style="position: absolute; top: 7px; left: 51px;" title="Add Sub-Category" href="plugins.php?page=file_manager&amp;id=<?php echo (int) $category_value['id']; ?>&amp;action=add_subcategory#categories"><span class="ui-icon ui-icon-plusthick"></span></a>
                        <a class="accordion-href" href="#">
                            <span style="padding-left: 47px;"><?php echo esc_html($category_value['name']); ?></span>
                            <?php echo ($settings['permissions']['use']) ? '<br /> Belt Access: ' . esc_html($belt_access)  . ' &bull; Programs Access: ' . esc_html($programs_access) : ''; ?>
                        </a>
                    </h3>

                <div class="jquery_accordion_content">
                <div style="display: none">You haven't set any sub-categories! <a href="plugins.php?page=file_manager&amp;id=<?php echo (int) $category_value['id']; ?>&amp;action=add_subcategory#categories">Add</a> one now.</div>
                    <?php
                    //Perparing to walk through sub-categories
                    $sub_category_array = array();
                    $temp_category_info = array('base' => $category_value['name'], 'current' => '', 'display_name' => '', 'next' => '', 'next_next' => '', 'previous' => '');
                    $current_level = 0;
                    $start_level = True;
                    $level_is_empty = False;

                    //Find sub-categories
                    array_walk($settings['categories'], function($temp_category_value, $temp_category_key) use(&$sub_category_array, $category_value) {
                        if (preg_match_all('/' . $category_value['name'] . '->/', $temp_category_value['name'], $matches)) {
                            $sub_category_array[] = $temp_category_value;
                        }
                    });

                    //Walk through sub-categories
                    array_walk($sub_category_array, function($sub_category_value, $sub_category_key) use($sub_category_array, $temp_category_info, &$start_level, &$level_is_empty, &$level_should_be_empty, &$current_level, $settings, $permissions_settings) {
                        $programs_access = '';
                        $belt_access = '';

                        if ($settings['permissions']['use']) {
                            $belt_access = (!empty($permissions_settings['belts'][$sub_category_value['belt_access']]['name'])) ? $permissions_settings['belts'][$sub_category_value['belt_access']]['name']  : 'N/A';

                            $programs_access = explode(',', $sub_category_value['programs_access']);
                            array_walk($programs_access, function($program_value, $program_key) use(&$programs_access, $permissions_settings) {
                            $programs_access[$program_key] = $permissions_settings['programs'][$program_value]['name'];
                            });
                            $programs_access = (!empty($programs_access[0])) ? implode(', ', $programs_access) : 'N/A';
                        }

                        $temp_category_info['current'] = $sub_category_value['name'];

                        //Begin writing $display_name
                        $display_name = array_reverse(explode('->', $temp_category_info['current']));
                        //$display_name = '<a class="accordion-href" href="">' . esc_html($display_name[0]) . '</a>';
                        $display_name = esc_html($display_name[0]);
                        $display_name = '
                        <a class="update" style="position: absolute; top: 7px; left: 21px;" title="Update Category" href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=update_category#categories"><span class="ui-icon ui-icon-pencil"></span></a>
                        <a class="delete" style="position: absolute; top: 7px; left: 36px;" title="Delete Category &amp; All Sub-Categories" href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=delete_category#categories"><span class="ui-icon ui-icon-trash"></span></a>
                        <a class="add" style="position: absolute; top: 7px; left: 51px;" title="Add Sub-Category" href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=add_subcategory#categories"><span class="ui-icon ui-icon-plusthick"></span></a>
                        <a class="accordion-href" href="#">
                        <span style="padding-left: 47px;">' . esc_html($display_name) . ' </span>';
                        $display_name .= ($settings['permissions']['use']) ? '<br /> Belt Access: ' . esc_html($belt_access)  . ' &bull; Programs Access: ' . esc_html($programs_access) . '</a>' : '</a>';
                        //End writing

                        $display_empty_category_text = 'You haven\'t set any sub-categories! <a href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=add_subcategory#categories">Add</a> one now.';

                        $temp_next_array = explode('->', $temp_category_info['current']);
                        array_pop($temp_next_array);
                        $temp_category_info['next'] = (!empty($sub_category_array[$sub_category_key+1]['name'])) ? $sub_category_array[$sub_category_key+1]['name'] : implode('->', $temp_next_array);
                        $temp_category_info['next_next'] = (!empty($sub_category_array[$sub_category_key+2]['name'])) ? $sub_category_array[$sub_category_key+2]['name'] : '';
                        $temp_category_info['previous'] = (!empty($sub_category_array[$sub_category_key-1]['name'])) ? $sub_category_array[$sub_category_key-1]['name'] : '';

                        $depth_of = array('current' => '', 'next' => '', 'previous' => '');
                        preg_match_all('/->/', $temp_category_info['current'], $depth_of['current']);
                        preg_match_all('/->/', $temp_category_info['next'], $depth_of['next']);
                        preg_match_all('/->/', $temp_category_info['previous'], $depth_of['previous']);
                        $depth_of['current'] = count($depth_of['current'][0]);
                        $depth_of['next'] = count($depth_of['next'][0]);
                        $depth_of['previous'] = count($depth_of['previous'][0]);
                        //var_dump($level_is_empty);
                        //print_r($depth_of);
                        //echo $current_level;

                        //Yes it is intetionally that there is two of these!
                        if (True === $start_level) {
                            $start_level = False;
                            ?>
                            <div class="accordion">
                            <?php
                        }

                        //Decide how I need to traverse the accordion
                        if ( //Stay at recursion level
                            ($depth_of['next'] == $depth_of['current'] || (!($depth_of['current'] < $depth_of['previous']) && ($depth_of['next'] == 0 || $level_is_empty))) && !($depth_of['next'] > $depth_of['current'])

                        ) { //End if logic
                            $level_is_empty = False;
                            ?>
                            <div>
                            <h3><?php echo $display_name; ?></h3>
                            <div class="jquery_accordion_content"><div style="display: none"><?php echo $display_empty_category_text; ?></div></div>
                            </div>
                            <?php
                        } else if ($depth_of['current'] < $depth_of['previous'] /*xor ($depth_of['current'] >= $depth_of['previous'] && $depth_of['previous'] != 0)*//*preg_match('/' . $temp_category_info['current'] . '/', $temp_category_info['next'])*/) { //Go up a recursion level
                            ?>
                            </div>
                            </div>
                            </div>
                            <div>
                            <h3><?php echo $display_name; ?></h3>
                            <div class="jquery_accordion_content"><div><?php echo $display_empty_category_text; ?></div>

                            <?php
                            if (preg_match('/' . $temp_category_info['current'] . '/', $temp_category_info['next'])) {
                                $start_level = True;
                                $level_is_empty = True;
                                //$current_level++;
                            } else {
                                $current_level--;
                                $level_is_empty = False;
                                echo '</div></div>';
                            }
                        } else { //Go down a recursion level
                            $start_level = True;
                            $level_is_empty = True;
                            $current_level++;
                            $temp_category_info['base'] = $temp_category_info['current'];
                            ?>
                            <div>
                            <h3><?php echo $display_name; ?></h3>
                            <div class="jquery_accordion_content"><div style="display: none"><?php echo $display_empty_category_text; ?></div>
                            <?php
                        } //Finish deciding

                        if (True === $start_level) {
                            $start_level = False;
                            ?>
                            <div class="accordion">
                            <?php
                        }
                    }); //End sub-categories array_walk

                    //Fix HTML match-ups
                    echo $current_level;
                    if ($current_level > 0) {
                        for ($i=0; $i<=$current_level; $i++) {
                            echo '</div>
                                </div>';
                        }
                    }
                ?>
                <!--After for loop-->
                <?php if (!empty($sub_category_array)) { ?>
                    <?php
                    if ($current_level == 1) {
                        echo '</div></div>';
                    } else if ($current_level > 1) {
                        echo '</div>';
                        for ($i=0; $i<$current_level; $i++) {
                            echo '</div>';
                        }
                    } else {
                        echo '</div></div></div>';
                    }
                    ?>
                <?php } else { ?>
                    </div></div>
                <?php } ?>
                <?php
                if ($settings['permissions']['use']) {
                    $temp = explode(',', $category_value['programs_access']);
                    array_walk($temp, function($temp_value, $temp_key) use(&$temp, $permissions_settings) {
                        $temp[$temp_key] = $permissions_settings['programs'][$temp_value]['name'];
                    });
                    $temp = implode(', ', $temp);
                }
            } //End if main category & not sub-category
        }); //End main categories array_walk
	    ?>
	</div>
	<?php
    }
    ?>
</div>
