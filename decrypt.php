<?php
// Inspiration from https://deliciousbrains.com/php-encryption-methods/
// This version by https://github.com/ged3000
//
// Dependencies:
// "composer require google/cloud-kms"
// php libsodium extension

use KeyManager;

$encrypted = '';

$projectId   = 'top-branch-326113'; // Get this from the google API - via the setup link at https://deliciousbrains.com/php-encryption-methods/
$location    = 'quickstart';
$keyRingId   = 'test';
$cryptoKeyId = 'global';

$keyManager = new KeyManager(
    new Kms(),
    $projectId,
    $location,
    $keyRingId,
    $cryptoKeyId
);

$unencrypted = $keyManager->decrypt($encrypted['secret'],$encrypted['data']);
print_r($unencrypted);
