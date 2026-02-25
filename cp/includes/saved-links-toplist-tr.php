        <tr id="item-<?php echo $item['toplist_id']; ?>">
          <td class="ta-center">
            <input type="checkbox" value="<?php echo $item['toplist_id']; ?>"/>
          </td>
          <td class="ta-center">
            <?php echo $item['toplist_id']; ?>
          </td>
          <td>
            <?php echo $item['source_type'] == 'all_links' ? 'All Links' : 'Specific Links'; ?>
          </td>
          <td>
            <?php echo $item['template']; ?>
          </td>
          <td>
            <?php echo $item['outfile']; ?>
          </td>
          <td class="ta-center">
            <?php
            $time_file = DIR_TIMES . '/saved-link-toplist-' . $original['toplist_id'];
            echo file_exists($time_file) ? date('Y-m-d H:i', filemtime($time_file)) : 'Never';
            ?>
          </td>
          <td class="ta-right">
            <div class="p-relative">
              <a href="_xSavedLinkToplistsBuild" data="&toplist_id=<?php echo $item['toplist_id']; ?>" class="xhr" confirm="Build this saved link toplist now?">
                <img src="images/build-22x22.png" border="0" title="Build"/>
              </a>

              <a href="_xSavedLinkToplistsEditShow" data="&toplist_id=<?php echo $item['toplist_id']; ?>" class="dialog">
                <img src="images/edit-22x22.png" border="0" title="Edit"/>
              </a>

              <a href="_xSavedLinkToplistsDelete" data="&toplist_id=<?php echo $item['toplist_id']; ?>" class="xhr" confirm="Delete this saved link toplist configuration?">
                <img src="images/delete-22x22.png" border="0" title="Delete"/>
              </a>
            </div>
          </td>
        </tr>
