      <div id="dialog-content">

        <div id="dialog-header">
          <img src="images/dialog-close-22x22.png" id="dialog-close"/>
          Country Stats for <?php echo $item['domain']; ?>
        </div>

        <div id="dialog-panel" dwidth="850px">
          <div>

            <div class="block-center fw-bold ta-center" style="width: 800px; font-size: 110%; margin-bottom: 10px;">
              <span class="option option-selected" style="width: 32%;">In</span>
              <span class="option" style="width: 32%;">Out</span>
              <span class="option" style="width: 32%;">Clicks</span>
            </div>

            <div class="block-center" id="ammap" style="width: 800px; height: 400px; border: 1px solid #666; background: transparent url(images/activity-32x32.gif) no-repeat 50% 50%;"></div>

          </div>
        </div>

        <div id="dialog-buttons">
          <input type="button" id="dialog-button-cancel" value="Close" style="margin-left: 10px;" />
        </div>

      </div>

<script language="JavaScript" type="text/javascript">
var ammap = null;
var rufflePlayer = null;

function reloadData(stat, src)
{
    if (rufflePlayer) {
        $('#ammap').css({visibility: 'hidden'});
        // Reload the SWF with new data URL
        var dataUrl = 'index.php?r=_xTradesCountriesData&stat=' + stat + '&domain=<?php echo $item['domain']; ?>';
        rufflePlayer.contentWindow.postMessage({type: 'reloadData', url: dataUrl}, '*');
        $(src).addClass('option-selected').siblings().removeClass('option-selected');
    }
}

function amMapCompleted(map_id)
{
    ammap = document.getElementById('ammap-ruffle-object');
    if (ammap) {
        $(ammap).css({visibility: 'visible'});
    }
}

function amProcessCompleted(map_id, process_name)
{
    if( process_name == 'reloadData' )
    {
        $('#ammap').css({visibility: 'visible'});
    }
}

// Initialize Ruffle
window.RufflePlayer = window.RufflePlayer || {};
window.RufflePlayer.config = {
    "autoplay": "on",
    "unmuteOverlay": "hidden",
    "backgroundColor": "#ffffff"
};

$(document).ready(function() {
    const ruffle = window.RufflePlayer.newest();
    rufflePlayer = ruffle.createPlayer();
    rufflePlayer.id = "ammap-ruffle-object";
    rufflePlayer.style.width = "800px";
    rufflePlayer.style.height = "400px";
    
    // Set Flash variables as URL parameters
    var swfUrl = "swf/ammap.swf?" + 
        "path=" + encodeURIComponent("swf/") + "&" +
        "data_file=" + encodeURIComponent("index.php?r=_xTradesCountriesData&stat=In&domain=<?php echo $item['domain']; ?>") + "&" +
        "settings_file=" + encodeURIComponent("assets/ammap-settings.xml") + "&" +
        "map_id=ammap-ruffle-object";
    
    const container = document.getElementById('ammap');
    container.innerHTML = '';
    container.appendChild(rufflePlayer);
    rufflePlayer.load(swfUrl).then(() => {
        $(rufflePlayer).css({visibility: 'visible'});
    });
});

$('span.option')
.click(function()
{
    reloadData($(this).text(), this);
});
</script>