<?php

namespace App\Enums;

enum VendorStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case REJECTED = 'rejected';
    case INACTIVE = 'inactive';
}
