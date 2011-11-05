<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
  <html>
  <body>
    <table border="1">
      <tr bgcolor="#9acd32">
        <th>Time</th>
        <th>Log</th>
	<th>Service</th>
	<th>Message</th>
      </tr>
      <xsl:for-each select="log/entry">
      <tr>
        <td><xsl:value-of select="@recorded"/></td>
        <td><xsl:value-of select="@logfile"/></td>
	<td><xsl:value-of select="@service"/></td>
	<td><xsl:value-of select="@text"/></td>
      </tr>
      </xsl:for-each>
    </table>
  </body>
  </html>
</xsl:template>

</xsl:stylesheet>
