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
        <form id="update_category_order" action="options.php#categories" method="post">
            <?php settings_fields('file_manager_settings'); ?>
            <input class="new_order" name="file_manager_settings[new_order]" type="hidden" />
        </form>
        <div id="accordion_content" class="accordion">
            <?php
            $settings['categories'] = $file_manager['main']->sort_array_by_element($settings['categories'], 'id');
            array_walk($settings['categories'], function($category_value, $category_key) use($settings, $permissions_settings, $file_manager) {
                if (!preg_match('/_/', $category_value['id'])) {
                    if ($settings['permissions']['use']) {
                        $belt_access = (!empty($permissions_settings['belts'][$category_value['belt_access']]['name'])) ? $permissions_settings['belts'][$category_value['belt_access']]['name']  : 'N/A';

                        $programs_access = explode(',', $category_value['programs_access']);
                        array_walk($programs_access, function($program_value, $program_key) use(&$programs_access, $permissions_settings) {
                            $programs_access[$program_key] = $permissions_settings['programs'][$program_value]['name'];
                        });
                        $programs_access = (!empty($programs_access[0])) ? implode(', ', $programs_access) : 'N/A';
                    }

                    ?>
                        <div id="<?php echo (int) $category_value['id']; ?>">
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
                        $sub_category_array = $file_manager['main']->get_subcategories($category_value['id']);

                        if (is_array($sub_category_array) && !empty($sub_category_array)) {
                            $file_manager['generate_views']->generate_subcategory_html($sub_category_array);
                        } else {
                            echo '</div></div>'; //End main category
                        }
                        //</div> (preemptive, real ones are under //End main category)
                }
            });
            ?>
        </div>
        <?php
    } //End if at least one category
    ?>
</div>
