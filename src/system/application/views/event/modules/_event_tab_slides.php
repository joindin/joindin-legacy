<div id="slides">
    <table summary="" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">
    <?php foreach ($slides_list as $sk=>$sv): ?>
        <tr class="<?php echo ($ct%2==0) ? 'row1' : 'row2'; ?>">
        <td>
        <a href="/talk/view/<?php echo $sk; ?>"><?php echo escape($sv['title']);
            ?></a>
        </td>
        <td><?php echo escape($sv['speaker']); ?>
        <td>
        <a href="<?php echo $sv['link']; ?>">Slides</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>
