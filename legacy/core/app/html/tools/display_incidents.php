<%

//Connect to Rackspace Database

$conn=mysql_Connect('support.rackspace.com:3306','monty','rocksalt');
if ($conn != true)
	{ 
	print("error: ");
	print("$conn");
	}

mysql_select_db('rstechweb');
  
//Assemble Query
/* $query = "select t1.ref_no, t1.description, t1.time_created from incidents AS t1, customer_custom AS t2 where t1.customer_id = t2.customer_id AND code = 1 AND t1.status=0 AND t2.val =$customer_number;";*/
   $query = "select t1.ref_no, t1.description, t1.time_created from incidents AS t1, customer_custom AS t2 where t1.customer_id = t2.customer_id AND code = 1 AND t2.val =$customer_number;";

//Get Results
$incident_query = mysql_query($query);

if (!$incident_query) 
{
    echo "An error occured.";
    exit;
}

//Output Results
$num = mysql_num_rows($incident_query);
$display = 3;
print ("<BR>Total Tickets: ");
print $num;
print ("<BR>");
print ("Displaying: ");
print $display;
print ("<BR>");

for ($i = 0; $i < $display; $i++)
{
	$ref_no = mysql_result($incident_query, $i, 0);
	$ticket_status = mysql_result($incident_query, $i, 1);
	$ticket_sid = mysql_result($incident_query, $i, 2);

	$counter=$i+1;
	//session_register("ticket_count");
	//session_register("ticket$counter");
	//session_register("ticket_sid$counter");
	//session_register("ticket_status$counter");
	
	$ticket_count = $counter;
	eval("\$ticket$counter = \"$ref_no\";");
	eval("\$ticket_sid$counter = \"$ticket_sid\";");
	eval("\$ticket_status$counter = \"$ticket_status\";");
}

mysql_Close($conn);

%>
