<h2 style="margin-bottom: 0"><?php esc_html_e($file_manager->current_attachment->post_title); ?></h2>
<a href="/<?php esc_html_e($_SERVER['HTTP_REFERER']); ?>">Back</a><br />
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
                            <embed type="application/x-shockwave-flash" wmode="transparent" src="http://www.google.com/reader/ui/3523697345-audio-player.swf?audioUrl=<?php echo $file_manager->current_attachment->guid; ?>" height="27" width="320"></embed>
                        <?php
                        break;
                    case 'video/x-flv':
                        ?>
                        <script type='text/javascript' src="<?php echo plugins_url('js/jwplayer.js', __FILE__); ?>"></script>
                        <div id='mediaspace'>This text will be replaced</div>
                        <script type='text/javascript'>
                        jwplayer('mediaspace').setup({
                            'flashplayer': '<?php echo plugins_url('misc/player.swf', __FILE__); ?>',
                            'file': '<?php echo $file_manager->current_attachment->guid; ?>',
                            'autostart': 'true',
                            'controlbar': 'bottom',
                            'width': '320',
                            'height': '240',
                            'allowfullscreen': 'false'
                        });
                        </script>
                        <?php
                        break;
                    default:
                        echo 'There was a problem displaying your post.';
                }
                ?>
            </td>
        </tr>
    </tbody>
</table>
<a href="/<?php esc_html_e($_SERVER['HTTP_REFERER']); ?>">Back</a>
