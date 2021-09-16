<?php

use MyCloudKmsProject\KeyManager;

putenv("GOOGLE_APPLICATION_CREDENTIALS=credentials.json");

/* Require composer modules */
require __DIR__ . '/vendor/autoload.php';

$projectId   = 'top-branch-326113'; // Get this from the google API - via the setup link at https://deliciousbrains.com/php-encryption-methods/
$location    = 'global';
$keyRingId   = 'test';
$cryptoKeyId = 'quickstart';

$keyManager = new KeyManager(
    $projectId,
    $location,
    $keyRingId,
    $cryptoKeyId
);

$encrypted = $keyManager->encrypt('My secret text');
var_dump($encrypted);

$unencrypted = $keyManager->decrypt($encrypted['secret'],$encrypted['data']);
var_dump($unencrypted);