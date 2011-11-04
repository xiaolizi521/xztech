<html>

<?php error_reporting(E_ALL); 		include_once('/var/www/html/rackwatch/includes.php'); ?>


	<head>
	
		<script type="text/javascript" src="prototype.js"></script>
	
		<style type="text/css" title=currentStyle" media="screen">
	
			@import "style.css";
	
		</style>
                <?php include("rackwatch/rackwatch.php"); if($_GET['host']){$form_host = $_GET['host'];}else{$form_host = 'localhost';}?>	
	</head>
	
	<body onload="init()">
	
			<h1>Rackwatch Support Utility</h1>
		
		<hr />
		<h2>Introduction</h2>
		<blockquote>The purpose of this tool is to assist in network diagosis of a customers machine, as well as the determination of whether or not a rackwatch alert is true or false. If an alert, for example SSH or SMTP, is present even though the port is accessible from your local box and appears to be open, use the below options to test the port from the local Rackwatch servers as well as from the Rackwatch servers around the world. This tool can provide a great deal of insight into potential routing issues, as well as port issues.</blockquote>
                <blockquote><font color="red">***For the new DFW pollers (onms-3.dfw1.rackspace.com or onms-4.dfw1.rackspace.com), please enable "all DCs" and look at the results from "onms-1.dfw2.corp.rackspace.com" or "onms-2.dfw2.corp.rackspace.com" respectively.***</font></blockquote>

		<form action="javascript:performTests();" name="rackwatch" id="rackwatch">
		
		<table>
			
			<tr>
				<td>
				
                                        Hostname: <?php echo '<input name="host" id="host" type="text" value="'.$form_host.'" />'; ?>				
				</td>
			
			</tr>
			
			<tr>
			
				<td>
					
					Test:
					
<?php				

	foreach($services_dn as $name => $value):
		
		print("<span class='inputForm'>\n");
		
		if ($name == "ping"):
	
			print($value . ": <input name='test' type='radio' id='".$name."' value='".$name."' checked/>\n");
			
		else:
		
			print($value . ": <input name='test' type='radio' id='".$name."' value='".$name."' />\n");

		endif;
		
		print("</span>\n");
	
	endforeach;
?>
				</td>			
	
			</tr>
			
			<tr>
			
				<td>
			
					Location: 
			
					All DCs<input name="location" type="radio" id="location" value="all" />
			
					Local<input name="location" type="radio" id="location" value="local" checked />
			
				</td>
			
			</tr>
			
			<tr>
			
				<td>
			
					<input type="button" name="button" value="Submit" onClick="performTests()" />
		
				</td>
		
			</tr>
		
		</table>
		
		</form>
 		
		
<?php			

	$x = 1;
	$z = 1;
	
	foreach($dcs as $key => $value){
	
		print("<div width='500px' style='word-wrap: break-word; overflow: auto;'>");
		
		print("<table > \n");

		print "<tr> \n";

		for($y=1; $y<3; $y++):	

			if($key == CURR_LOCATION):
				
				print "<td class='pollerheader'> \n";
				
				print '<h4 class="pollerheader">Poller: ' . $pollers[$y-1] . '.' . $value . "</h4>\n";
				
			else:
			
				print "<td class='pollerheader' id='pollerhead".$z."'> \n";
				
				print '<h4 class="pollerheader">Poller: ' . $pollers[$y-1] . '.' . $value . "</h4>\n";
				
				$z++;
				
			endif;
			
		endfor;

		print "</tr> \n";			

		print "<tr> \n";

		for($y=1; $y<3; $y++):	
			
			if($key == CURR_LOCATION):

				print "<td class='local".$rl[$y]."' id='local".$y."'> \n";
				
				print 'Information from poller: ' . $pollers[$y-1] . '.' . $value . "\n";
				
				print "</td> \n";
				
			else:
			
				print "<td class='container".$rl[$y]."' id='remote".$x."'> \n";
				
				print 'Information from poller: ' . $pollers[$y-1] . '.' . $value . "\n";
				
				print "</td> \n";
				
				$x++;
			
			endif;
			
		endfor;
		
		print "</tr> \n";					

		print("</table> \n");
		
		print("</div>");
	}
	
	
?>
<h2>What does this all mean?</h2>
<hr />
<p>This tool is meant to provide insight into how the Rackwatch systems are currently working, and whether or not the particular host you are having issues with is actually accessible from the rackwatch machines. Below you will find some common response to the various issues you may run into.</p>
<h3>Ping does not succeed</h3>
<p>If you have an issue with a poller communicating with a server, this is commonly due to either a firewall or routing issue.

<ol>
<li>In some occasions, routing can be the issue, especially with particularly large customers. If the customer routes 10.0.0.0/8 within their address space, there is a good chance that there are NAT rules that are blocking the ability for rackwatch to communicate with these servers. Rackwatch pollers are represented to all devices by their 10.x.96.0/24 address. You will want to make sure that there are no NAT rules that are conflicting with this particular address space.</li>
<li>Much the same, firewalls have a tendency to block 10.x.96.0/24 spaces for one reason or another.</li>
</ol>
</p>
<h4>Best Practices</h4>
<p>The current best practice recommendation is to open up all 10.x.96.0/24 space to the devices. These IP spaces are specifically reserved for the purpose of Rackwatch monitoring (and subsequently, other monitoring tools). Entry into this VLAN is governed by a very strict security requirement on the boxes in question. As part of our DR plan for Rackwatch, any poller in the other Datacenters is specifically set up to be able to be deployed as a poller for any other datacenter. As such, in the event of a major issue, there is the chance that a poller from any other datacenter could potentially become the active poller. This means that, in the current moment, the following VLANs could all become active pollers:

