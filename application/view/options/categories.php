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
	<div class="accordion">
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
                <a class="add" style="position: absolute; top: 7px; left: 51px;" title="Add Sub-Category" href="#"><span class="ui-icon ui-icon-plusthick"></span></a>
                    <a class="accordion-href" href="#">
                    <span style="padding-left: 47px;"><?php echo esc_html($category_value['name']); ?> </span>
                    <?php echo ($settings['permissions']['use']) ? '<br /> Belt Access: ' . esc_html($belt_access)  . ' &bull; Programs Access: ' . esc_html($programs_access) : ''; ?>
                    </a>
                </h3>
                <div class="jquery_accordion_content">
                <div style="display: none">You haven't set any sub-categories! <a href="plugins.php?page=file_manager&amp;id=<?php echo (int) $category_value['id']; ?>&amp;action=add_subcategory#categories">Add</a> one now.</div>
                    <?php
                    //Perparing to walk through sub-categories
                    $sub_category_array = array();
                    $temp_category_info = array('base' => $category_value['name'], 'current' => '', 'display_name' => '', 'next' => '');
                    $current_level = 0;
                    $start_level = True;
                    $iterations = 0;

                    //Find sub-categories
                    array_walk($settings['categories'], function($temp_category_value, $temp_category_key) use(&$sub_category_array, $category_value) {
                        if (preg_match_all('/' . $category_value['name'] . '->/', $temp_category_value['name'], $matches)) {
                            $sub_category_array[] = $temp_category_value;
                        }
                    });

                    //Walk through sub-categories
                    array_walk($sub_category_array, function($sub_category_value, $sub_category_key) use($sub_category_array, $temp_category_info, &$start_level, &$current_level, &$iterations, $settings, $permissions_settings) {
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
                        $display_name = array_reverse(explode('->', $temp_category_info['current']));
                        $display_name = esc_html($display_name[0]);
                        $display_name = '
                        <a class="update" style="position: absolute; top: 7px; left: 21px;" title="Update Category" href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=update_category#categories"><span class="ui-icon ui-icon-pencil"></span></a>
                        <a class="delete" style="position: absolute; top: 7px; left: 36px;" title="Delete Category &amp; All Sub-Categories" href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=delete_category#categories"><span class="ui-icon ui-icon-trash"></span></a>
                        <a class="add" style="position: absolute; top: 7px; left: 51px;" title="Add Sub-Category" href="#"><span class="ui-icon ui-icon-plusthick"></span></a>
                        <a class="accordion-href" href="#">
                        <span style="padding-left: 47px;">' . esc_html($display_name) . ' </span>';
                        $display_name .= ($settings['permissions']['use']) ? '<br /> Belt Access: ' . esc_html($belt_access)  . ' &bull; Programs Access: ' . esc_html($programs_access) . '</a>' : '</a>';
                        $display_empty_category_text = 'You haven\'t set any sub-categories! <a href="plugins.php?page=file_manager&amp;id=' . (int) $sub_category_value['id'] . '&amp;action=add_subcategory#categories">Add</a> one now.';
                        $temp_next_array = explode('->', $temp_category_info['current']);
                        array_pop($temp_next_array);
                        $temp_category_info['next'] = (!empty($sub_category_array[$sub_category_key+1]['name'])) ? $sub_category_array[$sub_category_key+1]['name'] : implode('->', $temp_next_array);
                        $iterations++;

                        //Yes it is intetionally that there is two of these!
                        if (True === $start_level) {
                            $start_level = False;
                            ?>
                            <div class="accordion">
                            <?php
                        }

                        //Decide how I need to traverse the accordion
                        if ( //Stay at recursion level
                            (
                                !preg_match('/' . $temp_category_info['current'] . '/', $temp_category_info['next']) &&
                                (
                                    preg_match('/' . $temp_category_info['base'] . '/', $temp_category_info['next']) &&
                                    $iterations <= count($sub_category_array)
                                )
                            )/* ||
                            (
                                count($sub_category_array) == 1
                            )*/
                        ) { //End if logic
                            ?>
                            <div>
                            <h3><?php echo $display_name; ?></h3>
                            <div class="jquery_accordion_content"><div style="display: none"><?php echo $display_empty_category_text; ?></div></div>
                            </div>
                            <?php
                        } else if (preg_match('/' . $temp_category_info['current'] . '/', $temp_category_info['next'])) { //Go down a recursion level
                            $start_level = True;
                            $current_level++;
                            $temp_category_info['base'] = $temp_category_info['current'];
                            ?>
                            <div>
                            <h3><?php echo $display_name; ?></h3>
                            <div class="jquery_accordion_content"><div style="display: none"><?php echo $display_empty_category_text; ?></div>
                            <?php
                        } else { //Go up a recursion level
                            $current_level--;
                            ?>
                            </div></div></div>
                            <div>
                            <h3><?php echo $display_name; ?></h3>
                            <div class="jquery_accordion_content"><div><?php echo $display_empty_category_text; ?></div>
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
                    if ($current_level > 0) {
                        for ($i=0; $i<=$current_level; $i++) {
                            echo '</div></div>';
                        }
                    }
                    ?>
                </div>
                </div>
                </div>
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
