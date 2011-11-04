<?
/* Database Constants */
define ("DB_HOST", "localhost");
define ("DB_USER", "whatpulse");
define ("DB_PASS", "FU55mwh3CzfZBFSK");
define ("DB_NAME", "whatpulse");
define ("USER_DB", "userDB");
define ("XML_DB", "xmlDB");

/* Connect to database */
$database = mysql_connect(DB_HOST, DB_USER, DB_PASS);
mysql_select_db(DB_NAME);

/* Includes */
include "designtop.php";
include "country.php";

/* Set a test session username */
$_SESSION['username'] = "jackyyll";

/* Query the database for user data */
$query = "SELECT * FROM whatpulse WHERE `user` = '".$_SESSION['username']."'";
$userData  = mysql_fetch_assoc(mysql_query($query)) or die(mysql_error());

/* Convert the database rgb to hex and put it in $fontColor */
$fontColor  = rgb2hex($userData['fontred'], $userData['fontgreen'], $userData['fontblue']);

/* Define item values */
$username 		= $userData['user'];
$totalKeys 		= $userData['tkc'];
$totalClicks 	= $userData['tmc'];
$rank			= $userData['rank'];
$teamName		= $userData['tname'];
$teamKeys		= $userData['tkeys'];
$teamClicks		= $userData['tclicks'];
$teamRank		= $userData['trank'];
$country		= $userData['country'];
$flag           = "http://pulse.offbeat-zero.net/images/".getFlag($country);

/* Define items */
$item[0] = array("user", $username, $userData['userx'], $userData['usery']);
$item[1] = array("keys", $totalKeys, $userData['tkcx'], $userData['tkcy']);
$item[2] = array("clicks", $totalClicks, $userData['tmcx'], $userData['tmcy']);
$item[3] = array("rank", $rank, $userData['rankx'], $userData['ranky']);
$item[4] = array("teamname", $teamName, $userData['tnamex'], $userData['tnamey']);
$item[5] = array("teamkeys", $teamKeys, $userData['tkeysx'], $userData['tkeysy']);
$item[6] = array("teamclicks", $teamClicks, $userData['tclicksx'], $userData['tclicksy']);
$item[7] = array("teamrank", $teamRank, $userData['trankx'], $userData['tranky']);
$item[8] = array("country", "<img src='".$flag."' />", $userData['countryx'], $userData['countryy']);

function rgb2hex($red, $green, $blue)
{
    /* Convert from r,g,b format to hex */
	return sprintf('#%02X%02X%02X', $red, $green, $blue);
}

function getBackgrounds()
{
	/* Query the database for the users backgrounds and the default backgrounds 
	 * and put them into a backgrounds array. Then echo the options for the 
     * background switching select box.
	 * This should only be called from an already created select element which
     * is intended for the backgrounds.
     */
	global $userData;

	$query = sprintf("SELECT * FROM `backgrounds` WHERE `userid` = '%s' OR `userid` = '0' ORDER BY `name` ASC",
					mysql_real_escape_string($userData['id']));
					
	$result = mysql_query($query) or die(mysql_error());
	
	while ($background = mysql_fetch_array($result))
	{
		if ($background['id'] == $userData['theme'])
		{
			echo "<option selected=\"selected\" value=\"".$background['path']."\">";
			echo substr_replace($background['name'], "", 14);
			echo "</option>\n";
		}
		else
		{
			echo "<option value=\"".$background['path']."\">";
            echo substr_replace($background['name'], "", 14);
			echo "</option>\n";
		}
	}
}

function getFonts()
{
	/* Query the database for users fonts and the default fonts, ant put them into
	 * an array and use said array to put them into a select box for the user to 
	 * change their font.
     * This should only be called from within an already created select element that
     * is intended to hold the fonts.
	 */
	global $userData;

	$query = mysql_query("SELECT * FROM `fonts` ORDER BY `name` ASC") or die(mysql_error());
	
	while ($font = mysql_fetch_array($query))
	{
		if ($font['file'] == $userData['font'])
		{
			echo "<option selected=\"selected\" value=\"".$font['file']."\">";
			echo substr_replace($font['name'], "", 8);
			echo "</option>\n";
		}
		else
		{
			echo "<option value=\"".$font['file']."\">";
			echo substr_replace($font['name'], "", 8);
			echo "</option>\n";
		}
	}
}

function getItems()
{
    /* Adds the members of $item to an unordered list, sets the onClick to a function
     * which will, onclick, add it to the container div at the specified x, y and then 
     * hide it from the list. If the [x] button on the item is pressed, it removes the
     * item from the container, and unhides the item in the list.
     * This should only be called from inside an already created ul element.
     */
    global $item;

    $size = count($item);

    for ($i = 0; $i < $size; $i++)
    {
        echo "<li id=\"".$item[$i][0]."Item\">";    /* <li id="userItem"> */
        echo "<a href=\"#\" onClick=\"";            /* <a href="#" onClick=" */
        echo "AddItem('container',";                /* AddItem('container', */
        echo " '".$item[$i][0]."',";                /* 'user', */
		echo " '".$item[$i][1]."',";                /* 'jackyyll' */            /* AddItem('container', 'user', 'jackyyll', 0, 12);  */
        echo " ".$item[$i][2].",";                  /* 0, */
        echo " ".$item[$i][3];                      /* 12 */
        echo ");\">".$item[$i][0]."</a></li>";      /* );">user</a></li> */
    }
}

