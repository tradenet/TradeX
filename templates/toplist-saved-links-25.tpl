<table cellspacing="0" cellpadding="5" border="0">
{assign var=$base_url_https code=str_replace('http://','https://',$g_config.base_url)}
{foreach var=$link from=$g_links counter=$counter}
  {if $counter % 5 == 1}<tr>{/if}
    <td align="center" valign="top">
    {if !empty($link)}
      <a href="{$link.out_url}" target="_blank">
        {if !empty($link.thumb_url)}
        <img src="{$link.thumb_url}" border="0" alt="{$link.link_name}" /><br />
        {else}
        <img src="{$base_url_https}/images/no-thumb.jpg" border="0" alt="{$link.link_name}" /><br />
        {/if}
        {$link.link_name}
      </a>
    {else}
      <a href="{$base_url_https}/register.php" target="_blank">
        <img src="{$base_url_https}/images/add-your-site.jpg" border="0" alt="Add Your Site" /><br />
        Add Your Site
      </a>
    {/if}
    </td>
  {if $counter % 5 == 0}</tr>{/if}
  {if $counter == 25}{foreachdone}{/if}
{/foreach}
{* Close any remaining open table rows *}
{if $counter % 5 != 0}</tr>{/if}
</table>
