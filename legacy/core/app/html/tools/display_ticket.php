<% 
		  for ($i = 0; $i <= $ticket_count; $i++)
			{
		    $counter = $i + 1;


//----------------------------GENERATION
		
		//generate ticket numbers
			eval("\$ticket_num_array[$i] = \$ticket$counter;");
			
		//generate ticket statuses
			eval("\$ticket_status_array[$i] = \$ticket_status$counter;");

		//generate ticket SIDs
			eval("\$ticket_sid_array[$i] = \$ticket_sid$counter;");


//-----------------------------OUTPUT
	
		//print ticket number
			%>
			<BR>
			<A HREF="http://support.rackspace.com/cgi-bin/racksup/r?11=<% print $ticket_num_array[$i]; %>&130=<% print $ticket_sid_array[$i]; %>">
			<% print $ticket_num_array[$i]; %>
			</A>
			<BR>
			<%

		//print ticket status
			print $ticket_status_array[$i];
			print("<BR>");
			
			}
%> 