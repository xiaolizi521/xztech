<?php
require_once("add_logic.php");
$title = "Add a Query Phrase";
$notes = "
<p>
Add as many parts to this group of parts.  You will search for them as a
group.  If you select all the diffirent SKUs for Windows, then
it will act as a single group in the search. <b>Product Line Parts</b> are
parts that are present on every server of a specific product line. 
<b>Available Parts</b> are the available parts that a server might have.
</p>
";
if( !$first_time ) {
        $notes .= "
<p>
You can now add additional groups of SKUs to search for.  Select as
many SKUs you want to your search.
<p>
Select either <b>AND</b> or <b>OR</b> to choose how you want to add 
to your search.
<ul>
<li>
<b>AND</b> will look for both the previous group
<em>and</em> and sum them into one total.  For example: You might make
the first group be all <em>Red Hat</em> SKUs and the second group be
all <em>Tape Drives</em> SKUs.  <b>AND</b>ing them together will give you
the number of servers with <em>both</em>.
<li>
<b>OR</b> will list this and the previous group seperately.  For example:
If the first group is all <em>Red Hat</em> SKUs and the second group
is all <em>Tape Drives</em> SKUs. <b>OR</b>ing them will give you the 
total number of servers with <em>Red Hat</em> and the total number of
servers with <em>Tape Drives</em> as two seperate numbers.
</ul>
<p>
<b>AND</b> has a higher precedence than <b>OR</b>.  This means all
<b>AND</b>ed groups will be placed together, while <b>OR</b>ed groups
will appear seperate.
";
}
$notes .= "
<p>
<b>NOT</b> is a dangerous thing to use.  It will give you <em>all servers
without the SKUs you select</em>.  This is usually not what most people want.
Example: Using <b>NOT</b> and <em>20 gb Taravan Tape Drive</em> will not
give you all the systems with tape drives that aren't <em>20 gb Taravan
Tape Drive</em>.  Instead it gives you every server, minus those that have a
<em>20 gb Taravan Tape Drive</em>.
<p>
<font color='red'>
<b>WARNING:</b> <b>NOT</b> is extremely slow.  It is very easy to 
create a search that will never complete.
</font>
";

require_once("header.php");
print $warn_datacenter;
?>

<FORM action="add_handler.php">

<?php if( !$first_time ): ?>
<SELECT name='logic'>
  <OPTION VALUE="AND"> AND </OPTION>
  <OPTION VALUE="OR" > OR </OPTION>
</SELECT>      
<?php endif; ?>

<pre><SELECT name='not'>
  <OPTION VALUE="f"> &nbsp; </OPTION>
  <OPTION VALUE="t"> NOT </OPTION>
</SELECT>      
<SELECT name='sku[]' size='16' multiple>
<?php PrintSelect() ?>
</SELECT>
<input type="submit" value=" Add "></pre></FORM>

<?php
require_once("footer.php");
?>
