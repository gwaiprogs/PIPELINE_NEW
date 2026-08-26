<?php
require_once __DIR__ . '/auth/microsoft_secrets.php';

// This MUST be a verified sender in your Mailjet account

define('MAILJET_API_KEY', getenv('MAILJET_API_KEY'));
define('MAILJET_API_SECRET', getenv('MAILJET_API_SECRET'));
define('MAILJET_SENDER_EMAIL', getenv('MAILJET_SENDER_EMAIL'));
define('MAILJET_SENDER_NAME', getenv('MAILJET_SENDER_NAME'));
?>
