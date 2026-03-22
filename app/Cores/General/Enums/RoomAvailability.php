<?php

namespace App\Cores\General\Enums;


enum RoomAvailability: string
{
    case AVAILABLE = '1';   // maps to true
    case UNAVAILABLE = '0'; // maps to false
}
