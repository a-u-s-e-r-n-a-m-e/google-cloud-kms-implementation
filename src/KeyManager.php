<?php

use Google\ApiCore\ApiException;
use Google\Cloud\Kms\V1\CryptoKey;
use Google\Cloud\Kms\V1\CryptoKey\CryptoKeyPurpose;
use Google\Cloud\Kms\V1\KeyManagementServiceClient as Kms;
use Google\Cloud\Kms\V1\KeyRing;

class KeyManager{

    private $kms;
    private $projectId;
    private $locationId;
    private $locationRef;
    private $keyRingId;
    private $keyRingRef;
    private $cryptoKeyId;
    private $cryptoKeyRef;

    public function __construct($projectId, $locationId, $keyRingId, $cryptoKeyId)
    {
        $this->kms = new Kms();
        $this->projectId = $projectId;
        $this->locationId = $locationId;
        $this->locationRef = $this->kms::locationName($this->projectId, $this->locationId);
        $this->keyRingId = $keyRingId;
        $this->keyRingRef = $this->kms::keyRingName($this->projectId, $this->locationId, $this->keyRingId);
        $this->cryptoKeyId = $cryptoKeyId;
        $this->cryptoKeyRef = $this->kms::cryptoKeyName($this->projectId, $this->locationId, $this->keyRingId, $this->cryptoKeyId);

        try {
            $this->kms->getKeyRing($this->keyRingRef);
        } catch (ApiException $e) {
            if ($e->getStatus() === 'NOT_FOUND') {
                $keyRing = new KeyRing();
                $keyRing->setName($this->keyRingRef);
                $this->kms->createKeyRing($this->locationRef, $this->keyRingId, $keyRing);
            }
        }

        try {
            $this->kms->getCryptoKey($this->cryptoKeyRef);
        } catch (ApiException $e) {
            if ($e->getStatus() === 'NOT_FOUND') {
                $cryptoKey = new CryptoKey();
                $cryptoKey->setPurpose(CryptoKeyPurpose::ENCRYPT_DECRYPT);
                $this->kms->createCryptoKey($this->keyRingRef, $this->cryptoKeyId, $cryptoKey);
            }
        }
    }

    public function encrypt($data){
        $key        = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        $nonce      = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($data, $nonce, $key);

        return [
            'data'   => base64_encode($nonce . $ciphertext),
            'secret' => $this->encryptKey($key),
        ];
    }

    public function encryptKey($key){
        $secret = base64_encode($key);

        $response = $this->kms->encrypt(
            $this->cryptoKeyRef,
            $secret
        );

        return $response->getCiphertext();
    }

    public function decryptKey($secret){
        $response = $this->kms->decrypt(
            $this->cryptoKeyRef,
            $secret
        );

        return base64_decode($response->getPlaintext());
    }

    public function decrypt($secret, $data){
        $decoded    = base64_decode($data);
        $key        = $this->decryptKey($secret);
        $nonce      = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        return sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
    }
}