?>


<p />
<div width="100%" style="float: left;">
	
	<div id="container" class="container"></div>

	<div class="codebox">
		
		<ul style="list-style: none; padding-left: 0px; margin-left: 1em; margin-top: 0px; padding-top: 0px;">
			
			<li style="float: left; margin: 0; padding-right: 15px;">
				<a href="#" onClick="ClearItems();">Clear Items</a>
			</li>
			
			<li style="float: left; margin: 0; padding-right: 15px;">
				<a href="#" onClick="ShowCoordinates();">Get Coordinates</a>
			</li>
			
			<li style="float: left; margin: 0; padding-right: 15px;">
				<a href="#" onClick="Element.show('instructions');">Show Instructions</a>
			</li>
			
			<li style="float: left; margin: 0; padding-right: 15px;">
				<a href="#" onClick="expandMenus();" id="expand">Expand Menus</a>
			</li>

			<li style="float: left; margin: 0; padding-right: 15px;">
				<a href="#" onClick="">Save!</a>
			</li>
			
		</ul>

	</div>
	
	<p />
	
	<div id="instructions" style="background: #DAF8FF none repeat scroll 0%; border: 1px solid #6DA2BD; padding: 5px; position: relative;">
		<b>Instructions:</b><br />
		To expand menus, click the menu name and it will expand. Click it again and it will<br />
		collapse.
		<p />
		Add items to your signature image by expanding the Available Items menu and<br />
		clicking an item. This will put the item onto your signature, you can then drag the<br />
		item around and position it where you want.
		<p />
		To change your font settings, expand the font options menu. There you may change<br />
		your font, font size, font color, and enable text shadows. To change your font <br />
		color, click on the input box and it will bring up a color choosing menu. Use <br />
		this menu to choose a color like you would in other programs. The color of your<br />
		font will update in real time so you can see what it will look like. To close <br />
		the color chooser, just click outside of it.
		<p />
		To change your background settings, expand the background options menu. In this<br />
		menu you can change the background image of your signature and enable or disable<br />
		signature borders.
		<p />
		If you want to remove all of your current items from your signature, just hit the<br />
		'Clear Items' button on the lower menu. If you want to remove a single item from <br />
		your signature, just hit the '[x]' next to the item inside of the signature.
		<p />
		Once you are satisfied with your customizations, press the Save! button on the<br />
		lower menu.<br />
		<a style="margin-left: 365px;" href="#" onClick="Element.hide('instructions');">close instructions [x]</a>
	</div>
	
	<p />
	
	<div id="coords"></div>
	
</div>

<div style="float: right; width: 175px;">

	<table width="100%" cellpadding="0" cellspacing="0">
		<tbody>

			<tr>
				<td class="chead" onClick="Collapse('availableitems');">
					Available Items 
					<img id="availableitemsImg" src="http://pulse.offbeat-zero.net/images/icons/bullet_arrow_top.png" />
				</td>
			</tr>

			<tr>
				<td>
					<ul id="availableitems" style="list-style: none; padding-left: 0px; margin-left: 1em;">
						<? getItems(); ?>
					</ul>
				</td>
			</tr>

			<tr>
				<td class="chead" onClick="Collapse('fontoptions');">
					Font Options 
					<img id="fontoptionsImg" src="http://pulse.offbeat-zero.net/images/icons/bullet_arrow_bottom.png" />
				</td>
			</tr>

			<tr>
				<td>
					<div id="fontoptions" style="display: none;">
						<table>

							<tr>
								<td>Font:<br />
									<select name="font" id="font">
										<? getFonts(); ?>
									</select>
								</td>
							</tr>

							<tr>
								<td>Font Size:</td>
								<td>
									<input type="text" value="<? echo $userData['fontsize']; ?>" id="fontsize" size="2" />
								</td>
							</tr>

							<tr>
								<td>Font Color:</td>
								<td>
									<input type="text" class="color" id="color" value="<? echo $fontColor ?>" style="width: 60px;" />
								</td>
							</tr>

							<tr>
								<td>Enable Shadows:</td>
								<td>
									<? $userData['se'] == 0 ? $shadow = "" : $shadow = "CHECKED"; ?>
									<input type="checkbox" name="shadows" id="shadow" <? echo $shadow; ?> />
								</td>
							</tr>

						</table>
					</div>
				</td>
			</tr>

			<tr>
				<td class="chead" onClick="Collapse('backgroundoptions');">
					Background Options 
					<img id="backgroundoptionsImg" src="http://pulse.offbeat-zero.net/images/icons/bullet_arrow_bottom.png" />
				</td>
			</tr>

			<tr>
				<td>
					<div id="backgroundoptions" style="display: none;">
						<table>

							<tr>
								<td>Background:<br />
									<select name="background" id="background">
										<? getBackgrounds(); ?>
									</select>
								</td>
							</tr>

							<tr>
								<td>Enable Border: </td>
								<td>
									<? $userData['be'] == 0 ? $border = "" : $border = "CHECKED"; ?>
									<input type="checkbox" name="border" id="border" <? echo $border; ?> />
								</td>
							</tr>

						</table>
					</div>
				</td>
			</tr>

		</tbody>
	</table>

</div>

<div style="height: 0; clear: both;">&nbsp;</div>

<? include "menu.php" ?>

</div>
</body>
</html>
