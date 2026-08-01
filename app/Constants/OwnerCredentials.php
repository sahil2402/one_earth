<?php

namespace App\Constants;

/**
 * Temporary bootstrap credentials for the first application owner.
 * Move these values to a database-backed user account before production use.
 */
final class OwnerCredentials
{
    public const EMAIL = 'test@gmail.com';
    public const PASSWORD = '12345678';
    public const ROLE = Role::OWNER;
}
