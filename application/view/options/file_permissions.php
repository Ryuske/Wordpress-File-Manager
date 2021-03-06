<div id="file_permissions" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
    <h1>File Permissions</h1>
    <table class="file_manager_table" style="width: 80%;">
	<tbody>
	    <tr>
		<th></th>
		<th>File</th>
		<th style="width: 350px;">Categories</th>
		<?php
		if ($settings['permissions']['use']) {
		    ?>
		    <th>Belt Access</th>
		    <th>Programs Access</th>
		    <?php
		}
		?>
	    </tr>
        <?php
        print_r($permissions_settings);
        if (is_array($file_manager['generate_views']->attachments)) {
            array_walk($file_manager['generate_views']->attachments, function($attachment_value, $attachment_key) use($settings, $permissions_settings) {
                $temp = explode(',', $settings['files'][$attachment_value->ID]['categories']);
                array_walk($temp, function($temp_value, $temp_key) use(&$temp, $settings) {
                        $temp[$temp_key] = $settings['categories'][$temp_value]['name'];
                });
                $temp = implode(', ', $temp);
                ?>
                <tr>
                    <td><a href="plugins.php?page=file_manager&amp;id=<?php echo (int) $attachment_value->ID; ?>&amp;action=update_file#file_permissions"><span class="ui-icon ui-icon-pencil" style="position: relative; margin: 0 auto;"></span></a></td>
                    <td><?php esc_html_e($attachment_value->post_title); ?></td>
                    <td><?php esc_html_e($temp); ?></td>
                    <?php
                    if ($settings['permissions']['use']) {
                        $temp = array('belt_access' => $settings['files'][$attachment_value->ID]['belt_access'], 'programs_access' => explode(',', $settings['files'][$attachment_value->ID]['programs_access']));

                        $temp['belt_access'] = (!empty($temp['belt_access']) || 0 === $temp['belt_access']) ? $permissions_settings['belts'][$temp['belt_access']]['name'] : 'N/A';
                        if (is_array($temp['programs_access'])) {
                            array_walk($temp['programs_access'], function($temp_value, $temp_key) use(&$temp, $permissions_settings) {
                                $temp['programs_access'][$temp_key] = (!empty($permissions_settings['programs'][$temp_value]) && 0 !== $permissions_settings['programs'][$temp_value]) ? $permissions_settings['programs'][$temp_value]['name'] : 'N/A';
                            });
                        }

                        $temp['programs_access'] = (!empty($temp['programs_access'][0]) || 0 === $temp['programs_access']) ? implode(', ', $temp['programs_access']) : 'N/A';
                        ?>
                        <td><?php esc_html_e($temp['belt_access']); ?></td>
                        <td><?php esc_html_e($temp['programs_access']); ?></td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
            });
        }
	    ?>
	</tbody>
    </table>
</div>
