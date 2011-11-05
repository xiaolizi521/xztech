<?php
# TITLE: Site Submissions
# $Id: site_submit_report.php 1.13 04/08/13 11:16:15-05:00 droth@dev.core.rackspace.com $

function printOptions($array,$current="") {
    foreach( $array as $text=>$value ) {
        echo "<option value=\"$value\"";
        if( $current == $value ) {
            echo " selected ";
        }
        echo ">$text</option>\n";
    }
}

$args_array[] = "status";
$args_array[] = "assign";
$args_array[] = "submitted_to";

// Dig out args out of session if they aren't set
$parts = explode( ".", $page );
$sess_prefix = "session_".$parts[0];
foreach( $args_array as $key ) {
    $skey = "$sess_prefix"."_$key";
    session_register( $skey );

    if( isset( $_GET[$key] ) ) {
        $$skey = $$key = stripslashes($_GET[$key]);

    } elseif( isset( $_POST[$key] ) ) {
        $$skey = $$key = stripslashes($_POST[$key]);

    } elseif( isset( $$skey ) ) {
        $$key = $$skey;
    }
}

$uid = GetRackSessionEmployeeNumber();
if( empty($uid) or $uid <= 0 ) {
    trigger_error( "This page doesn't work if you are not logged in.", FATAL );
}
$name = $GLOBAL_db->getVal('
select "FirstName" || \' \' || "LastName"
from "xref_employee_number_Contact"
join "CONT_Contact" on ("CONT_ContactID" = "CONT_Contact"."ID")
join "CONT_Person" on ("CONT_PersonID" = "CONT_Person"."ID")
where employee_number = '.$uid);

$status_select = 'status as "Status",';
if( !empty($status) ) {
    if( $status == "active" ) {
        $status_where = "status_number >= 0";
        $status_select = ""; // Don't need to show it here.
    } elseif( $status == "inactive" ) {
        $status_where = "status_number < 0";
    }
} else {
    $status = "";
}

if( !empty($assign) ) {
    if( $assign == "unassigned" ) {
        $assign_where = 'rep_number is NULL';
    } elseif( $assign == "me" ) {
        $assign_where = "rep_number = $uid";
    } elseif( $assign == "both" ) {
        $assign_where = "(rep_number is NULL OR rep_number = $uid)";
    }
} else {
    $assign = "";
}

if( !empty($submitted_to) and $submitted_to != "any" ) {
    $submit_where = "submitted_to = '$submitted_to'";
    $submit_select = "";
} else {
    $submitted_to = "any";
    $submit_select = '
    CASE
      WHEN submitted_to = \'instant_callback\'
        THEN \'Callback\'
      WHEN submitted_to = \'myrackspace_referral\'
        THEN \'MyRS Referral\'
      WHEN submitted_to = \'partner_submission\'
        THEN \'Partner Referral\'
      ELSE \'Submission\'
    END as "Type",
';
}

// Build the where clause
if( !empty($status_where) or 
    !empty($assign_where) or
    !empty($submit_where) ) {
    $where = "";
    if( !empty($status_where) ) {
        $where .= " $status_where ";
    }
    if( !empty($assign_where) ) {
        if( !empty($where) ) {
            $where .= " AND";
        }
        $where .= " $assign_where ";
    }
    if( !empty($submit_where) ) {
        if( !empty($where) ) {
            $where .= " AND";
        }
        $where .= " $submit_where ";
    }
    $where = "WHERE $where";
} else {
    $where = "";
}
        
# This sets up for sorting.  If you don't want your report to be
# sortable, then you don't need this.
if( empty($direction) ) { $direction = "ASC"; }
if( empty($sortby) ) { $sortby='customer_submission.sec_created'; }

# Build the sort ordering part of the query.
$order_by = "ORDER BY $sortby $direction";
if( $sortby != 'customer_submission.sec_created' and
    $sortby != 'Type') {
    $order_by .= ",customer_submission.sec_created $direction";
}
$order_by .= ",phone_color ";
if( $direction == "ASC" ) {
    $order_by .= "DESC";
} else {
    $order_by .= "ASC";
}
if( $sortby != 'customer_submission.sec_created' and
    $sortby == 'Type') {
    $order_by .= ",customer_submission.sec_created $direction";
}

# This is the query.  Make sure you like the field names.  You want them
# to be human readable.  The SQL "AS" is great for changing field names.
$report->setCountQuery('
SELECT count(*)
FROM customer_submission
     left join rep_assignment using (customer_number)
'.$where );
$report->setQuery( '
SELECT
    DATE(customer_submission.sec_created::abstime) as "Date Submitted",
    first_name || \' \' || last_name as "Name",
    phone as "Phone",
    email as "Email",
    '.$status_select.'
    CASE
      WHEN submitted_to = \'instant_callback\'
       THEN \'red\'
    ELSE
      \'black\'
    END as phone_color,
'.$submit_select.'
    CASE WHEN "Sales Rep" is NULL
    THEN \' Nobody \' ELSE "Sales Rep" END as "Sales Rep",
    CASE WHEN comments is NULL
    THEN \'no\' ELSE \'yes\' END as "Comments",
    customer_submission.oid as "View"
    
FROM
  customer_submission join submission_status using (status_number)
  left join rep_assignment using (customer_number)
  left join (select employee_number as rep_number,
          "FirstName" || \' \' || "LastName" as "Sales Rep"
     from "xref_employee_number_Contact"
    join "CONT_Contact" on ("CONT_ContactID" = "CONT_Contact"."ID")
    join "CONT_Person" on ("CONT_PersonID" = "CONT_Person"."ID")
  ) as foo using (rep_number)
'.$where.'
'.$order_by);

# This is to Mix "FirstName" and "LastName" (see the query above) to a
# field named "Name".  This will be mixed by a ' ' (space).
# You can use this to mix any number of fields with a common seperator.
#$report->addMix( 'Name', ' ', 'FirstName', 'LastName' );

# This says to ignore (not print) the field CID.  You can then use this
# for generating info or links behind the scenes.
#$report->ignoreField( 'cid' );
$report->ignoreField( 'phone_color' );

# This sets the size of a single page.  We generally don't want to
# let the user alter this
$report->setPageSize( 40 );

if( !empty($download) ) {
    $report->ignoreField( 'View' );
}

# This is the function that is actually called by show.php.  Every report
# needs one.  You can add fieldrules here.  You do NOT want to put
# fieldrules outside of this function because then you will be munging
# the XLS, CSV, and Gnumeric versions as well!
function printReport() {
    # You need both of these globals!!
    global $report, $page_index, $page, $status, $assign, $name;
    global $sortby, $direction, $submitted_to;

    # These are the default args.  Useful for calling your page
    # again.
    $args = "page=$page&page_index=$page_index";

    $status_array = array( "Any" => "",
                           "Active" => "active",
                           "Inactive" => "inactive" );

    $assign_array = array( "Any" => "",
                           "Nobody" => "unassigned",
                           "$name" => "me",
                           "Nobody or $name" => "both");

    $submit_array = array( "Any" => "",
                           "Callbacks" => "instant_callback",
                           "MyRS Referral" => "myrackspace_referral",
                           "Partner Referral" => "partner_submission",
                           "Sales Referral" => "sales_submission");

    $sort = urlencode("$sortby");
    $targs = "$args&assign=$assign&sortby=$sort&direction=$direction&submitted_to=$submitted_to";
    echo "\n<form>\n";
    echo "Status: <select name=\"status\" ";
    echo "onChange=\"location.href='show.php?$targs&status='+this.options[this.selectedIndex].value\"";
    echo ">\n";
    printOptions($status_array,$status);
    echo "</select>\n";

    $targs = "$args&status=$status&sortby=$sort&direction=$direction&submitted_to=$submitted_to";
    echo "Assigned to: <select name=\"assign\" ";
    echo "onChange=\"location.href='show.php?$targs&assign='+this.options[this.selectedIndex].value\"";
    echo ">\n";
    printOptions($assign_array,$assign);
    echo "</select>\n";

    $targs = "$args&status=$status&sortby=$sort&direction=$direction&assign=$assign";
    echo "Type: <select name=\"submitted_to\" ";
    echo "onChange=\"location.href='show.php?$targs&submitted_to='+this.options[this.selectedIndex].value\"";
    echo ">\n";
    printOptions($submit_array,$submitted_to);
    echo "</select>\n";

    echo "</form>\n";
    echo "</td></tr>\n";
    echo "<tr><td colspan=\"2\">\n";


    $report->setFieldRule( 'Phone',
          '<font color="%phone_color">%Phone</font>&nbsp;' );
    $report->setFieldRule( 'Email',
          '<a href="mailto:%Email">%Email</a>&nbsp;' );
    $report->setFieldRule( 'View',
          '<a href="/tools/display_submission.php3?oid=%View">View</a>' );

    $args = "$args&assign=$assign&status=$status&submitted_to=$submitted_to";

        # These add the sort arrows for each field.
        # The first argument is the field name
        # The second argument is the text to replace it with.
    $report->setHeaderReplacement( "Date Submitted",
                                   $report->strArrows('customer_submission.sec_created',$args).
                                   '<br>Date Submitted' );
    $report->setHeaderReplacement( "Name",
                                   $report->strArrows('"Name"',$args).
                                   ' Name' );
    $report->setHeaderReplacement( "Phone",
                                   $report->strArrows('"Phone"',$args).
                                   ' Phone' );
    $report->setHeaderReplacement( "Email",
                                   $report->strArrows('"Email"',$args).
                                   ' Email' );
    $report->setHeaderReplacement( "Status",
                                   $report->strArrows('"Status"',$args).
                                   '<br>Status' );
    $report->setHeaderReplacement( "Type", 
                                   $report->strArrows('"Type"',$args).
                                   '<br>Type' );
    $report->setHeaderReplacement( "Sales Rep",
                                   $report->strArrows('"Sales Rep"',$args).
                                   '<br>Sales Rep' );
    $report->setHeaderReplacement( "Comments",
                                   $report->strArrows('"Comments"',$args).
                                   '<br>Comments' );

    $report->printHTML( $page_index );
}


// Local Variables:
// mode: php
// c-basic-offset: 4
// indent-tabs-mode: nil
// End:
?>
