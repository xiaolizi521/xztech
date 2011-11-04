{include file="ticket_search_header.tpl"}
<table width="95%" class="ion">
<tr>
	<td height="34"  colspan="5">
	  <div style="font-size:20;margin-left: 40%;font-weight:bold;color:#996699;"> Ticket Text Search </div>	
	</td>
</tr>

<form action="/ticket_search/TicketSearchView.php">
<input type="hidden" name="view" value="ticketsearch" />

<tr>
	<th class="subtitle-middle" nowrap="nowrap">
		<div align="center">
		Search Text :
		<input type="text" name="search_string" size="15" value="{$ticket.search_string}" />
		</div>
	</th>
	<th class="subtitle-middle" nowrap=1>
		<div align="center">
		Show
		<select name="results_per_page" class="withtext" id="results_per_page">
			<option value="10">10</option>
			<option value="25">25</option>
			<option value="50">50</option>
        </select>
		Results Per Page
		</div>
    </th>
    <th class="subtitle-middle" nowrap="nowrap">
		<div align="center">
		Ticket Age :
		<select name="age" class="withtext" id="result_age">
			<option value="7">7 days</option>
			<option value="14">2 weeks</option>
			<option value="21">3 weeks</option>
			<option value="30">1 month</option>
			<option value="60">2 months</option>
			<option value="90">3 months</option>
			<option value="180">6 months</option>
			<option value="360">1 year</option>
			<option value="720">2 years</option>
		</select>
		</div>
	</th>
	<th class="subtitle-middle" nowrap="nowrap">
		<div align="center">
		Search By Account Number :
		<input type="text" name="limit_account_num" size="6" {if $ticket.limit_account_num}value="{$ticket.limit_account_num}"{else}value=""{/if} />
		(optional)
		</div>
	</th>
	<th class="subtitle-middle-last" nowrap="nowrap"><div align="center"><INPUT type="submit" value="SEARCH" class="textbutton" /></div></th>
</tr>

</form>

<tr>
	<td colspan="5">
		<table width="100%">
		
		{if !empty( $ticket.result_list )}
		<tr>
			<th class="title-naked" colspan="2">
				<div align="center">
				{if !empty($ticket.show_back_link)}
			    	<a href="{"TicketSearchView.php?search_string=`$ticket.search_string`&next_start=`$ticket.prev_start`&results_per_page=`$ticket.results_per_page`&age=`$ticket.age`" class="textbutton"}"> LAST {$ticket.results_per_page} </a>
				{else}
					START
				{/if}
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				Showing Results {$ticket.last_start} - 
		  		{if empty( $ticket.num_left )} 
		      		{$ticket.next_start}
		  		{else}
		      		{$ticket.last_start+$ticket.num_left}
		 		{/if}
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				{if !empty($ticket.show_next_link)}
					<a href="{"TicketSearchView.php?search_string=`$ticket.search_string`&next_start=`$ticket.next_start`&results_per_page=`$ticket.results_per_page`&age=`$ticket.age`" class="textbutton"}"> NEXT {$ticket.results_per_page}</a>
				{else}
					END
				{/if}
				</div>
			</th>
		</tr>
		
		{foreach item=a_ticket from=$ticket.result_list}	
		<tr>
		 	<th width="75%" class="subtitle-middle-last">#{$a_ticket.ref_no} - {$a_ticket.subject|truncate:80:"...":true}</th>
			<th width="25%" class="subtitle-middle-last" nowrap="nowrap">
				<div align="right">
					<a href="{"TicketSearchMessageView.php?message_id=`$a_ticket.message_id`&ticket_id=`$a_ticket.ticket_id`&search_string=`$ticket.search_string`&next_start=`$ticket.last_start`&results_per_page=`$ticket.results_per_page`&age=`$ticket.age`"}" class="textbutton">VIEW FULL COMMENT</a>
		    		<a href="{"/py/ticket/view.pt?ref_no=`$a_ticket.ref_no`"}" class="textbutton" > VIEW TICKET</a>
				</div>
			</th>
		</tr>
		<tr>
			<td class="cell-last" colspan="2">{$a_ticket.message}</td>
		</tr>
		{/foreach}
		
	    {elseif !empty($ticket.search_string)}
	    
            <tr>
            	<td class="cell-last" colspan="2" style="font-size:16;"><BR>No Results Found</td>
            </tr>
            
		{/if}
		
		</table>
	</td>
</tr>
</table>

{php}
print page_stop(); 
{/php}
</html>

