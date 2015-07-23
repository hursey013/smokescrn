<?php
/**
 * This is a JWT (JSON Web Token) generator, used to prevent CSRF from being successful in using the website services. 
 */

// check out https://github.com/firebase/php-jwt
require_once('common.php');

use Firebase\JWT\JWT;

$nonce = rand();

$_SESSION['jwt_nonce'] = $nonce;

$token = JWT::encode(array("nonce" => $nonce, "TTL" => time() + JWT_TIMEOUT), JWT_SECRET, 'HS512');

echo $token;
