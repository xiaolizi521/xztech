<?php require_once("CORE_app.php"); ?>
<?	
	$computer=new RackComputer;
	$computer->Init("",$computer_number,$db);
	$customer_number=$computer->customer_number();
	$customer_number=$computer->customer_number();
	if(!empty($command) and $command=="ASSIGN_REP")
	{
		$data=array();
		$data["computer_number"]=$computer_number;
		$data["employee_number"]=$employee_number;
        $data["customer_number"]=$customer_number;
		$data["rep_number"]=$employee_number;

		if ($employee_number==0)
		{
			$db->SubmitQuery("delete from rep_assignment where computer_number=$computer_number;");

			$computer->Log("Rep Assignment changed to : No One");

		}
		else
		{
			if ($db->TestExist("select computer_number from rep_assignment where computer_number=$computer_number;"))
				$db->Update("rep_assignment",$data,"computer_number=$computer_number");
			else
				$db->Insert("rep_assignment",$data);
				$employee_info=$db->SubmitQuery("Select first_name,last_name from employees where employee_number=$employee_number;");
			$computer->Log("Rep Assignment changed to :".$employee_info->getResult(0,"first_name")." ".$employee_info->getResult(0,"last_name"));
			$employee_info->freeResult();
		}
		ForceReload("display_computer.php3?computer_number=$computer_number");
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="mainbody">
<HEAD>
    <TITLE>
        CORE: Assign Rep
    </TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
<?require_once("tools_body.php");?>
<!-- Begin Assign Rep ------------------------------------------------------ -->
	<TABLE BORDER="1"
	       CELLSPACING="0"
	       CELLPADDING="0"
	       ALIGN="left">
	<TR>
		<TD>		
			<FORM ACTION="assign_rep.php3"
			      METHOD="post">
			<INPUT TYPE="hidden"
			       NAME="command"
			       VALUE="ASSIGN_REP">
			<INPUT TYPE="hidden"
			       NAME="customer_number"
			       VALUE="<?print($customer_number);?>">
			<INPUT TYPE="hidden"
			       NAME="computer_number"
			       VALUE="<?print($computer_number);?>">
			<TABLE BORDER="0"
			       CELLSPACING="2"
			       CELLPADDING="2">
			<TR>
				<TD BGCOLOR="#003399" 
					CLASS="HD3REV"
					COLSPAN=2> Assign Rep: 
					#<?print($customer_number);?>-<?print($computer_number);?></TD>
			</TR>
			<TR>
				<TD CLASS="label"> Rep Assigned </TD>
				<TD>
			<SELECT name=employee_number>
			<OPTION VALUE="0">No One
			<?
				//Load up the customer profile and status
				$current_rep=$db->GetVal("
					select 
						rep_number as employee_number 
					from 
						rep_assignment 
					where 
						customer_number=$customer_number 
						and computer_number=$computer_number;");
				if ($current_rep=="")
				{
					//Default to the last rep used for this customer
					$current_rep=$db->GetVal("
						select 
							rep_number as employee_number,
							sec_last_mod 
						from 
							rep_assignment 
						where 
							customer_number=$customer_number 
						order by 
							sec_last_mod DESC ;");
				}
				if ($current_rep=="")
					$current_rep=0;
				//Test to see if the rep that is assigned is not on the rep_profile list anymore
				if ($current_rep!=0)
				{
					if (!$db->TestExist("
						select 
							employee_number 
						from 
							employees 
						where 
							employee_number=$current_rep;"))
					{
						//Their rep is no longer a rep - still need to keep the entry
						$rep_info=$db->SubmitQuery("
							SELECT 
								employee_number,
								first_name,
								last_name  
							from 
								employees 
							where 
								employee_number=$current_rep;");
						if ($rep_info->numRows()>0)
						{
							print ("<OPTION SELECTED VALUE=\"".$rep_info->getResult(0,"employee_number")."\" $selected>".$rep_info->getResult(0,"first_name")." ".$rep_info->getResult(0,"last_name")."\n");
						}
							
						$rep_info->freeResult();
					}
				}
			
				$rep_info=$db->SubmitQuery('
             SELECT
               employee_number,
               "FirstName" as first_name,
               "LastName" as last_name
             FROM
               employee_authorization 
               join employee_dept using (employee_number) 
               join "xref_employee_number_Contact" using (employee_number)
               join "CONT_Contact" on ( "CONT_ContactID" = "CONT_Contact"."ID")
               join "CONT_Person" on ( "CONT_PersonID" = "CONT_Person"."ID" )
               WHERE department = \'SALES\'
               ORDER BY last_name, first_name'
                        );
			
				$num=$rep_info->numRows();
				for ($i=0;$i<$num;$i++)
				{
					if ($rep_info->getResult($i,"employee_number")==$current_rep)
						$selected=" SELECTED ";
					else
						$selected="";
					print "<OPTION VALUE=\"".$rep_info->getResult($i,"employee_number")."\" $selected>";
                    print $rep_info->getResult($i,"last_name");
                    print ", ";
                    print $rep_info->getResult($i,"first_name");
                    print "</option>\n";
				}
			
				$rep_info->freeResult();
			?>
			</SELECT>
			</TD>
			</TR>
			<TR>
				<TD COLSPAN="2"
				    ALIGN="center"><INPUT TYPE="image"
			                                      SRC="../images/button_command_save_off.jpg"
			                                      BORDER="0"></TD>
			</TR>
			</TABLE>
			</FORM></TD>
	</TR>
	</TABLE>
<!-- End Assign Rep -------------------------------------------------------- -->
<?=page_stop()?>
<? $db->CloseConnection();?>
</HTML>
