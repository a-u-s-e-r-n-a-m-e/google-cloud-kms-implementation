<?php

putenv("GOOGLE_APPLICATION_CREDENTIALS=credentials.json");

/* Require composer modules */
require __DIR__ . '/vendor/autoload.php';

use MyCloudKmsProject\KeyManager;

$projectId   = 'project-id-123456';
$location    = 'global';
$keyRingId   = 'keyring';
$cryptoKeyId = 'key';

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