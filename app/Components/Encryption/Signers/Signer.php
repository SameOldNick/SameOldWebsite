<?php

namespace App\Components\Encryption\Signers;

interface Signer
{
    /**
     * Creates signature for message.
     */
    public function sign(string $message): string;

    /**
     * Verifies the signature is for the message.
     */
    public function verify(string $message, string $signature): bool;

    /**
     * Generates a private key.
     */
    public function generate(): string;
}