<ul>
<li>10.1.96.0/24 (San Antonio)</li>
<li>10.2.96.0/24 (London)</li>
<li>10.4.96.0/24 (IAD/Virginia)</li>
<li>10.5.96.0/24 (Dallas)</li>
</ul>

We recommend that all of these VLANs be open and have ample access to the devices.</p>

<h2>Other Services Fail to Respond</h2>
<hr />
<p>The following issues can come up with certain services, and have been witnessed quite often as issues with polling:

<ul>
<li><strong>SSH: </strong>OpenNMS opens and closes an SSH connection every 5 minutes as part of its polling intervals. The connections are empty as they do not send a username or password. Many SSH configurations as well as PortSentry and other active firewall solutions monitor for unsuccessful and empty connection attempts and will actively block them. Depending on the used method, it can occur through IPTables or through the /etc/hosts.allow file. Be sure to check this if you are having SSH issues.</li>
<li><strong>SMTP: </strong>Many SMTP MTAs have a similar throttling configuration to SSH as part of spam and security best practices. As such, the empty connections that OpenNMS opens (OpenNMS opens a socket and sends a HELO, upon response it closes the connection) and the frequency at which they happen (every 5 minutes) tend to cause throttling to occur. Please refer to your appropriate MTA's configuration and support on how to resolve this. It should be possible to whitelist the pollers from this particular issue.</li>
<li><strong>MySQL: </strong>MySQL has a configuration called "max_connect_errors". This configuration option is by default set to 10. Since OpenNMS does not send any data, it is concidered an empty connection and therefore ends up in this pool. After 10 failed connections, it will be restricted with the following message: "Host 'host_name' is blocked because of many connection errors.
Unblock with 'mysqladmin flush-hosts'". Running the given command resolves this issue. More information can be found at <a href="http://dev.mysql.com/doc/refman/5.0/en/blocked-host.html">this article</a> in the MySQL reference manual.</li>
</ul>
<hr />
<h1>Description of Rackwatch Services</h1>
<hr />
<h2>The Pollers</h2>
<p>The Rackwatch Pollers are currently utilizing OpenNMS version 1.3.9 as their polling tool. The pollers are set up in a paired fashion in each datacenter. In San Antonio and London, we are currently utilizing a geographical location setup whereby only 2 pollers exist for all datacenters in that particular geographical location. The pollers for San Antonio are located in SAT1 (Weston) while the London pollers are currently found in LON1.</p>
<h2>The Service</h2>
<p>Rackwatch currently operates by performing the following tests:
<ol>
<li>Normal PING/ICMP Test</li>
<li>HTTP/S (80, 8080, 8443, 443). This test is performed by opening a connection and doing an HTTP/1.1 formed GET request of the root url ("GET /"). The test looks for any response code that is BELOW 400. Any response code over 400 will generate an alert. Timeout is 5 seconds.</li>
<li>SSH. This test is performed by opening a connection to the socket and waiting for a username request. Once this happens, the connection is closed. Timeout is 5 seconds.</li>
<li>SMTP. This test is performed by opening a connection to the socket on port 25 and sending a "HELO hostname" and looks for server response. Timeout is 10 seconds.</li>
<li>POP3. This test is performed by opening a connection to the socket on port 110 and listening for a properly formed Banner from the server. Timeout is 5 seconds.</li>
<li>Cold Fusion. This test is performed similarly to the HTTP/S test, however it does a GET for a specific file: "GET /cfide/administrator/index.cfm." If this file does not exist, the alert will trigger. Timeout is 5 seconds.</li>
<li>MySQL/PostgreSQL/MSSQL. A connection is made to the appropriate port for the service, listening for a properly formed banner. Timeout is 5 seconds.</li>
<li>DNS. A DNS Request is made to the server for hostname 'localhost' with a timeout of 5 seconds waiting for the response of a successful lookup.</li>
<li>FTP. A connection is made to the FTP socket. The application listens for a properly formed FTP banner and then issues the QUIT command. Timeout is 10 seconds.</li>
<li>Webport. Webport is a simple HTTP test that looks to make sure that port 80 is open, and nothing more. No special transmissions are made.</li>
<li>Telnet. A Telnet Connection is attempted. Listens for an appropriate banner.</li>
</ol>
</p>
<h3>Intervals</h3>
<p>Rackwatch performs its polling at 5 minute intervals. In the event that a service is found to be down, the interval changes to 30 seconds for a total of 5 minutes. After 5 minutes, the poller returns to the normal polling interval for that node. If the service is down for 12 hours or longer, the service will be then polled every 10 minutes until it comes back up.</p>

<h1>Other Information</h1>
<hr />
<p>Rackwatch is currently maintained by Corporate Infrastructure. If you have any questions, issues, or need assistance, please open a Genie Ticket with the Service Desk who will be able to escalate the issue to the appropriate personell. This includes stuck alerts, questions, or false alerts that need to be addressed. You can contact the service desk at x501-4357.</p>
	</body>

</html>
