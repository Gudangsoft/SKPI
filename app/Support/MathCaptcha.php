<?php

namespace App\Support;

class MathCaptcha
{
    /**
     * @return array{0: int, 1: int}
     */
    public static function generate(string $context): array
    {
        $a = random_int(1, 10);
        $b = random_int(1, 10);

        session([static::sessionKey($context) => $a + $b]);

        return [$a, $b];
    }

    public static function check(string $context, mixed $answer): bool
    {
        $key = static::sessionKey($context);
        $expected = session($key);

        session()->forget($key);

        if ($expected === null || $answer === null || $answer === '') {
            return false;
        }

        return is_numeric($answer) && (int) $answer === (int) $expected;
    }

    protected static function sessionKey(string $context): string
    {
        return "math_captcha.{$context}";
    }
}
