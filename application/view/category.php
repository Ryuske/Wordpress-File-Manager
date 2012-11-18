<style type="text/css">
    .table, .th, .td { border: 1px solid #000; }
    .th { font-size: 25px !important; }
</style>
<?php
$title = (!empty($file_manager['generate_views']->current_category) || $file_manager['generate_views']->current_category === '0') ? $settings['categories'][$file_manager['generate_views']->current_category]['name'] : 'VIP Content';
?>
<h2 style="display: inline;"><?php esc_html_e($title); ?></h2>
<?php
if ($title != 'VIP Content') {
    /*
    ?>
    <h4 style="display: inline; position: relative; bottom: 1px; margin-left: 10px;"><a href="<?php esc_html_e($file_manager->referer['category']); ?>">Back</a></h4>
    <?php
     */
}
?>
<table class="file_manager_listings_table">
    <tbody>
        <?php
        if ((!empty($file_manager['generate_views']->current_category) || $file_manager['generate_views']->current_category === '0') && $file_manager['generate_views']->check_permissions($current_user->ID, $settings['categories'][$file_manager['generate_views']->current_category]['belt_access'], $settings['categories'][$file_manager['generate_views']->current_category]['programs_access'])) {
            $display_categories = array();
            $subcategories = $file_manager['main']->get_subcategories($file_manager['generate_views']->current_category, False);
            if (is_array($subcategories)) {
                array_walk($getsubcategories, function($category_value, $category_key) use(&$display_categories, $settings) {
                    $display_categories[] = $category_value;
                });
            }
        } else {
            $temp = $settings['categories'];
            $display_categories = array();
            if (is_array($temp)) {
                array_walk($temp, function($category_value, $category_key) use(&$display_categories) {
                    if (!preg_match('/_/', $category_value['id'])) {
                        $display_categories[] = $category_value;
                    }
                });
            }
        }
        if ($display_categories[0] != '') {
            $display_categories = $file_manager['main']->sort_array_by_element($display_categories, 'name');
            if (is_array($display_categories)) {
                array_walk($display_categories, function($category_value, $category_key) use($file_manager, $settings) {
                    if ($file_manager['main']->check_permissions($current_user->ID, $category_value['belt_access'], $category_value['programs_access'])) {
                        ?>
                        <tr>
                            <td class="category"><a href="?fm_category=<?php echo $category_value['id']; ?>"><?php esc_html_e($category_value['name']); ?></a></td>
                        </tr>
                        <?php
                    }
                });
            }
        }

        $display_files = array();
        if (is_array($file_manager['generate_views']->attachments)) {
            array_walk($file_manager['generate_views']->attachments, function($attachment_value, $attachment_key) use(&$display_files, $file_manager) {
                if ($file_manager['main']->in_category($attachment_value->ID, $file_manager['generate_views']->current_category)) {
                    $display_files[] = $attachment_value;
                }
            });
        }

        if (is_array($display_files)) {
            array_walk($display_files, function($file_value, $file_key) use($file_manager, $settings) {
                if ($file_manager['main']->check_permissions($current_user->ID, $settings['files'][$file_value->ID]['belt_access'], $settings['files'][$file_value->ID]['programs_access'])) {
                    ?>
                    <tr>
                        <?php
                        if ($file_value->post_mime_type == 'application/pdf') {
                            echo '<td class="file"><a href="?fm_attachment=' . (int) $file_value->ID . '" target="_blank">' . esc_html($file_value->post_title) . '</a></td>';
                        } else {
                            echo '<td class="file"><a href="?fm_attachment=' . (int) $file_value->ID . '">' . esc_html($file_value->post_title) . '</a></td>';
                        }
                        ?>
                    <tr>
                    <?php
                }
            });
        }
        ?>
    </tbody>
</table>
