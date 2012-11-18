<?php
class generate_views extends file_manager {
    public $attachments, $current_attachment, $current_category, $parent_page;

    function __construct() {
        add_action('admin_menu', array(&$this, 'add_admin_menu'));
        $settings = get_option('file_manager_settings');
        $this->attachments = get_children('post_parent=' . $settings['attachment_page'] . '&post_type=attachment&orderby=title&order=ASC');
    } //End __construct

    public function add_admin_menu() {
        add_plugins_page('Manage file manager options. Integrated with MA Accounts.', 'File Manager', 'administrator', 'file_manager', array(&$this, 'render_backend'));
    }

    public function generate_categories_admin($array) {
        global $file_manager;
        array_walk($array, function($category_value, $category_key) use($settings, $permissions_settings, $file_manager) {
            if ($settings['permissions']['use']) {
                $belt_access = (!empty($permissions_settings['belts'][$category_value['belt_access']]['name'])) ? $permissions_settings['belts'][$category_value['belt_access']]['name']  : 'N/A';

                //CodeBlock $programs_access
                $programs_access = explode(',', $category_value['programs_access']);
                array_walk($programs_access, function($program_value, $program_key) use(&$programs_access, $permissions_settings) {
                    $programs_access[$program_key] = $permissions_settings['programs'][$program_value]['name'];
                });
                $programs_access = (!empty($programs_access[0])) ? implode(', ', $programs_access) : 'N/A';
                //End CodeBlock
            } //End if($settings['permissions']['use'])

            ?>
            <div class="accordion">
                <div id="<?php echo $category_key; ?>">
                    <h3>
                        <a class="update" style="position: absolute; top: 7px; left: 21px;" title="Update Category" href="plugins.php?page=file_manager&amp;id=<?php echo $category_key; ?>&amp;action=update_category#categories"><span class="ui-icon ui-icon-pencil"></span></a>
                        <a class="delete" style="position: absolute; top: 7px; left: 36px;" title="Delete Category &amp; All Sub-Categories" href="plugins.php?page=file_manager&amp;id=<?php echo $category_key; ?>&amp;action=delete_category#categories"><span class="ui-icon ui-icon-trash"></span></a>
                        <a class="add" style="position: absolute; top: 7px; left: 51px;" title="Add Sub-Category" href="plugins.php?page=file_manager&amp;id=<?php echo $category_key; ?>&amp;action=add_subcategory#categories"><span class="ui-icon ui-icon-plusthick"></span></a>
                        <a class="accordion-href" href="#">
                            <span style="padding-left: 47px;"><?php echo esc_html($category_value['name']); ?></span>
                            <?php echo ($settings['permissions']['use']) ? '<br /> Belt Access: ' . esc_html($belt_access)  . ' &bull; Programs Access: ' . esc_html($programs_access) : ''; ?>
                        </a>
                    </h3>

                    <div class="jquery_accordion_content">
                        <div style="display: none">
                            You haven't set any sub-categories! <a href="plugins.php?page=file_manager&amp;id=<?php echo $category_key; ?>&amp;action=add_subcategory#categories">Add</a> one now.
                        </div>
                        <?php
                        if (is_array($category_value['subcategories'])) {
                            $file_manager['generate_views']->generate_categories_admin($category_value['subcategories']);
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        });
} //End generate_categories_admin

    public function render_backend() {
        global $file_manager;
        if (current_user_can('administrator')) {
            wp_enqueue_style('black-tie');
            wp_enqueue_style('fileManagerStyle');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('fileManagerScript');

            $settings = parent::$options;
            if ($settings['permissions']['use']) {
                $permissions_settings = get_option($settings['permissions']['options_name']);
            }
            include parent::__basepath__ . '/application/view/options/main.php';
        }
    } //End render_backend

    public function file_func() {
        global $file_manager, $current_user;
        $settings = get_option('file_manager_settings');
        wp_enqueue_style('fileManagerStyle');

        if (!empty($file_manager['generate_views']->attachments[get_query_var('fm_attachment')]) || $file_manager['generate_views']->attachments[get_query_var('fm_attachment')] === 0) {
            wp_enqueue_script('fileManagerJwplayer');
            $file_manager['generate_views']->current_attachment = $file_manager['generate_views']->attachments[get_query_var('fm_attachment')];
             if ($file_manager['main']->check_permissions($current_user->ID, $settings['files'][$file_manager['generate_views']->current_attachment->ID]['belt_access'], $settings['files'][$file_manager['generate_views']->current_attachment->ID]['programs_access'])) {
                ob_start();
                include parent::__basepath__ . '/application/view/attachment.php';
                return ob_get_clean();
            } else {
                echo '<meta http-equiv="refresh" content="0;url=' . esc_html($_SERVER['HTTP_REFERER']) . '">';
            }
        } else {
            $file_manager['generate_views']->current_category = get_query_var('fm_category');
            ob_start();
            include parent::__basepath__ . '/application/view/category.php';
            return ob_get_clean();
        }
    } //End file_func
} //End generate_views
?>
