<?php
require_once('I18N_UnicodeString.php');

$text = <<<EOT
<p>wenn sich jemand ein nettes Blogtool anschauen möchte,
kann ich wärmstens Wordpress empfehlen.
Heute ist das neueste Release, die 1.5 Version erschienen.
Wer kein Englisch kann, kann sich die Feat ...</p>
EOT;

$u = new I18N_UnicodeString(nl2br($text), 'HTML');
echo $u->toUtf8String();
?>
