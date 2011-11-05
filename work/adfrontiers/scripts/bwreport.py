#!/usr/bin/python
# -*- coding: utf-8 -*-

# Bandwidth Reporting
# 
# This script is a simple script for emailing the results of a query.
# The script connects to the database, executes the required SQl
#
# Once complete, the results are emailed to the Recipients listed.

import datetime
import psycopg2
import sys
import os
import smtplib
import string
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

# This is the only configuration item. Add/remove email addresses as 
recipients = [ 'offbeatadam@gmail.com', 'robert@jcartermarketkng.com' ]

## Postgres Database Connection ##
# Database Configuration and Connection
dbString = "host='localhost' dbname='af_db_main' user='adfrontiers' password=''"

# Attempt to connect to the database
try:

  # Open a Connection
  pgConn = psycopg2.connect(dbString)
  
  # Grab a Cursor object from our newly opened connection
  dbCursor = pgConn.cursor()

except:

  # Get the most recent exception
  exceptionType, exceptionValue, exceptionTraceback = sys.exc_info()
  
  # Exit the script and print an error telling what happened.
  sys.exit("Failed to connect to the database.\n ->%s" % (exceptionValue))

## Assemble the Query ##

# We only need last month.
currDate = datetime.datetime.now()
currYear = int(ts.strftime('%Y'))
currMonth = int(ts.strftime('%m'))

# If we are now in January, the previous month is December - and it is also Last Year.
# Otherwise, the minimum date is the current month less one, within the same year.
if(currMonth == 1):
  dateMin = '%d-%d-1' % ((currYear - 1),12)
  dateMax = '%d-%d-1' % (currYear,currMonth)
else:
  dateMin = '%d-%d-1' % (currYear, (currMonth - 1))
  dateMax = '%d-%d-1' % (currYear,currMonth)

# This is the query that obtains the data
query = """
SELECT    account.name
        , ROUND(
            SUM( bandwidth_bytes ) / 1073741824 
          , 4
          ) AS GB
        , SUM(
            CASE  WHEN pde.event_type = 'i' THEN pde.quantity 
                  ELSE 0 
            END
          ) AS imps
        , SUM(
            CASE  WHEN pde.event_type = 'i' THEN pde.quantity 
                  ELSE 0 
            END 
          ) / 1000.0 * .03 AS impsbill
        , ROUND(
            SUM( bandwidth_bytes ) / 1073741824
          , 2
          ) * 20 AS GBbill
FROM pub_daily_event pde
JOIN      pub_daily pd
ON        pd.id = pde.pub_daily_id
JOIN      campaign_media cm
ON        cm.id = pd.campaign_media_id
JOIN      account
ON        account.id = cm.ac_id
WHERE     pd.event_time >= '%s'
AND       pd.event_time < '%s'
GROUP BY  account.name;
""" % (dateMin, dateMax)

# Prior to executing the query, we want to get our message text ready for the loop.
text = "Here is the bandwidth report for %s\n" % dateMin
text += "\n\n\n"
text += "           name            |    gb    |   imps    |         impsbill         |  gbbill  \n"
text += "---------------------------+----------+-----------+--------------------------+----------\n"

# Same for HTML text.
html = """
<html>
  <head>
    <style type="text/css">
      table {
        border-width: 1px;
        border-spacing: 0px;
        border-style: solid;
        border-color: black;
        border-collapse: collapse;
        background-color: white;
      }
      th,td {
        border-width: 1px;
        padding: 2px;
        border-style: solid;
        border-color: black;
        background-color: white;
      }
      th {
        font-weight: bold;
      }
    </style>
  </head>
  <body>
    <h1>Bandwidth Report for %s</h1>
    <p>Greetings,</p>
    <p>This is the bandwidth report for last month, %s.</p>
    <table>
      <tr>
        <th>name</th>
        <th>gb</th>
        <th>imps</th>
        <th>impsbill</th>
        <th>gbbill</th>
      </tr>
""" % (dateMin, dateMin)

# Run the query
dbCursor.execute(query)

# For as long as there are rows, process the data. This is mostly a formatting loop.
# It does not do any manipulation of the data itself.
for i in dbCursor:

  # 0=name, 1=gb, 2=imps, 3=impsbill, 4=gbbill

  # The information returend by postgres is mixed in types.
  # Strictly casting a string allows for better formatting within
  # The plaintext table. Makes it easier to make it pretty dammit.
  name = i[0].__str__() + " " * (27 - len(i[0].__str__())) + "|"
  gb = " " * (9 - len(i[1].__str__())) + i[1].__str__() + " |"
  imps = " " * (10 - len(i[2].__str__())) + i[2].__str__() + " |"
  impsbill = " " * (25 - len(i[3].__str__())) + i[3].__str__() + " |"
  gbbill = " " * (9 - len(i[4].__str__())) + i[4].__str__() + "\n"
  text += name + gb + imps + impsbill + gbbill

  # HTML on the other hand... is much easier.
  html += """
      <tr>
        <td>%s</td>
        <td>%s</td>
        <td>%s</td>
        <td>%s</td>
        <td>%s</td>
      </tr>
  """ % (i[0].__str__(), i[1].__str__(), i[2].__str__(), i[3].__str__(), i[4].__str__())

# Followign the loop, close out the HTML tags. The plaintext requires no closure.
html += """
    </table>
  </body>
</html>
"""

# Assemble each part into an appropriate container
part1 = MIMEText(text, 'plain')
part2 = MIMEText(html, 'html')

# This is the from field. Since its the server itself
# The variable made sense.
server = "root@adfrontiers.com"

## Assembling the E-Mail ##

# Insantiate a new MIME object
msg = MIMEMultipart('alternative')

# Set the appropriate paramaters
msg['Subject'] = "Bandwidth Report for %s" % dateMin
msg['From'] =  server

# Note that the MIME object expects a string. The SMTPLIB object expects a list.
msg['To'] = ', '.join(recipients)

# Attach the two parts
msg.attach(part1)
msg.attach(part2)

# Connecting to the email server
s = smtplib.SMTP('localhost')

# Sending the message
s.sendmail(server, recipients, msg.as_string())

# Disconnect
s.quit()

# Close the database connections
dbCursor.close()
pgConn.close()

# Done #