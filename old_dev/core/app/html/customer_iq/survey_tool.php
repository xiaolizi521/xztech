<?php
/* Survey Tool -- Customer Intel Reports
 * Most of this written on 10-3-02
 * Ken Kinder <kkinder@rackspace.com>
 *
 * This generates Customer Intel Reports for Surveys in the Survey Tool. At the
 * time of writing this, we are calling it the "New" Suvey Tool.
 *
 * The process is:
 *
 *    ----------------------------------------------------------
 *    | User clicks on Survey in Customer Intel Reports Screen |
 *    ----------------------------------------------------------
 *                                |
 *                                |
 *               -------------------------------------
 *               | This script called with survey ID |
 *               -------------------------------------
 *                                |
 *                                |
 *          -----Stage 1-----------------------------------
 *          | This script prompts user for date ranges(*) |
 *          -----------------------------------------------
 *                                |
 *                                |
 *        ---State 2----------------------------------------
 *        | This script called with survey ID, Date Ranges |
 *        --------------------------------------------------
 *                                |
 *                                |
 *                     -----------------------
 *                     | User given XLS file |
 *                     -----------------------
 *
 *      (*) Date ranges are for date deployed and/or date responded
 */


$TICKET_RESPONSE_SURVEY = 5;    // Survey type ID of the ticketing survey
                                // Used to determine whether or not to show
                                // ticket id

require_once("CORE_app.php");
require_once("menus.php");
require_once("common.php");
require_once("act/ActFactory.php");

if (isset($cancel)) {
    header("Location: index.php");
    exit;
}

if (isset($getdata)) {
    // Date two
    $stage = 2;
} else {
    $stage = 1;
}

function getTeam($account_number) {
    // Returns a string of the team name
    $report_db = getReportDB();
    
    $i_account = ActFactory::getIAccount();
    $account = $i_account->getAccountByAccountNumber($report_db, $account_number);
    
    $team = $account->getSupportTeamName();
    if(!empty($team)) {
        return team;
    }
    return "None";        
}

