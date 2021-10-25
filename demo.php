<?php 
$zip = new ZipArchive;
$res = $zip->open('digital.zip');
if ($res === TRUE) {
  $zip->extractTo('/home/suketuparikh/webapps/noyta');
  $zip->close();
  echo 'woot!';
} else {
  echo 'doh!';
}
?>
