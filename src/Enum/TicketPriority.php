<?php

namespace App\Enum;

enum TicketPriority: string
{
    case low = 'low';

    case normal = 'normal';

    case urgent = 'urgent';
}
