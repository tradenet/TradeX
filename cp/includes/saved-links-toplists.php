<?php
include 'global-header.php';
include 'global-menu.php';

require_once 'textdb.php';

$_REQUEST['sort_by'] = isset($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : null;

$db = new ToplistsSavedLinksDB();
$toplists = $db->RetrieveAll($_REQUEST['sort_by']);
?>

    <div class="centered-header">
      Saved Link Toplists: <span id="num-items"><?php echo count($toplists); ?></span> Configured
    </div>

    <table align="center" width="90%" cellspacing="0" class="item-table">
      <thead>
        <tr>
          <td class="ta-center" style="width: 25px;">
            <input type="checkbox" class="check-all"/>
          </td>
          <td class="ta-center<?php if( $db->sorter == 'toplist_id' ) echo ' sort-by'; ?>" style="width: 55px;">
            <a href="index.php?r=_xSavedLinkToplistsShow&sort_by=toplist_id">ID</a>
          </td>
          <td class="<?php if( $db->sorter == 'source_type' ) echo 'sort-by'; ?>" style="width: 110px;">
            <a href="index.php?r=_xSavedLinkToplistsShow&sort_by=source_type">Source</a>
          </td>
          <td class="<?php if( $db->sorter == 'template' ) echo 'sort-by'; ?>" style="width: 220px;">
            <a href="index.php?r=_xSavedLinkToplistsShow&sort_by=template">Template</a>
          </td>
          <td class="<?php if( $db->sorter == 'outfile' ) echo 'sort-by'; ?>">
            <a href="index.php?r=_xSavedLinkToplistsShow&sort_by=outfile">Output File</a>
          </td>
          <td class="ta-center" style="width: 130px;">Last Built</td>
          <td style="width: 90px;"></td>
        </tr>
      </thead>
      <tbody>
        <?php
        if( count($toplists) == 0 ):
        ?>
        <tr>
          <td colspan="7" style="padding: 15px; text-align: center;">
            No saved link toplists configured yet.
            <a href="_xSavedLinkToplistsAddShow" class="dialog">Add one now</a>.
          </td>
        </tr>
        <?php
        else:
            foreach( $toplists as $original )
            {
                $item = string_htmlspecialchars($original);
                include 'saved-links-toplist-tr.php';
            }
        endif;
        ?>
      </tbody>
    </table>

    <div id="toolbar">
      <div id="toolbar-content">
        <div id="toolbar-icons">
          <a href="_xSavedLinkToplistsAddShow" class="dialog" title="Add"><img src="images/add-32x32.png" border="0" /></a>
          <img src="images/toolbar-separator-2x32.png"/>
          <img src="images/build-32x32.png" class="action" title="Build">
          <img src="images/delete-32x32.png" class="action" title="Delete">
          <img src="images/toolbar-separator-2x32.png"/>
          <a href="_xSavedLinkToplistsBuildAll" class="xhr" title="Build All" confirm="Build all saved link toplists now?"><img src="images/build-all-32x32.png" border="0" /></a>
        </div>
      </div>
    </div>

    <div id="toolbar-vspacer"></div>

<script type="text/javascript">
$(function()
{
    $('.action[title="Build"]').click(function()
    {
        var ids = [];
        $('input[type="checkbox"]:checked', $('#items-table')).each(function() {
            ids.push($(this).val());
        });
        if( ids.length == 0 ) return alert('Please select one or more toplists.');
        XHR.send('_xSavedLinkToplistsBuild', 'toplist_id=' + ids.join(','),
            function(data) { WA.message(data[WA_KEY_MESSAGE]); });
    });

    $('.action[title="Delete"]').click(function()
    {
        var ids = [];
        $('input[type="checkbox"]:checked', $('#items-table')).each(function() {
            ids.push($(this).val());
        });
        if( ids.length == 0 ) return alert('Please select one or more toplists.');
        if( !confirm('Delete the selected toplists?') ) return;
        XHR.send('_xSavedLinkToplistsDeleteBulk', 'toplist_id=' + ids.join(','),
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