function getTicketNumber($issued_survey_id) {
    // Returns a ticket number (string)
    $report_db = getReportDB();
    
    $result_ticket = $report_db->SubmitQuery('
        select
            t."ReferenceNumber"
        from
            "TCKT_Ticket" t,
            "SRVY_xref_IssuedSurvey_Ticket" x
        where
            x."SRVY_IssuedSurveyID" = ' . $issued_survey_id . '
            and x."TCKT_TicketID" = t."TCKT_TicketID"
    ');
    if ($result_ticket->numRows() > 0) {
        $ticketinfo = $result_ticket->fetcharray(0);
        return $ticketinfo["ReferenceNumber"];
    } else {
        return "None";
    }
}

$report_db = getReportDB();

if ($stage == 1) {
    /*********************************************************************
    *                             STAGE ONE                             *
    *********************************************************************/
  
    $query = 'select "SRVY_Survey"."Title" from "SRVY_Survey" where "SRVY_SurveyID" = ' . $id;
    $result = $report_db->SubmitQuery($query);
    $row = $result->fetchrow(0);
    $survey_title = $row[0]
    
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    <HTML id="mainbody">
      <HEAD>
        <TITLE> CORE: Customer IQ Reports </TITLE>
        <LINK HREF="/css/core_ui.css" REL="stylesheet">
        <LINK HREF="/css/core2_basic.css" REL="stylesheet">
        <?=menu_headers()?>
      </HEAD>
      <?=page_start()?>
        <FORM METHOD=GET ACTION="survey_tool.php">
          <INPUT TYPE=HIDDEN NAME="id" VALUE="<? print $id ?>">
          <INPUT TYPE=HIDDEN NAME="getdata" VALUE="1">
          <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" CLASS="titlebaroutline">
            <TR>
              <TD>
                <TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="0" BGCOLOR="FFFFFF">
                  <TR>
                    <TD>
                      <TABLE BORDER="0" CELLSPACING="2" CELLPADDING="2">
                        <TR>
                          <TD BGCOLOR="#003399" CLASS="hd3rev">Genrate Report For <i><? print $survey_title ?></i></TD>
                        </TR>
                        <TR>
                          <TD>
                            <TABLE BORDER=0 CELLPADDING=2 CELLSPACING=0>
                              <TR>
                                <TD><B>Deployment Date:</B></TD>
                                <TD>
                                  <input type=text name="deploy_date_start"> to <input type=text name="deploy_date_end">
                                </TD>
                              </TR>
                              <TR>
                                <TD>&nbsp;</TD>
                                <TD><small>yyyy-mm-dd</small></TD>
                              </TR>
                              <TR>
                                <TD><B>Response Date:</B></TD>
                                <TD>
                                  <input type=text name="response_date_start"> to <input type=text name="response_date_end">
                                </TD>
                              </TR>
                              <TR>
                                <TD>&nbsp;</TD>
                                <TD><small>yyyy-mm-dd</small></TD>
                              </TR>
                              <TR>
                                <TD colspan=2><small>Either field may be left blank for complete results.</small></TD>
                              </TR>
                              <TR>
                                <TD COLSPAN=2 ALIGN=RIGHT>
                                  <INPUT TYPE=SUBMIT NAME=SUBMIT value="OK">
                                  <INPUT TYPE=SUBMIT NAME=cancel value="Cancel">
                                </TD>
                              </TR>
                            </TABLE>
                          </TD>
                        </TR>
                      </TABLE>
                    </TD>
                  </TR>
                </TABLE>
              </TD>
            </TR>
          </TABLE>
        </FORM>
      <?=page_stop()?>
    </html>
    <?
} else {
    Header("Pragma:");
    Header("Content-type: application/vnd.ms-excel");
    Header("Content-Description: Survey Report");
    Header("Content-Disposition: attachment; filename=survey_report.xls");
    /*********************************************************************
     *                             STAGE TWO                             *
     *********************************************************************/
    
    // Get list of questions
    $questions = array();
    
    $result = $report_db->SubmitQuery('
        select
          q.*
        from
          "SRVY_Question" q
          join "SRVY_xref_Survey_Question" x using ("SRVY_QuestionID")
        where "SRVY_SurveyID" = ' . $id . ' order by "Order"');
    $rows = $result->numRows();
    for ($i=0; $i<$rows; $i++) {
        $row = $result->fetcharray($i);
        array_push($questions, $row);
    }
    
    // Figure out survey info
    $result_survey = $report_db->SubmitQuery('
        select
            s.*
        from
            "SRVY_Survey" s
        where
            "SRVY_SurveyID" = ' . $id);
    $surveyinfo = $result_survey->fetcharray(0);
    
    // Print out header row
    if ($surveyinfo["SRVY_val_SurveyTypeID"] == $TICKET_RESPONSE_SURVEY) {
        // Ticket response survey, show ticket number
        print "Account Number\tDate Issued\tDate Responded\tSupport Team\tTicket Number\tGift Certificate\tExpiration Date\t";
    } else {
        print "Account Number\tDate Issued\tDate Responded\tSupport Team\tGift Certificate\tExpiration Date\t";
    }
    foreach ($questions as $question) {
        print $question['Text'];
        print "\t";
    }
    print "\n";
    
    // Go through each response
    $clauses_list = array();
    
    if (isset($deploy_date_start) and $deploy_date_start) {
        $clause = "s.\"DateIssued\" >= date '$deploy_date_start'";
        array_push($clauses_list, $clause);
    }
    if (isset($deploy_date_end) and $deploy_date_end) {
        $clause = "s.\"DateIssued\" <= date '$deploy_date_end'";
        array_push($clauses_list, $clause);
    }
    if (isset($response_date_start) and $response_date_start) {
        $clause = "s.\"DateAnswered\" >= date '$response_date_start'";
        array_push($clauses_list, $clause);
    }
    if (isset($response_date_end) and $response_date_end) {
        $clause = "s.\"DateAnswered\" <= date '$response_date_end'";
        array_push($clauses_list, $clause);
    }
    
    array_push($clauses_list, '"SRVY_SurveyID" = ' . $id);
    $clauses = join(" and ", $clauses_list);
    
    $result = $report_db->SubmitQuery('
        select
          s.*,
          a."AccountNumber"
        from
          "SRVY_IssuedSurvey" s
          join "ACCT_xref_Account_IssuedSurvey" x using ("SRVY_IssuedSurveyID")
          join "ACCT_Account" a on x."ACCT_AccountID" = a."ID"
        where
        ' . $clauses);
    
    $rows = $result->numRows();
    for ($i=0; $i<$rows; $i++) {
        $issued_survey = $result->fetcharray($i);
        
        // Do account number(s)
        #$result_acct = $report_db->SubmitQuery('
            #select
                #a."AccountNumber"
            #from
                #"ACCT_Account" a
                #join "ACCT_xref_Account_Contact_AccountRole" x on (a."ID" = x."ACCT_AccountID")
            #where
                #x."CONT_ContactID" = ' . $issued_survey["CONT_ContactID"]);
        #$accounts_list = array();

        #$rows_acct = $result_acct->numRows();
        #for ($ii=0; $ii<$rows_acct; $ii++) {
            #$account = $result_acct->fetcharray($ii);
            
            #array_push($accounts_list, $account["AccountNumber"]);
        #}
        #print join(",", array_unique($accounts_list));
        #print "\t";
        
        print $issued_survey["AccountNumber"];
        print "\t";
        
        // Do dates
        print $issued_survey["DateIssued"];
        print "\t";
        print $issued_survey["DateAnswered"];
        print "\t";
        
        print getTeam($issued_survey["AccountNumber"]);
        print "\t";
        
        // Do other columns
        if ($surveyinfo["SRVY_val_SurveyTypeID"] == $TICKET_RESPONSE_SURVEY) {
            print getTicketNumber($issued_survey["SRVY_IssuedSurveyID"]);
            print "\t";
        }
        
        // Do gift certificates
        $result_cert = $report_db->SubmitQuery('
            select
                g."CertificateNumber" as number,
                g."ExpirationDate" as expdate
            from
                "GIFT_Certificate" g,
                "SRVY_xref_IssuedSurvey_Certificate" x
            where
                g."GIFT_CertificateID" = x."GIFT_CertificateID"
                and x."SRVY_IssuedSurveyID" = ' . $issued_survey['SRVY_IssuedSurveyID']);
        if ($result_cert->numRows()) {
            $cert_data = $result_cert->fetcharray(0);
            print $cert_data["number"] . "\t" . $cert_data["expdate"] . "\t";
        } else {
            print "\t\t";
        }
        
        // Do actual questions
        foreach ($questions as $question) {
            // Get list of answers
            $answers = array();
            
            $result_ans = $report_db->SubmitQuery("
                select
                    a.\"Text\" as \"text\",
                    x.\"Text\" as \"freetext\"
                from
                    \"SRVY_Answer\" a,
                    \"SRVY_Question\" q,
                    \"SRVY_xref_IssuedSurvey_Answer\" x
                where
                    x.\"SRVY_IssuedSurveyID\" = $issued_survey[SRVY_IssuedSurveyID]
                    and q.\"SRVY_QuestionID\" = $question[SRVY_QuestionID]
                    and x.\"SRVY_AnswerID\" = a.\"SRVY_AnswerID\"
                    and a.\"SRVY_QuestionID\" = q.\"SRVY_QuestionID\"
            ");
            $rows_ans = $result_ans->numRows();
            for ($ii=0; $ii<$rows_ans; $ii++) {
                $answer = $result_ans->fetcharray($ii);
                if ($answer["freetext"]) {
                    // Escape the crap out of free-form answers.
                    array_push($answers, $answer["text"] . ' ' . 
                                         str_replace('"', '""',
                                           str_replace("\r", ' ',
                                             str_replace("\n", ' ',
                                               str_replace("\t", '        ',
                                                 $answer["freetext"])))));
                } else {
                    array_push($answers, $answer["text"]);
                }
            }
            
            // DOES BK SHOW YOU THIS!?
            
            if (count($answers)) {
                print join(",", $answers);
            }
            print "\t";
        }
        print "\n";
    }
}

?>
