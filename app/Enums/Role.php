<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case SALES_MANAGER = 'sales_manager';
    case ONSITE_VENDOR = 'onsite_vendor';
    case OFFSITE_VENDOR = 'offsite_vendor';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::MANAGER => 'Recon Manager',
            self::SALES_MANAGER => 'Sales Manager',
            self::ONSITE_VENDOR => 'On-Site Vendor',
            self::OFFSITE_VENDOR => 'Off-Site Vendor',
        };
    }

    public function canEnterCosts(): bool
    {
        return match($this) {
            self::ADMIN, self::MANAGER, self::ONSITE_VENDOR => true,
            default => false,
        };
    }

    public function canApproveEstimates(): bool
    {
        return match($this) {
            self::ADMIN, self::SALES_MANAGER => true,
            default => false,
        };
    }

    public function isVendor(): bool
    {
        return match($this) {
            self::ONSITE_VENDOR, self::OFFSITE_VENDOR => true,
            default => false,
        };
    }
} 