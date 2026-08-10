<?php

namespace App\Support;

class Roles
{
    public const SUPER_ADMIN = 'super_admin';
    public const ADMIN_PRODI = 'admin_prodi';
    public const KAPRODI = 'kaprodi';
    public const MAHASISWA = 'mahasiswa';

    public const ALL = [
        self::SUPER_ADMIN,
        self::ADMIN_PRODI,
        self::KAPRODI,
        self::MAHASISWA,
    ];
}
