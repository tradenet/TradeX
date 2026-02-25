      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Edit Saved Link: <?php echo htmlspecialchars($item['link_id']); ?>
        </div>

        <form method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="700px">
            <div style="padding-top: 2px;">

              <fieldset>
                <legend>Link Settings</legend>

                <div class="field">
                  <label>Link ID:</label>
                  <span><strong><?php echo htmlspecialchars($item['link_id']); ?></strong> (cannot be changed)</span>
                </div>

                <div class="field">
                  <label>Link Name:</label>
                  <span><input type="text" name="link_name" value="<?php echo htmlspecialchars($item['link_name']); ?>" size="60"></span>
                </div>

                <div class="field">
                  <label>Type:</label>
                  <span><strong><?php echo htmlspecialchars(ucfirst($item['type'])); ?></strong></span>
                </div>

                <?php if ($item['type'] == 'percent'): ?>
                <div class="field">
                  <label>% to Content:</label>
                  <span><?php echo htmlspecialchars($item['percent']); ?>% <?php if ($item['flag_fc'] == '1') echo '(First click to content)'; ?></span>
                </div>
                <?php endif; ?>

                <?php if ($item['type'] == 'scheme'): ?>
                <div class="field">
                  <label>Skim Scheme:</label>
                  <span><?php echo htmlspecialchars($item['skim_scheme']); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($item['type'] == 'trade'): ?>
                <div class="field">
                  <label>Trade:</label>
                  <span><?php echo htmlspecialchars($item['trade']); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($item['content_url'])): ?>
                <div class="field">
                  <label>Content URL:</label>
                  <span><?php echo htmlspecialchars($item['content_url']); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($item['category'])): ?>
                <div class="field">
                  <label>Category:</label>
                  <span><?php echo htmlspecialchars($item['category']); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($item['group'])): ?>
                <div class="field">
                  <label>Group:</label>
                  <span><?php echo htmlspecialchars($item['group']); ?></span>
                </div>
                <?php endif; ?>

                <div class="field">
                  <label>Custom Thumbnails:</label>
                  <span><textarea name="custom_thumbs" rows="8" style="width: 500px;"><?php echo htmlspecialchars($item['custom_thumbs']); ?></textarea></span>
                </div>

              </fieldset>

            </div>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save Changes" />
            <input type="button" id="dialog-button-cancel" value="Cancel" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xSavedLinkEdit"/>
          <input type="hidden" name="link_id" value="<?php echo htmlspecialchars($item['link_id']); ?>"/>
        </form>

      </div>
