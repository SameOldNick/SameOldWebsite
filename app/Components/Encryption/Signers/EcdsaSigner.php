<?php

namespace App\Components\Encryption\Signers;

use App\Components\Encryption\Exceptions\InvalidConfigurationException;
use EllipticCurve\Ecdsa;
use EllipticCurve\PrivateKey;
use EllipticCurve\Signature;
use Exception;
use Illuminate\Support\Arr;
use function Safe\file_get_contents;

class EcdsaSigner implements Signer
{
    public function __construct(
        private readonly array $config
    ) {
    }

    public function sign(string $message): string
    {
        $key = $this->getPrivateKey();

        $signature = Ecdsa::sign($message, $key);

        return $signature->toBase64();
    }

    public function verify(string $message, string $signatureBase64): bool
    {
        try {
            $signature = Signature::fromBase64($signatureBase64);
            $key = $this->getPrivateKey();

            return Ecdsa::verify($message, $signature, $key->publicKey());
        } catch (Exception $ex) {
            return false;
        }
    }

    public function generate(): string
    {
        $privateKey = new PrivateKey;

        return $privateKey->toPem();
    }

    private function getPrivateKey()
    {
        $type = Arr::get($this->config, 'type');

        if ($type === 'file') {
            return $this->getPrivateKeyFromFile();
        } elseif ($type === 'string') {
            return $this->getPrivateKeyFromContents();
        }

        throw new InvalidConfigurationException("Private key file type '{$type}' is invalid.");
    }

    private function getPrivateKeyFromFile()
    {
        $path = Arr::get($this->config, 'path');

        if (! is_readable($path)) {
            throw new InvalidConfigurationException("File '{$path}' is not readable.");
        }

        $contents = file_get_contents($path);

        return $this->createPrivateKey($contents);
    }

    private function getPrivateKeyFromContents()
    {
        $contents = Arr::get($this->config, 'contents');

        return $this->createPrivateKey($contents);
    }

    private function createPrivateKey(string $contents)
    {
        return PrivateKey::fromPem($contents);
    }
}
