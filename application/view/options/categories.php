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

        <?php
        $settings['categories'] = $file_manager['main']->sort_array_by_element($settings['categories'], 'id');
        ?>
        <div id="accordion_content" class="accordion">
            <?php $file_manager['generate_views']->generate_categories_admin($settings['categories']); ?>
        </div>
        <?php
    } //End if at least one category
    ?>
</div>
