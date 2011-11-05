<?php

    include "includes/header.php";
    
    $query = "SELECT hosts.id,hosts.host,backups.time,backups.filename,backups.verified FROM `hosts` LEFT JOIN backups ON hosts.id = backups.hostid";
    
    $result = $db->query($query);
    echo <<<HTML
    <table>
        <thead>
            <tr>
                <th>Hostname</th>
                <th>Filename</th>
                <th>Last Backup Time</th>
                <th>Verified</th>
            </tr>
        </thead>
        <tbody>
HTML;
    while($data=$result->fetch_assoc()):
    
        echo "<tr>";
        echo "<td>".$data["host"]."</td>";
        echo "<td>".$data["filename"]."</td>";
        echo "<td>".$data["time"]."</td>";
        echo "<td style='text-align: center;'>";
        if($data['verified']):
            echo "<img src='images/tick.png'";
        else:
            echo "<img src='images/cross.png'";
        endif;
        echo "</td>";
        echo "</tr>";
    
    endwhile;
    
    echo "</tbody>";
    include "includes/footer.php";

?>