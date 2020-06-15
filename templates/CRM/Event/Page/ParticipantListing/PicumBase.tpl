<p>&nbsp;</p>
{if $rows}
    {include file="CRM/common/pager.tpl" location="top"}
       <table class="selector" cellpadding="0" cellspacing="0" border="0">
         <tr class="columnheader">
        {foreach from=$headers item=header}
        <th scope="col">
        {if $header.sort}
          {assign var='key' value=$header.sort}
          {$sort->_response.$key.link}
        {else}
          {$header.name}
        {/if}
        </th>
      {/foreach}
         </tr>
      {foreach from=$rows item=row}
         <tr class="{cycle values="odd-row,even-row"} crm-participant-name">
             {foreach from=$row item=col}
                <td style="text-align: left">{$col}</td>
             {/foreach}
         </tr>
      {/foreach}
      </table>
    {include file="CRM/common/pager.tpl" location="bottom"}
{else}
    <div class='spacer'></div>
    <div class="messages status no-popup">
    <div class="icon inform-icon"></div>
        {ts}There are currently no participants registered for this event.{/ts}
    </div>
{/if}
