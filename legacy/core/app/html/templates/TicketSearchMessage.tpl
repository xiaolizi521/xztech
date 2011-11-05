{include file="ticket_search_header.tpl"}

<table width="95%" class="ion">
<tr>
	<td height="34"  colspan="5">
	<div style="font-size:20;margin-left: 40%;font-weight:bold;color:#996699;"> Ticket Text Search </div>	
	</td>
</tr>

<form action="/ticket_search/TicketSearchView.php">
<INPUT type="hidden" name="view" value="ticketsearch">

<tr>
	<th class="subtitle">
		<div align="center">
		Search Text :
		<INPUT type="text" name="search_string" size="30" value="{$ticket.search_string}">
		</div>
	</th>
	<th class="subtitle" nowrap=1>
		<div align="center">
		Show
		<SELECT name="results_per_page" class="withtext" id="results_per_page">
			<OPTION value="10">10</OPTION>
			<OPTION value="25">25</OPTION>
			<OPTION value="50">50</OPTION>
        </SELECT>
		Results Per Page
		</div>
    </th>
    <th class="subtitle">
		<div align="center">
		Ticket Age :
		<SELECT name="age" class="withtext" id="result_age">
			<OPTION value="">----</OPTION>
			<OPTION value="7">7 days</OPTION>
			<OPTION value="14">2 weeks</OPTION>
			<OPTION value="21">3 weeks</OPTION>
			<OPTION value="30">1 month</OPTION>
			<OPTION value="60">2 months</OPTION>
			<OPTION value="90">3 months</OPTION>
			<OPTION value="180">6 months</OPTION>
			<OPTION value="360">1 year</OPTION>
			<OPTION value="720">2 years</OPTION>
		</SELECT>
		</div>
	</th>
	<th class="subtitle">
		<div align="center">
		Search By Account Number :
		<INPUT type="text" name="limit_account_num" size="6" value="" />
		(optional)
		</div>
	</th>
	<th class="subtitle-last"><div align="center"><INPUT type="submit" value="SEARCH" class="textbutton" /></div></th>
</tr>

</FORM>

<tr>
	<td colspan="7">
		<table width="100%">
		<tr>
			<th class="subtitle-last">FULL COMMENT</th>
			<th class="subtitle-last" nowrap="nowrap">
				<div align="right">
					<a href="{"TicketSearchView.php?search_string=`$ticket.search_string`&next_start=`$ticket.next_start`&results_per_page=`$ticket.results_per_page`&age=`$ticket.age`"}" class="textbutton"> RETURN TO SEARCH </a>
					<a href="{"/py/ticket/view.pt?ref_no=`$ticket.ref_no`"}" class="textbutton"> VIEW TICKET </a>
				</div>
			</th>
		</tr>
		<tr>
			<td class="cell-last" colspan="2">{$ticket.message}</td>
		</tr>
		</table>
	</td>
</tr>	
</table>


{php}
print page_stop(); 
{/php}

</html>
