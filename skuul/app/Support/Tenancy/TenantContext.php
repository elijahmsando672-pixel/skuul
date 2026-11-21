<?php

namespace App\Support\Tenancy;

class TenantContext
{
    protected static ?int $schoolId = null;
    protected static mixed $school = null;

    public static function set(mixed $school): void
    {
        static::$school = $school;
        static::$schoolId = $school?->id;
    }

    public static function school(): mixed
    {
        return static::$school;
    }

    public static function schoolId(): ?int
    {
        return static::$schoolId;
    }

    public static function clear(): void
    {
        static::$school = null;
        static::$schoolId = null;
    }
}

