<style type="text/css">
    .table, .th, .td { border: 1px solid #000; }
    .th { font-size: 25px !important; }
</style>
<?php
$title = (!empty($file_manager->current_category) || $file_manager->current_category === '0') ? $settings['categories'][$file_manager->current_category]['name'] : 'VIP Content';
if (preg_match('/->/', $title)) {
    $title = implode(', ', explode('->', $title));
}
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
        if ((!empty($file_manager->current_category) || $file_manager->current_category === '0') && $file_manager->check_permissions($current_user->ID, $settings['categories'][$file_manager->current_category]['belt_access'], $settings['categories'][$file_manager->current_category]['programs_access'])) {
            $display_categories = array();
            foreach (explode(',', $settings['categories'][$file_manager->current_category]['sub_categories']) as $category_key => $category_value) {
                $display_categories[] = $settings['categories'][$category_value];
            }
        } else {
            $temp = $settings['categories'];
            $display_categories = array();
            foreach ($temp as $category_key => $category_value) {
                if (!preg_match('/->/', $category_value['name'])) {
                    $display_categories[] = $category_value;
                }
            }
        }
        if ($display_categories[0] != '') {
            $file_manager->sort_array_by_element($display_categories, 'name');
            foreach ($display_categories as $category_value) {
                if ($file_manager->check_permissions($current_user->ID, $category_value['belt_access'], $category_value['programs_access'])) {
                    $title = (preg_match('/->/', $category_value['name'])) ? substr($category_value['name'], strlen($settings['categories'][$file_manager->current_category]['name'])+2) : $category_value['name'];
                    ?>
                    <tr>
                        <td class="category"><a href="?fm_category=<?php echo (int) $category_value['id']; ?>"><?php esc_html_e($title); ?></a></td>
                    </tr>
                    <?php
                }
            }
        }

        $display_files = array();
        foreach ($settings['files'] as $file_key => $file_value) {
            if (in_array($file_manager->current_category, explode(',', $file_value['categories']))) {
                $display_files[] = $file_manager->attachments[$file_value['id']];
            }
        }
        foreach ($display_files as $file_value) {
            if ($file_manager->check_permissions($current_user->ID, $settings['files'][$file_value->ID]['belt_access'], $settings['files'][$file_value->ID]['programs_access'])) {
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
        }
        ?>
    </tbody>
</table>
