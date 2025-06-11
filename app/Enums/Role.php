<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case RECON_MANAGER = 'Recon Manager';
    case SALES_PERSON = 'Sales person';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::MANAGER => 'Manager',
            self::SALES_PERSON => 'Sales person',
        };
    }

    public function canEnterCosts(): bool
    {
        return match ($this) {
            self::ADMIN, self::MANAGER, self::ONSITE_VENDOR => true,
            default => false,
        };
    }

    public function canApproveEstimates(): bool
    {
        return match ($this) {
            self::ADMIN, self::SALES_MANAGER => true,
            default => false,
        };
    }

    public function isVendor(): bool
    {
        return match ($this) {
            self::ONSITE_VENDOR, self::OFFSITE_VENDOR => true,
            default => false,
        };
    }
}
