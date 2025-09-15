<?php

declare(strict_types=1);

namespace App\Concerns\Model;

use Illuminate\Support\Str;

// @phpstan-ignore trait.unused
trait EncryptUnique
{
    private $iv = '1234567812345678';

    /**
     * Get the encrypted ID attribute.
     *
     * Returns the encrypted ID of the model.
     */
    protected function getEncryptedIdAttribute(): ?string
    {
        return $this->encrypt($this->attributes['id']);
    }

    /**
     * Get the route key for the model.
     *
     * Returns the encrypted ID for route model binding.
     */
    public function getRouteKey()
    {
        return $this->encrypted_id;
    }

    /**
     * Decrypts a value.
     * Decodes and decrypts a base64 encoded string using AES-256-CBC.
     */
    private function decrypt(string $value): mixed
    {
        $decoded = base64_decode($value);

        try {
            $decrypt = (string) openssl_decrypt(
                data: $decoded,
                cipher_algo: 'aes-256-cbc',
                passphrase: config('app.key'),
                options: 0,
                iv: $this->iv
            );
        } catch (\Throwable) {
            return null;
        }

        return Str::of($decrypt)->isJson()
            ? json_decode($decrypt, true, 512, JSON_THROW_ON_ERROR)
            : $decrypt;
    }

    /**
     * Encrypts a value.
     *
     * Encrypts the given value using AES-256-CBC and returns the base64 encoded result.
     */
    private function encrypt(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_THROW_ON_ERROR);
        }

        if (is_float($value) || is_int($value)) {
            $value = (string) $value;
        }

        $encrypted = (string) openssl_encrypt(
            data: $value,
            cipher_algo: 'aes-256-cbc',
            passphrase: config('app.key'),
            options: 0,
            iv: $this->iv
        );

        return base64_encode($encrypted);
    }
}
