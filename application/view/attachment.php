<h2 style="margin-bottom: 0"><?php esc_html_e($file_manager->current_attachment->post_title); ?></h2>
<!--<a href="<?php //esc_html_e($file_manager->referer['category']); ?>">Back</a>--><br />
<table style="margin-bottom: 0;">
    <tbody>
        <tr>
            <td>
                <?php
                switch($file_manager->current_attachment->post_mime_type) {
                    case 'image/jpeg':
                        echo '<img src="' . wp_get_attachment_url($file_manager->current_attachment->ID) . '" alt="There was a problem loading the picture." />';
                        break;
                    case 'text/plain':
                        echo file_get_contents($file_manager->current_attachment->guid);
                        break;
                    case 'audio/mpeg':
                        ?>
                        <div id='audiospace'>This text will be replaced</div>
                        <script type='text/javascript'>
                            jQuery(document).ready(function() {
                                jwplayer('audiospace').setup({
                                    'flashplayer': '<?php echo plugins_url('misc/player.swf', __FILE__); ?>',
                                    'file': '<?php echo $file_manager->current_attachment->guid; ?>',
                                    'autostart': 'true',
                                    'height': '24',
                                    'width': '470',
                                    'controlbar': 'bottom',
                                    'frontcolor': '000000',
                                    'lightcolor': '000000',
                                    'screencolor': '000000',
                                    'skin': '<?php echo plugins_url('misc/beelden.zip', __FILE__); ?>',
                                    'volume': '80'
                                });
                            });
                        </script>
                        <?php
                        break;
                    case 'video/x-flv':
                        ?>
                        <div id='videospace'>This text will be replaced</div>
                        <script type='text/javascript'>
                            jQuery(document).ready(function() {
                                jwplayer('videospace').setup({
                                    'flashplayer': '<?php echo plugins_url('misc/player.swf', __FILE__); ?>',
                                    'file': '<?php echo $file_manager->current_attachment->guid; ?>',
                                    'autostart': 'true',
                                    'controlbar': 'bottom',
                                    'frontcolor': '000000',
                                    'lightcolor': '000000',
                                    'screencolor': '000000',
                                    'skin': '<?php echo plugins_url('misc/beelden.zip', __FILE__); ?>',
                                    'volume': '80'
                                });
                            });
                        </script>
                        <?php
                        break;
                    case 'application/pdf':
                        echo '<meta http-equiv="refresh" content="0;url=' . $file_manager->current_attachment->guid . '">';
                        break;
                    default:
                        echo 'There was a problem displaying your post.';
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>
<!--<a href="<?php //esc_html_e($file_manager->referer['category']); ?>">Back</a>-->
