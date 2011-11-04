<?PHP
require_once("CORE_app.php");
require_once("menus.php");
require_once("act/ActFactory.php");

define("MAX_NOTES_ON_PAGE", 5);

$external_contact = '';
$i_contact = ActFactory::getIContact();
$external_contact = $i_contact->getExternalContact($GLOBAL_db, $external_contact_primary_id);
$core_contactID = $external_contact->getCoreContactId($GLOBAL_db);

$notes = '';

$contactNotes = $external_contact->getNotes(false, MAX_NOTES_ON_PAGE);
$note_count = count($contactNotes);
if($note_count > 0) {
    $note_array = array_splice($contactNotes, 0, MAX_NOTES_ON_PAGE);
    
    foreach($note_array as $currentNote) {
        $notes .= '
                <TABLE class="note">
                  <TR>
                    <th class="note">' . $currentNote->getUserIdentifier() .
                                         '</th>
                    <th class="date">'. $currentNote->insertDate . '</th>
                  </TR>
                  <TR>
                    <TD class="note" colspan=2>' . $currentNote->noteText . '<BR></TD>
                  </TR>
                </TABLE><br>
                ';
    }
    if($note_count > MAX_NOTES_ON_PAGE) {
        $notes .= "<A HREF=\"javascript:open_notes('$external_contact_primary_id','$individual_id')\" class=\"text_button\">View All Notes</A>\n";
    }
}
?>
<HTML id="mainbody">
<HEAD>
    <TITLE>
        CORE: Contact
    </TITLE>
        <?= menu_headers() ?>
        <LINK HREF="/css/core2_basic.css" REL="stylesheet">
<SCRIPT LANGUAGE="JavaScript"><!--
function open_notes(external_contact_primary_id, individual_id) {
  window.open('EXT_CONT_notes_popup_page.php?external_contact_primary_id=' + external_contact_primary_id + '&individual_id=' + individual_id, 'contact_notes', 'resizable,toolbar=no,menubar=no,location=no,status=no,width=250,height=400,scrollbars=yes');
  //return void;
}
//--></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="/script/popup.js" TYPE="text/javascript"></SCRIPT>
</HEAD>
<?= page_start() ?>
<br>
<table border="0" cellspacing="0" cellpadding="4" align="left" width="100%">
<tr>
	<td valign="top">
		<?require('ACCT_view_onyx_external_contact_include.php'); ?>
    	<br clear="all">
<!-- Begin Right Pane of Workspace ----------------------------------------- -->        
    <td valign="top">
<!-- Begin Notes --------------------------------------------------   -->
	<?= $notes ?>
<!-- End Notes --------------------------------------------------     -->
	</td>
</tr>
</table>

<iframe src="<?= $GLOBALS['coreadmin_url'] ?>accounts/<?= $account_number ?>/user_permissions/<?= $core_contactID ?>?requestor_contact_id=<?= GetRackSessionContactID() ?>" style="border: 0px; width: 100%; height: 600px;"></iframe> 

<?= page_stop() ?>
</HTML>
