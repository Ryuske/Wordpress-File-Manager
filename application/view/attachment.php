<?php
$post_content = '';
switch($file_manager->current_attachment->post_mime_type) {
    case 'image/jpeg':
        $post_content = '<img src="' . wp_get_attachment_url($file_manager->current_attachment->ID) . '" alt="There was a problem loading the picture." />';
        break;
    case 'text/plain':
        $post_content = file_get_contents($file_manager->current_attachment->guid);
        break;
    case 'audio/mpeg':
        $post_content = '<embed type="application/x-shockwave-flash" wmode="transparent" src="http://www.google.com/reader/ui/3523697345-audio-player.swf?audioUrl=' . $file_manager->current_attachment->guid . '" height="27" width="320"></embed>';
        break;
    case 'video/mpeg':
        $post_content = '<video width="320" height="240" controls="controls">
                <source src="' . $file_manager->current_attachment->guid . '" type="video/mpeg" />
                <object width="320" height="240" src="' . $file_manager->current_attachment->guid . '">
                    <embed width="320" height="240" src="' . $file_manager->current_attachment->guid . '">
                        Your browser does not support our videos!
                    </embed>
                </object>
            </video>';
        break;
    default:
        $post_content = 'There was a problem displaying your post.';
}
return '
<h2 style="margin-bottom: 0">' . $file_manager->current_attachment->post_title . '</h2>
<a href="/' . get_page(get_the_ID())->post_name . '">Back</a><br />
<table style="margin-bottom: 0;">
    <tbody>
        <tr>
            <td>' . $post_content . '</td>
        </tr>
    </tbody>
</table>
<a href="/' . get_page(get_the_ID())->post_name . '">Back</a>
';
?>
