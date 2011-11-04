<?php require_once('ACCT_view_onyx_external_contact_include_logic.php'); ?>
<script>
    function editSecret() {
        makePopUpWin('EXT_CONT_edit_secret/step2_page.php?external_contact_primary_id=<?=$external_contact_primary_id ?>&individual_id=<?=$external_contact->individual->primaryId ?>&question=<?= $secret_questionURL ?>&answer=<?= $secret_answerURL ?>',400,600,'',4)
    }
</script>
<table class="blueman">
<tr>
    <th class="blueman"
		COLSPAN=2> External Contact Information (<?=$contact_type ?>)
              <?php if(in_dept("CORE")){ echo "[cid:$external_contact_primary_id]"; echo "[iid:" . $external_contact->individual->primaryId . "]"; } ?>
    </th>
</tr>
<tr>
    <td>
		<table class=datatable>   
        <TR>
            <Th COLSPAN="2"
        	    class="subhead1"> Personal
                &nbsp; &nbsp;
                <?php
                   $args = "external_contact_primary_id=" . $external_contact_primary_id . "&individual_id=" . $external_contact->individual->primaryId;
                   if( !empty( $account_id ) ) {
                       $args .= "&account_id=$account_id";
                   }
                   if( !empty( $role_id ) ) {
                       $args .= "&role_id=$role_id";
                   }
                ?>
				</TH>
         </TR>
		   <TR>
			<TD>
				<TABLE class=datatable>
		      <TR>
		         <Th> First Name: </Th>
		            <TD NOWRAP> <? print($external_contact->individual->firstName);?> 
                <? if( !$readonly ): ?>
				<A HREF="javascript:makePopUpWin('EXT_CONT_edit_person/edit_fname_page.php?<?=$args ?>',300,700,'',4)"
                   class=text_button> EDIT </A>
                <? endif; ?>
                    </TD>
				</TR>
		         <Th> Last Name: </Th>
		            <TD NOWRAP> <? print($external_contact->individual->lastName);?>
                <? if( !$readonly ): ?>
				<A HREF="javascript:makePopUpWin('EXT_CONT_edit_person/edit_lname_page.php?<?=$args ?>',300,700,'',4)"
                   class=text_button> EDIT </A>
                <? endif; ?>
                    
                    </TD>
				</TR>
				<TR>
				    <th> Secret: </th>
				    <TD>
                <?php
                     if( $secret_exists ) {
                         echo "$secret_question<BR>$secret_answer\n";
                     } else {
                         echo "There are no secrets";
			$secret_question='';
			$secret_answer='';
                     }
                ?> &nbsp;
                <? if( !$readonly ): ?>
                <A HREF="javascript:editSecret()"
                   class=text_button>
                EDIT</A>
                <? endif; ?>
		                    </TD>
				</TR>
		         </TABLE></TD>
		</TR>
		<TR>
        	<Th COLSPAN="2"
        	    class="subhead1"> Organization &nbsp; &nbsp;
            </Th>
        </TR>
		<TR>
			<TD>
			<TABLE class=datatable>
             <TR>
                <th> Primary Company: </th>
                <TD NOWRAP> <?=$external_contact->primaryCompanyName ?> </TD>
             </TR>
    		   <TR>
                <th> Job&nbsp;Title: </th>
                <TD NOWRAP> <?=$external_contact->individual->titleDescription ?> &nbsp;
                   <? if( !$readonly ): ?>
                        <A HREF="javascript:makePopUpWin('EXT_CONT_edit_title_page.php?external_contact_primary_id=<?=$external_contact_primary_id ?>&individual_id=<?=$external_contact->individual->primaryId ?>',300,500,'',4)"><IMG SRC="/images/button_command_tiny_edit.gif" WIDTH="26" HEIGHT="13" BORDER="0" VALIGN="middle" ALT="Edit"></A>
                   <? endif; ?>
                   </TD>
    		   </TR>                   
    		</TABLE></TD>
    		</TR>
        <TR>
            <Th COLSPAN="2"
        	    class="subhead1"> Address &nbsp; &nbsp;
                <? if(!$readonly or $core_edit ): ?>
				<A HREF="javascript:makePopUpWin('EXT_CONT_edit_address/start_page.php?external_contact_primary_id=<?=$external_contact_primary_id ?>&individual_id=<?=$external_contact->individual->primaryId ?>',500,600,'',4)"
                   class=text_button>
				EDIT</A>
                 <? endif; ?>                
				 </TH>
        </TR>
		<TR>
			<TD>
				<TABLE class=datatable>
				<TR>
             		<TD VALIGN="top"> 
                       <?php                 
                            $externalContactAddress = $external_contact->individual->getPrimaryAddress(); 
                            print($contact_street); 
                       ?>
						<BR> 
                       <?
                            print $externalContactAddress->city; ?>, <?print $externalContactAddress->regionCode ?>
                        <? print $externalContactAddress->postCode ?> <?=$externalContactAddress->countryCode ?></TD>
         		</TR>
				</TABLE></TD>
		</TR>
		<TR>
            <Th COLSPAN="2"
        	    class="subhead1"> Phone Numbers
              <? if( !$readonly ): ?>
               <a href="javascript:makePopUpWin('EXT_CONT_add_phone_page.php?external_contact_primary_id=<?=$external_contact_primary_id ?>&individual_id=<?=$external_contact->individual->primaryId ?>', 260,500,'',4)"
                  class=text_button>
               ADD</a>
              <? endif; ?>
                 </TD>
              </TR>
              <TR>
                 <TD VALIGN="top" NOWRAP> <?=$phone_numbers ?> </TD>
              </TR>
              <TR>
            <Th COLSPAN="2"
        	    class="subhead1"> E-Mail Addresses:
                <? if( !$readonly ): ?>
                <a href="javascript:makePopUpWin('EXT_CONT_add_email_page.php?external_contact_primary_id=<?=$external_contact_primary_id ?>&individual_id=<?= $external_contact->individual->primaryId ?>', 260,500,'',4)"
                   class=text_button>
                ADD</a>
                 <? endif; ?>
            </Th>
      </TR>
      <TR>
         <TD VALIGN="top" NOWRAP> <?=$email_addresses ?> </TD>
      </TR>
    <TR>
	</TABLE></TD>
</TR>
</TABLE>     
