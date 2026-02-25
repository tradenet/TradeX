<?php
include 'global-header.php';
include 'global-menu.php';

require_once 'dirdb.php';

$_REQUEST['sort_by'] = isset($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : null;

$db = new SavedLinksDB();
$links = $db->RetrieveAll($_REQUEST['sort_by']);
?>

    <div class="centered-header">
      Saved Links: <span id="num-items"><?php echo count($links); ?></span> Total
    </div>

    <table id="items-table" align="center" width="90%" cellspacing="0" class="item-table">
      <thead>
        <tr>
          <td class="ta-center" style="width: 25px;">
            <input type="checkbox" class="check-all"/>
          </td>
          <td class="<?php if( $db->sorter == 'link_id' ) echo 'sort-by'; ?>" style="width: 140px;">
            <a href="index.php?r=_xSavedLinksShow&sort_by=link_id">Link ID</a>
          </td>
          <td class="<?php if( $db->sorter == 'link_name' ) echo 'sort-by'; ?>">
            <a href="index.php?r=_xSavedLinksShow&sort_by=link_name">Link Name</a>
          </td>
          <td class="<?php if( $db->sorter == 'type' ) echo 'sort-by'; ?>" style="width: 90px;">
            <a href="index.php?r=_xSavedLinksShow&sort_by=type">Type</a>
          </td>
          <td class="ta-center" style="width: 90px;">Thumbnails</td>
          <td style="width: 100px;"></td>
        </tr>
      </thead>
      <tbody>
        <?php
        if( count($links) == 0 ):
        ?>
        <tr>
          <td colspan="6" style="padding: 15px; text-align: center;">
            No saved links found. Use the <a href="_xLinkGenerateShow" class="dialog">Link Generator</a>
            and check &ldquo;Save this link with custom thumbnails&rdquo; to create saved links.
          </td>
        </tr>
        <?php
        else:
            foreach( $links as $original )
            {
                $item = string_htmlspecialchars($original);
                include 'saved-links-tr.php';
            }
        endif;
        ?>
      </tbody>
    </table>

    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <a href="_xLinkGenerateShow" class="dialog" title="Link Generator"><img src="images/add-32x32.png" border="0" /></a>
          <img src="images/toolbar-separator-2x32.png"/>
          <img src="images/link-32x32.png" class="action" title="Generate Toplist URL">
          <img src="images/delete-32x32.png" class="action" title="Delete">
          <img src="images/toolbar-separator-2x32.png"/>
          <a href="docs/saved-links.html" title="Documentation" target="_blank"><img src="images/help-32x32.png" border="0" /></a>
        </div>
      </div>
    </div>

    <div id="toolbar-vspacer"></div>

<script type="text/javascript">
$(function()
{
    $('.action[title="Generate Toplist URL"]').click(function()
    {
        var ids = [];
        $('input[type="checkbox"]:checked', $('#items-table')).each(function() {
            ids.push($(this).val());
        });
        if( ids.length == 0 ) return alert('Please select one or more saved links.');
        XHR.send('_xSavedLinksGenerateToplist', 'link_ids[]=' + ids.join('&link_ids[]='),
            function(data) { WA.message(data[WA_KEY_MESSAGE]); });
    });

    $('.action[title="Delete"]').click(function()
    {
        var ids = [];
        $('input[type="checkbox"]:checked', $('#items-table')).each(function() {
            ids.push($(this).val());
        });
        if( ids.length == 0 ) return alert('Please select one or more saved links.');
        if( !confirm('Delete the selected saved links?') ) return;
        XHR.send('_xSavedLinksDeleteBulk', 'ids=' + ids.join(','),
            function(data) {
                WA.message(data[WA_KEY_MESSAGE]);
                $.each(ids, function(i, id) { $('#item-' + id).remove(); });
                $('#num-items').text($('#items-table tbody tr').length);
            });
    });
});
</script>

<?php
include 'global-footer.php';
?>
