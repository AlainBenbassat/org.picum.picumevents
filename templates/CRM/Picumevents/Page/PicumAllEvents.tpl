
<table class="report-layout display">
    <thead>
    <tr>
        <th><a href="{$currentURL}&newsort=2">Event Type</a></th>
        <th><a href="{$currentURL}&newsort=3">Date</a></th>
        <th><a href="{$currentURL}&newsort=4">Event</a></th>
        <th><a href="{$currentURL}&newsort=5">Output Number</a></th>
        <th><a href="{$currentURL}&newsort=6">Meeting Place</a></th>
        <th><a href="{$currentURL}&newsort=7">Registered/Attended</a></th>
        <th><a href="{$currentURL}&newsort=8">Cancelled/No-Show</a></th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$events item=row}
        <tr class="{cycle values="odd-row,even-row"}">
            <td>{$row.event_type}</td>
            <td>{$row.start_date}</td>
            <td><a href="admin.php?page=CiviCRM&q=civicrm%2Fevent%2Fmanage%2Fsettings&reset=1&action=update&id={$row.id}">{$row.event}</a></td>
            <td>{$row.output_number}</td>
            <td>{$row.meeting_place}</td>
            <td><a href="admin.php?page=CiviCRM&q=civicrm%2Fevent%2Fsearch&reset=1&force=1&event={$row.id}&status=true">{$row.num_participant_pos}</a></td>
            <td><a href="admin.php?page=CiviCRM&q=civicrm%2Fevent%2Fsearch&reset=1&force=1&event={$row.id}&status=false">{$row.num_participant_neg}</a></td>
        </tr>
    {/foreach}
    </tbody>
</table>
