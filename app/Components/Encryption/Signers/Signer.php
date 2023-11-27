<?php

namespace App\Components\Encryption\Signers;

interface Signer {
    /**
     * Creates signature for message.
     *
     * @param string $message
     * @return string
     */
    public function sign(string $message): string;

    /**
     * Verifies the signature is for the message.
     *
     * @param string $message
     * @param string $signature
     * @return boolean
     */
    public function verify(string $message, string $signature): bool;

    /**
     * Generates a private key.
     *
     * @return string
     */
    public function generate(): string;
}
