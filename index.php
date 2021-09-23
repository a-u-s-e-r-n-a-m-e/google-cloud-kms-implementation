<?php

putenv("GOOGLE_APPLICATION_CREDENTIALS=credentials.json");

/* Require composer modules */
require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Kms\V1\KeyManagementServiceClient as Kms;
use Google\Cloud\Kms\V1\CryptoKey;
use Google\Cloud\Kms\V1\KeyRing;
use MyCloudKmsProject\KeyManager;

$projectId   = 'project-id-123456';
$location    = 'global';
$keyRingId   = 'keyring';
$cryptoKeyId = 'key';

$keyManager = new KeyManager(
    new Kms(),
    new KeyRing(),
    new CryptoKey(),
    $projectId,
    $location,
    $keyRingId,
    $cryptoKeyId
);

$encrypted = $keyManager->encrypt('My secret text');
var_dump($encrypted);

$unencrypted = $keyManager->decrypt($encrypted['secret'],$encrypted['data']);
var_dump($unencrypted);