<?php
namespace App\Enum;

enum TicketStatus: string
{
    case open = 'open';
    case in_progress = 'in_progress';

    case resolved = 'resolved';

    case waiting_for_resident_eval = 'waiting_for_resident_eval';

    case validated_from_resident = 'validated_from_resident';

    case closed = 'closed';
}
