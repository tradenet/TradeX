      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Custom Thumbnail Toplist Generator
        </div>

        <form id="custom-thumbs-form" method="post" action="xhr.php" class="xhr-form">

          <div id="dialog-panel" dwidth="950px">
            <div style="padding-top: 2px;">

              <fieldset>
                <legend>Instructions</legend>
                <div style="padding: 10px; line-height: 1.6;">
                  <strong>How to use:</strong>
                  <ol>
                    <li>Select trades from the list below</li>
                    <li>For each selected trade, enter custom thumbnail URLs (one per line)</li>
                    <li>Save the custom thumbnails to the trades</li>
                    <li>Go to <strong>Toplists</strong> to create/build a toplist using the <strong>toplist-random-36-custom-thumbs.tpl</strong> template</li>
                  </ol>
                  <p><strong>Note:</strong> Custom thumbnails must be full URLs (e.g., https://example.com/thumb1.jpg)</p>
                </div>
              </fieldset>

              <fieldset>
                <legend>Select Trades</legend>
                <div class="field">
                  <label>Filter by Group:</label>
                  <span>
                    <select id="filter-group">
                      <option value="">-- ALL --</option>
                      <?php
                      $groups = array_map('trim', file(FILE_GROUPS));
                      echo form_options($groups);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label>Filter by Category:</label>
                  <span>
                    <select id="filter-category">
                      <option value="">-- ALL --</option>
                      <?php
                      $categories = array_map('trim', file(FILE_CATEGORIES));
                      echo form_options($categories);
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label>Select Trades:</label>
                  <span>
                    <select id="trade-selector" multiple="multiple" size="10" style="width: 500px;">
                      <?php
                      require_once 'dirdb.php';
                      $db = new TradeDB();
                      $trades = $db->RetrieveAll();
                      foreach($trades as $trade) {
                          $label = $trade['domain'];
                          if (!empty($trade['site_name'])) {
                              $label .= ' (' . $trade['site_name'] . ')';
                          }
                          $group = isset($trade['groups']) ? $trade['groups'] : '';
                          $category = isset($trade['categories']) ? $trade['categories'] : '';
                          echo '<option value="' . htmlspecialchars($trade['domain']) . '" data-group="' . htmlspecialchars($group) . '" data-category="' . htmlspecialchars($category) . '">' . htmlspecialchars($label) . '</option>';
                      }
                      ?>
                    </select>
                  </span>
                </div>

                <div class="field">
                  <label></label>
                  <span>
                    <button type="button" id="btn-add-selected" class="button-small">Add Selected Trades →</button>
                    <button type="button" id="btn-clear-all" class="button-small">Clear All</button>
                  </span>
                </div>
              </fieldset>

              <fieldset>
                <legend>Custom Thumbnails</legend>
                <div id="custom-thumbs-container" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                  <p class="notice">No trades selected. Please select trades from the list above.</p>
                </div>
              </fieldset>

            </div>
          </div>

          <div id="dialog-help">
            <a href="docs/custom-thumbs.html" target="_blank"><img src="images/help-22x22.png"></a>
          </div>

          <div id="dialog-buttons">
            <img src="images/activity-16x16.gif" height="16" width="16" border="0" title="Working..." />
            <input type="submit" id="button-save" value="Save Custom Thumbnails" />
            <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;" />
          </div>

          <input type="hidden" name="r" value="_xCustomThumbsSave"/>
        </form>

      </div>


<script language="JavaScript" type="text/javascript">
var selectedTrades = {};

$(function()
{
    // Filter trades by group/category
    function filterTrades() {
        var selectedGroup = $('#filter-group').val();
        var selectedCategory = $('#filter-category').val();

        $('#trade-selector option').each(function() {
            var $opt = $(this);
            var group = $opt.data('group') || '';
            var category = $opt.data('category') || '';
            var show = true;

            if (selectedGroup && group !== selectedGroup) {
                show = false;
            }
            if (selectedCategory && category !== selectedCategory) {
                show = false;
            }

            if (show) {
                $opt.show();
            } else {
                $opt.hide();
            }
        });
    }

    $('#filter-group, #filter-category').change(filterTrades);

    // Add selected trades to custom thumbs area
    $('#btn-add-selected').click(function() {
        $('#trade-selector option:selected').each(function() {
            var domain = $(this).val();
            if (!selectedTrades[domain]) {
                selectedTrades[domain] = {
                    label: $(this).text(),
                    thumbs: ''
                };
            }
        });
        renderCustomThumbs();
    });

    // Clear all selected trades
    $('#btn-clear-all').click(function() {
        if (confirm('Remove all trades and their custom thumbnails?')) {
            selectedTrades = {};
            renderCustomThumbs();
        }
    });

    // Render the custom thumbs input areas
    function renderCustomThumbs() {
        var $container = $('#custom-thumbs-container');
        $container.empty();

        if (Object.keys(selectedTrades).length === 0) {
            $container.html('<p class="notice">No trades selected. Please select trades from the list above.</p>');
            return;
        }

        $.each(selectedTrades, function(domain, data) {
            var $tradeBlock = $('<div class="custom-thumb-block" style="margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></div>');
            
            var $header = $('<div style="margin-bottom: 8px;"></div>');
            $header.append('<strong style="font-size: 14px;">' + $('<div>').text(data.label).html() + '</strong>');
            $header.append(' <button type="button" class="btn-remove-trade button-small" data-domain="' + $('<div>').text(domain).html() + '" style="margin-left: 10px;">Remove</button>');
            
            var $textarea = $('<textarea name="thumbs[' + $('<div>').text(domain).html() + ']" rows="4" style="width: 100%; font-family: monospace; font-size: 11px;" placeholder="Enter custom thumbnail URLs, one per line&#10;Example:&#10;https://example.com/thumb1.jpg&#10;https://example.com/thumb2.jpg"></textarea>');
            $textarea.val(data.thumbs);

            $tradeBlock.append($header);
            $tradeBlock.append('<div style="margin-bottom: 5px; font-size: 11px; color: #666;">Custom Thumbnail URLs (one per line):</div>');
            $tradeBlock.append($textarea);
            $tradeBlock.append('<div style="margin-top: 5px; font-size: 11px; color: #666;">Total thumbs: <span class="thumb-count">0</span></div>');

            $container.append($tradeBlock);

            // Update count
            $textarea.on('input', function() {
                var lines = $(this).val().split('\n').filter(function(line) { return line.trim() !== ''; });
                $(this).closest('.custom-thumb-block').find('.thumb-count').text(lines.length);
            }).trigger('input');
        });

        // Handle remove trade button
        $('.btn-remove-trade').click(function() {
            var domain = $(this).data('domain');
            delete selectedTrades[domain];
            renderCustomThumbs();
        });
    }

    // Form submission
    $('#custom-thumbs-form')
    .bind('form-success', function(e, data)
    {
        $.growl(data[AjaxResponse.KEY_MESSAGE]);
        
        // Optionally reload trade data to get updated custom_thumbs
        // but for now just keep the UI as-is
    });

    // Initial render
    renderCustomThumbs();
});
</script>

<style>
.custom-thumb-block {
    background: #f9f9f9;
}
.button-small {
    padding: 4px 12px;
    font-size: 12px;
    border: 1px solid #ccc;
    background: #fff;
    border-radius: 3px;
    cursor: pointer;
}
.button-small:hover {
    background: #f0f0f0;
}
.notice {
    color: #666;
    font-style: italic;
    padding: 20px;
    text-align: center;
}
</style>
