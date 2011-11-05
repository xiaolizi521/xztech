<?

function mysql_fetch_assoc_join( $r )
{
	$numfields = mysql_num_fields($r);
	$tfields = Array();
	for ($i=0;$i<$numfields;$i++)
	{
	    $field =  mysql_fetch_field($r,$i);
	    $tfields[$i] = $field->table.'.'.$field->name;
	}
	$row = mysql_fetch_row($r);
	$rowAssoc = Array();
	for ($i=0;$i<$numfields;$i++)
	{
		$rowAssoc[$tfields[$i]] = $row[$i];
	}
	
	return $rowAssoc;
}

?>