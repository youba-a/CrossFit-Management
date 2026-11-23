<?php

// Routeur pour le serveur de developpement PHP.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
if (str_contains($path, '\\')
    || !preg_match('~^/(?:index\.php|img/[^/].+\.(?:jpe?g|png|gif|webp|ico)|css/[^/].+\.css|js/[^/].+\.js|back/API/.+\.php)?$~i', $path)
    || preg_match('~(?:^|/)\.|^/back/API/member/payment/stripe/|^/img/.*\.(?:php|phar)(?:/|$)~i', $path)) {
    http_response_code(404);
    exit;
}

return false;

