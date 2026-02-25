      <?php
      $editing = isset($item) && !empty($item) && !empty($item['toplist_id']);
      if( !$editing )
      {
          require_once 'textdb.php';
          $db = new ToplistsSavedLinksDB();
          $item = $db->Defaults();
      }

      if( !isset($item) || !is_array($item) )
      {
          $item = array();
      }

      $item_defaults = array(
          'toplist_id'       => '',
          'source_type'      => 'all_links',
          'link_ids'         => '',
          'template'         => 'toplist-saved-links-36.tpl',
          'outfile'          => '',
          'sort_by'          => 'link_name',
          'max_thumbnails'   => 36,
          'rebuild_interval' => '',
          'last_build'       => 0,
      );

      $item = array_merge($item_defaults, $item);

      // Load saved links for the specific-links multi-select
      require_once 'dirdb.php';
      $saved_links_db = new SavedLinksDB();
      $all_saved_links = $saved_links_db->RetrieveAll('link_name');

      // Parse selected link_ids into array
      $selected_link_ids = array();
      if( !empty($item['link_ids']) )
      {
          $selected_link_ids = array_map('trim', explode(',', $item['link_ids']));
      }

      // Collect available saved-links toplist templates
      $tpl_files = glob(DIR_TEMPLATES . '/toplist-saved-links-*.tpl');
      $templates = array();
      foreach( $tpl_files as $tpl )
      {
          $templates[] = basename($tpl);
      }
      sort($templates);

      $sort_options = array(
          'link_name'    => 'Link Name',
          'link_id'      => 'Link ID',
          'date_created' => 'Date Created',
      );
      ?>

      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          <?php echo $editing ? 'Edit' : 'Add'; ?> a Saved Link Toplist
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="700px">
            <div style="padding-top: 2px;">

              <div class="field">
                <label class="short">Source:</label>
                <span>
                  <select name="source_type" id="sltpl-source-type">
                    <option value="all_links"       <?php echo $item['source_type'] == 'all_links'       ? 'selected="selected"' : ''; ?>>All Saved Links</option>
                    <option value="specific_links"  <?php echo $item['source_type'] == 'specific_links'  ? 'selected="selected"' : ''; ?>>Specific Links</option>
                  </select>
                </span>
              </div>

              <div class="field" id="sltpl-field-link-ids" style="<?php echo $item['source_type'] == 'specific_links' ? '' : 'display:none;'; ?>">
                <label class="short">Links:</label>
                <span>
                  <select name="link_ids_multi[]" multiple="multiple" size="8" style="min-width: 300px;">
                    <?php foreach( $all_saved_links as $sl ): ?>
                    <option value="<?php echo htmlspecialchars($sl['link_id']); ?>"
                      <?php echo in_array($sl['link_id'], $selected_link_ids) ? 'selected="selected"' : ''; ?>>
                      <?php echo htmlspecialchars($sl['link_name'] . ' (' . $sl['link_id'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                  <br/><small>Hold Ctrl / Cmd to select multiple. Order matches selection order.</small>
                </span>
              </div>

              <div class="field">
                <label class="short">Template:</label>
                <span>
                  <select name="template">
                    <?php foreach( $templates as $tpl ): ?>
                    <option value="<?php echo htmlspecialchars($tpl); ?>"
                      <?php echo $item['template'] == $tpl ? 'selected="selected"' : ''; ?>>
                      <?php echo htmlspecialchars($tpl); ?>
                    </option>
                    <?php endforeach; ?>
                    <?php if( empty($templates) ): ?>
                    <option value="toplist-saved-links-36.tpl" selected="selected">toplist-saved-links-36.tpl</option>
                    <?php endif; ?>
                  </select>
                </span>
              </div>

              <div class="field">
                <label class="short">Output File:</label>
                <span><input name="outfile" value="<?php echo htmlspecialchars($item['outfile']); ?>" size="70" type="text" placeholder="/path/to/output.html"/></span>
              </div>

              <div class="field">
                <label class="short">Sort By:</label>
                <span>
                  <select name="sort_by">
                    <?php echo form_options_hash($sort_options, $item['sort_by']); ?>
                  </select>
                </span>
              </div>

              <div class="field">
                <label class="short">Max Thumbnails:</label>
                <span><input name="max_thumbnails" value="<?php echo (int) $item['max_thumbnails']; ?>" size="6" type="text"/></span>
              </div>

              <div class="field">
                <label class="short">Auto-Rebuild:</label>
                <span>
                  Every <input name="rebuild_interval" value="<?php echo htmlspecialchars($item['rebuild_interval']); ?>" size="6" type="text" placeholder="0"/>
                  minutes &nbsp;<small>(leave blank or 0 to disable)</small>
                </span>
              </div>

            </div>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="<?php echo $editing ? 'Update' : 'Add'; ?> Toplist" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xSavedLinkToplists<?php echo $editing ? 'Edit' : 'Add'; ?>"/>
          <input type="hidden" name="toplist_id" value="<?php echo $item['toplist_id']; ?>"/>
        </form>

      </div>

<script language="JavaScript" type="text/javascript">
$('#sltpl-source-type').change(function()
{
    if( $(this).val() == 'specific_links' )
    {
        $('#sltpl-field-link-ids').show();
    }
    else
    {
        $('#sltpl-field-link-ids').hide();
    }
    $('#dialog').center(document);
});
</script>
