<?php
define('CLIENT_ID','1014506269081-lubfjqenpduu4q3c1e278shabo90r05d.apps.googleusercontent.com');
define('CLIENT_SECRET','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');
define('GOOGLE_OAUTH_SCOPE','https://www.googleapis.com/auth/calendar');
define('CLIENT_REDIRECT_URL','http://localhost/controle_salas/teste_api_calendar/calendar-API-tutorial-main/google-login.php');


 $googleOauthURL = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode(GOOGLE_OAUTH_SCOPE) . '&redirect_uri=' . CLIENT_REDIRECT_URL . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online'; 