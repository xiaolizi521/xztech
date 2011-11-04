<? require_once("CORE_app.php");
require("license.phlib");


$back_url = "display_license_keys.php?license_index=$license_index";
function PrintUnlicensedServerOptionList($license_index)
{
	global $db;
	$inventory_list = $db->SubmitQuery("
	select 
		t1.computer_number, product_sku, product_label
	from server_parts t1, server_status_all t2
	where t1.computer_number = t2.computer_number
		and t2.status_number >= 3
		and product_sku in
			(select product_sku
			from license_group_skus
			where license_index = $license_index
			)
		and t1.computer_number not in
			(select computer_number
			from license_key_assignments sub1
			where license_index = $license_index
				and t1.computer_number = sub1.computer_number
				and t1.product_sku = sub1.product_sku
				and t1.product_label = sub1.product_label
			)
	order by computer_number
	");
	for ($i = 0; $i < $inventory_list->numRows(); $i++)
	{
		$row = $inventory_list->fetchArray($i);
		$option = $row['computer_number'] . "___"
			. $row['product_sku'] . "___"
			. $row['product_label'];
		print("\n<OPTION value=\"$option\">");
		print($row['computer_number'] . " - " . $row['product_label']);
	}
}
if(!isset($license_index))
{
	DisplayError("License index missing.");
}

$license_group = new LicenseGroup($db, $license_index);
if ($license_group->get('key_based') == 'f')
{
	DisplayError("The license group \"" 
		. $license_group->get('license_name') . "\" does not use keys.");
}

$title = "Edit License Keys<br>\"" 
	. $license_group->get('license_name') . '"';
print("<title>$title</title><body bgcolor=white>");
back_link();
print CreateHeadlineString($title);

if (isset($command))
{
	if($command == $COMMAND_ADD_STATIC_KEY)
	{
		if (!in_dept('INVENTORY'))
		{
			DisplayError('You do not have access to add static keys.');
		}
		$key_list = split("\n", $new_keys);
		if (count($key_list) < 1)
		{
			DisplayError("No keys entered.");
		}
		$min_chars = (int)$min_chars;
		$max_chars = (int)$max_chars;
		// trim and check length
		for ($i = 0; $i < count($key_list); $i++)
		{
			$key_list[$i] = trim($key_list[$i]);
			if (strlen($key_list[$i]) == 0)
			{
				unset($key_list[$i]);
			}
			else if (strlen($key_list[$i]) < $min_chars
				|| strlen($key_list[$i]) > $max_chars)
			{
				DisplayError("Key \"$key_list[$i]\" (length="
					. strlen($key_list[$i]) . ") is not between
					$min_chars and $max_chars characters long.");
			}
		}
		foreach(array_count_values($key_list) as $name => $value)
		{
			if ($value > 1)
			{
				DisplayError("Duplicate key \"$name\" cannot be added");
			}
		}
		$db->BeginTransaction();
		foreach($key_list as $new_key)
		{
			$license_group->addStaticKey($new_key);
		}
		$db->CommitTransaction();
		$key_str[0] = ' ';
		LateForceReload(
			"display_license_keys.php?license_index=$license_index");
		print "<CENTER>
            <A HREF=display_license_keys.php?"
            . "license_index=$license_index>Continue</A>
            </CENTER><P>\n";
	}
	else if($command == $COMMAND_ASSIGN_STATIC_KEY_FORM)
	{
		?>
		<?include("form_wrap_begin.php");?>

		<FORM ACTION=edit_license_keys.php>
		<TABLE WIDTH=540 BORDER=0>
		<TR><TH>License Key:
			<TD><TT><?print($license_key);?>
		<TR><TH>Assign to:
			<TD><SELECT name=product>
		<?
		PrintUnlicensedServerOptionList($license_index);
		?>
		</SELECT>
		<input type=hidden name=license_index
			value=<?print($license_index);?>>
		<input type=hidden name=license_key
			value=<?print($license_key);?>>
		<TR><TH colspan=2>
			<input type=submit name=command 
				value="<?print($COMMAND_ASSIGN_STATIC_KEY);?>">
		</TABLE>
		</FORM>
		<?include("form_wrap_end.php");?>
		<?
	}
	else if($command == $COMMAND_ASSIGN_STATIC_KEY)
	{
		list($computer_number, $product_sku, $product_label)
			= split("___", $product);
		$license_group->assignStaticKey($license_key, $computer_number,
			$product_sku, $product_label);
		print("<h1>Assigned key \"$license_key\" to 
			computer #$computer_number</h1>");
		LateForceReload("display_license_keys.php?"
			. "license_index=$license_index", 2);
		print "<CENTER>
            <A HREF=display_license_keys.php?"
            . "license_index=$license_index>Continue</A>
            </CENTER><P>\n";
	}
	else if($command == $COMMAND_RECYCLE_STATIC_KEY)
	{
		$result = $license_group->recycle($license_key);
		if($result)
		{
			print("<h1>Recycled key \"$license_key\".</h1>");
			LateForceReload("display_license_keys.php?"
				. "license_index=$license_index", 1);
            print "<CENTER>
                <A HREF=display_license_keys.php?"
                . "license_index=$license_index>Continue</A>
                </CENTER><P>\n";
		}
		else
		{
			DisplayError("Failed to recycle key \"$license_key\".");
		}
	}
	else if($command == $COMMAND_DYNAMIC_KEY_INFO)
	{
		$key_info_list = $license_group->getDynamicKeyInfo($license_key);
		$computer_number = $license_group->getKeyComputerNumber($license_key);
		print("<TABLE WIDTH=540 BORDER=0>\n");
		print("<TR><TH ALIGN=LEFT WIDTH=40%>License Key:
			<TD><TT>$license_key</TR>\n");
		print("<TR><TH ALIGN=LEFT WIDTH=40%>Computer Number:
			<TD><TT>$computer_number</TR>\n");
		print("<TR bgcolor=black><TH colspan=2>
			<font color=white>Additional key info</TH></TR>\n");
		if(count($key_info_list))
		{
			foreach($key_info_list as $name => $value)
			{
				print("<TR><TH ALIGN=LEFT WIDTH=40%>$name:
					<TD><TT>$value</TR>\n");
			}
		}
		else
		{
			print("<TR><TH colspan=2>No extra info on this key.</TH></TR>\n");
		}
		print("</TABLE>\n");	
	}
	else if($command == $COMMAND_ADD_STATIC_KEY_FORM)
	{
		if (!in_dept('INVENTORY'))
		{
			DisplayError('You do not have access to add static keys.');
		}
		?>
		<?include("form_wrap_begin.php");?>

		<FORM ACTION=edit_license_keys.php>
		<TABLE WIDTH=540 BORDER=0>
		<TR><TD colspan=2>
		<blockquote>
		Just paste as many keys as you want in the large
		textbox each on a separate line. 
		Fill in the min and max character
		text fields so that it will fail to add keys that
		are incorrectly copied or read.
		</blockquote>
		<TR><TH align=left>
		Minimum key character length:
		<TD>
		<input type=text size=8 name=min_chars>
		<TR><TH align=left>
		Maximum key character length:
		<TD>
		<input type=text size=8 name=max_chars>
		<TR><TD colspan=2>
		<textarea cols=50 rows=10 name=new_keys></textarea>
		<TR><TH COLSPAN=2>
		<input type=hidden name=license_index
			value="<?print("$license_index");?>">
		<input type=submit name=command 
			value="<?print($COMMAND_ADD_STATIC_KEY);?>">
		</TABLE>
		</FORM>
		<?include("form_wrap_end.php");?>
		
	<?
	}
	else if($command == $COMMAND_ASSIGN_DYNAMIC_KEY_FORM)
	{
		?>
		<?include("form_wrap_begin.php");?>

		<FORM ACTION=edit_license_keys.php>
		<TABLE WIDTH=540 BORDER=0>
		<TR><TH>License Key:
			<TD><TT><input type=text size=35 name=license_key>
		<TR><TH>Key character length:
			<TD><TT><input type=text size=5 name=key_char_length>
		<TR><TH>Assign to:
			<TD><SELECT name=product>
		<?
		PrintUnlicensedServerOptionList($license_index);
		?>
		</SELECT>

		<?
		$field_list = $license_group->getKeyInfoFields();

		if (count($field_list) > 0)
		{
			?>
			<TR bgcolor=black><TH colspan=2>
				<font color=white>Enter information used to generate key:
			<TR bgcolor=black>
				<TH><font color=white>Name of Info
				<TH><font color=white>Value
				</TR>
			<?
			foreach($field_list as $field_name)
			{
				print("<TR><TD>$field_name</TD>");
				print("<TD><input type=text size=35 
					name=\"key_info_fields[$field_name]\">
					</TD></TR>\n");
			}
		}
		?>

		<input type=hidden name=license_index
			value=<?print($license_index);?>>
		<TR><TH colspan=2>
			<input type=submit name=command 
				value="<?print($COMMAND_ASSIGN_DYNAMIC_KEY);?>">
		</TABLE>
		</FORM>
		<?include("form_wrap_end.php");?>
		<?
	}
	else if ($command == $COMMAND_ASSIGN_DYNAMIC_KEY)
	{
		if ($HTTP_POST_VARS == false)
		{
			$form_data = $HTTP_GET_VARS;
		}
		else
		{
			$form_data = $HTTP_POST_VARS;
		}
		$license_key = trim($license_key);
		if (strlen($license_key) != $key_char_length
			or strlen($license_key) == 0)
		{
			DisplayError("The key \"$license_key\" is not $key_char_length
				characters long, or it is empty.");
		}
		$key_data = array();
		foreach($key_info_fields as $field_name => $value)
		{
			$field_name = trim($field_name);
			$value = trim($value);
			if (strlen($value) == 0)
			{
				DisplayError("You did not provide a value for the
					additional field \"$field_name\".");
			}

			$key_data[$field_name] = $value;
		}
		list($computer_number, $product_sku, $product_label)
			= split("___", $product);
		$license_group->assignDynamicKey($license_key, $computer_number,
			$product_sku, $product_label, $key_data);
		print("<h1>Assigned key \"$license_key\" to 
			computer #$computer_number</h1>");
		LateForceReload("display_license_keys.php?"
			. "license_index=$license_index", 2);
        print "<CENTER>
            <A HREF=display_license_keys.php?"
            . "license_index=$license_index>Continue</A>
            </CENTER><P>\n";
	}
}

print CreateBottomLineString();
?>
