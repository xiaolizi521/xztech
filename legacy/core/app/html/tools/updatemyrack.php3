<?
	require_once("CORE_app.php");
    require_once("helpers.php");

if ($upd_new_password == $upd_new_password2)
	{



		if (!TestCustomerNumberInUse($db,$customer_number))
		{
                     //then fail
                     print("Error: <BR> Could not find customer.");
		}
		else
		{
			if($pass_exists==1)
			{
				$pw_update=$db->SubmitQuery("update customer_auth set password='$upd_new_password',userid='$customer_number' where customer_number='$customer_number';");
			}
			else
			{
				$pw_update=$db->SubmitQuery("insert into customer_auth values ($customer_number,'$upd_new_password','$customer_number');");
            }
            if( isset( $reloadparent ) and $reloadparent ) {
                print "<SCRIPT type='text/javascript'>
                        function loadParent( url, close ) {
                            try {
                                opener.location.href=url;
                            } catch(e) {
                                // Can't do anything. :-(
                            }
                            if( close ) {
                            window.close();
                            }
                        }
                           url = opener.location.href;
                           loadParent( url, 1 );
                       </SCRIPT>";
            } else {
                header("Location: /tools/myrackpass.php3?customer_number=$customer_number\n\n");
		    }
		}
	
	}
else
	{
            ForceReload("/tools/myrackpass.php3?customer_number=$customer_number&error=1&upd_new_password=$upd_new_password&upd_new_password2=$upd_new_password2");
	}

?>
