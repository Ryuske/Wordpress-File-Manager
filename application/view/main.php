<?php
return '
<style type="text/css">
    .table, .th, .td { border: 1px solid #000; }
    .th { font-size: 25px !important; }
</style>
<h2 style="margin-bottom: 0;">VIP Content - All Types</h2>
<table class="table">
    <thead>
        <tr>
            <th class="th">Name</th>
            <th class="th">Type</th>
        </tr>
    </thead>
    <tbody>
        ' . $file_manager->return_attachments() . '
    </tbody>
</table>
';
?>
