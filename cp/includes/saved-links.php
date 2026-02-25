<?php
include 'global-header.php';
include 'global-menu.php';

require_once 'dirdb.php';
$db = new SavedLinksDB();
$links = $db->RetrieveAll();
?>

      <div class="main-content-header">
        Saved Links: <span id="num-items"><?php echo count($links); ?></span> Total
      </div>

      <div class="main-content-section">

        <table id="items-table" class="wa-table" cellspacing="0" cellpadding="0" border="0">
          <thead>
            <tr>
              <td class="ta-center">Link ID</td>
              <td>Link Name</td>
              <td class="ta-center">Type</td>
              <td class="ta-center">Thumbnails</td>
              <td class="ta-center">Functions</td>
            </tr>
          </thead>
          <tbody>
            <?php
            if (count($links) == 0):
            ?>
            <tr>
              <td colspan="5" style="padding: 15px; text-align: center;">
                No saved links found. Use the <a href="_xLinkGenerateShow" class="dialog">Link Generator</a> and check "Save this link with custom thumbnails" to create saved links.
              </td>
            </tr>
            <?php
            else:
              foreach ($links as $link):
                $thumb_count = empty($link['custom_thumbs']) ? 0 : count(explode("\n", trim($link['custom_thumbs'])));
            ?>
            <tr>
              <td class="ta-center"><?php echo htmlspecialchars($link['link_id']); ?></td>
              <td><?php echo htmlspecialchars($link['link_name']); ?></td>
              <td class="ta-center"><?php echo htmlspecialchars(ucfirst($link['type'])); ?></td>
              <td class="ta-center"><?php echo $thumb_count; ?></td>
              <td class="ta-center">
                <a href="_xSavedLinkEditShow&id=<?php echo urlencode($link['link_id']); ?>" class="dialog function">Edit</a>
                <a href="_xSavedLinkDelete&id=<?php echo urlencode($link['link_id']); ?>" class="xhr function confirm">Delete</a>
                <a href="_xSavedLinkCopyUrl&id=<?php echo urlencode($link['link_id']); ?>" class="xhr function">Copy Toplist URL</a>
              </td>
            </tr>
            <?php
              endforeach;
            endif;
            ?>
          </tbody>
        </table>

        <?php if (count($links) > 0): ?>
        <div style="margin-top: 20px; padding: 15px; background: #f0f0f0; border: 1px solid #ccc;">
          <h3>Generate Toplist HTML</h3>
          <form method="post" action="xhr.php" class="xhr-form">
            <div class="field">
              <label>Select Links:</label>
              <select name="link_ids[]" multiple="multiple" size="10" style="width: 500px;">
                <?php foreach ($links as $link): ?>
                <option value="<?php echo htmlspecialchars($link['link_id']); ?>">
                  <?php echo htmlspecialchars($link['link_name'] . ' (' . $link['link_id'] . ')'); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <button type="submit" class="button">Generate Toplist URL</button>
            </div>
            <div id="toplist-url-result" class="d-none" style="margin-top: 10px;">
              <label>Toplist URL:</label><br/>
              <input type="text" id="toplist-url-field" style="width: 100%; padding: 5px;" readonly />
              <p style="font-size: 11px; color: #666;">Use this URL to display a toplist of the selected saved links with their custom thumbnails.</p>
            </div>
            <input type="hidden" name="r" value="_xSavedLinksGenerateToplist"/>
          </form>
        </div>
        <?php endif; ?>

      </div>

<script type="text/javascript">
$(function() {
    $('form[action="xhr.php"]').bind('form-success', function(e, data) {
        if (data.toplist_url) {
            $('#toplist-url-field').val(data.toplist_url);
            $('#toplist-url-result').removeClass('d-none').show();
            $('#toplist-url-field').select();
        }
    });
});
</script>
