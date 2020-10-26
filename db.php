<?php 
$output = shell_exec('php bin/console doctrine:schema:update --force'); 
echo "<pre>$output</pre>";
?>
