<?php

declare(strict_types=1);

namespace App\Concerns\Model;

use Illuminate\Support\Str;

trait EncryptUnique
{
    private $iv = '1234567812345678';

    public function getRouteKey()
    {
        return $this->encrypted_id;
    }

    public function getEncryptedIdAttribute(): ?string
    {
        return $this->encrypt($this->attributes['id']);
    }

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
