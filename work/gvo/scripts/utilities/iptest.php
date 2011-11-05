<?php

$connection = ssh2_connect('12.204.164.12', 2, array('hostkey'=>'ssh-rsa'));

if (ssh2_auth_pubkey_file($connection, 'root',
                          '/root/scripts/utilities/publickey',
                          '/root/.ssh/id_rsa', '')) {
  echo "Public Key Authentication Successful\n";
} else {
  die('Public Key Authentication Failed');
}

?>
