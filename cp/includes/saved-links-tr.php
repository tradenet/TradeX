        <?php
        $thumb_count = empty($item['custom_thumbs']) ? 0 : count(array_filter(array_map('trim', explode("\n", $item['custom_thumbs']))));
        ?>
        <tr id="item-<?php echo $item['link_id']; ?>">
          <td class="ta-center">
            <input type="checkbox" value="<?php echo $item['link_id']; ?>"/>
          </td>
          <td>
            <?php echo $item['link_id']; ?>
          </td>
          <td>
            <?php echo $item['link_name']; ?>
          </td>
          <td class="ta-center">
            <?php echo ucfirst($item['type']); ?>
          </td>
          <td class="ta-center">
            <?php echo $thumb_count; ?>
          </td>
          <td class="ta-right">
            <div class="p-relative">
              <a href="_xSavedLinkCopyUrl" data="&id=<?php echo urlencode($item['link_id']); ?>" class="xhr" title="Copy Toplist URL">
                <img src="images/link-22x22.png" border="0" title="Copy Toplist URL"/>
              </a>

              <a href="_xSavedLinkEditShow" data="&id=<?php echo urlencode($item['link_id']); ?>" class="dialog">
                <img src="images/edit-22x22.png" border="0" title="Edit"/>
              </a>

              <a href="_xSavedLinkDelete" data="&id=<?php echo urlencode($item['link_id']); ?>" class="xhr" confirm="Delete this saved link?">
                <img src="images/delete-22x22.png" border="0" title="Delete"/>
              </a>
            </div>
          </td>
        </tr>
