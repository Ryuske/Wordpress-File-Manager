<style type="text/css">
    .table, .th, .td { border: 1px solid #000; }
    .th { font-size: 25px !important; }
</style>
<h2 style="margin-bottom: 0;">VIP Content</h2>
<table class="table">
    <tbody>
        <?php
        if (!empty($file_manager->current_category) && $file_manager->check_permissions($current_user->ID, $settings['categories'][$file_manager->current_category]['belt_access'], $settings['categories'][$file_manager->current_category]['programs_access'])) {
            $display_categories = array();
            array_walk(explode(',', $settings['categories'][$file_manager->current_category]['sub_categories']), function($category_value, $category_key) use(&$display_categories, $settings) {
                $display_categories[] = $settings['categories'][$category_value];
            });
        } else {
            $display_categories = $settings['categories'];
        }
        $file_manager->sort_array_by_element($display_categories, 'name');
        foreach ($display_categories as $category_value) {
            if ($file_manager->check_permissions($current_user->ID, $category_value['belt_access'], $category_value['programs_access'])) {
                ?>
                <tr>
                    <td><a href="?fm_category=<?php echo (int) $category_value['id']; ?>"><?php esc_html_e($category_value['name']); ?></a></td>
                </tr>
                <?php
            }
        }

        $display_files = array();
        array_walk($settings['files'], function($file_value, $file_key) use(&$display_files, $file_manager) {
            if (in_array($file_manager->current_category, explode(',', $file_value['categories']))) {
                $display_files[] = $file_manager->attachments[$file_value['id']];
            }
        });
        foreach ($display_files as $file_value) {
            if ($file_manager->check_permissions($current_user->ID, $settings['files'][$file_value->ID]['belt_access'], $settings['files'][$file_value->ID]['programs_access'])) {
                ?>
                <tr>
                    <td><?php esc_html_e($file_value->post_title); ?></td>
                <tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>
