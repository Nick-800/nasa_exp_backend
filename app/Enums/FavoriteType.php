<?php

namespace App\Enums;

enum FavoriteType: string
{
    case APOD = 'apod';
    case NEO = 'neo';
    case EONET = 'eonet';
    case TREK = 'trek';
    case SPACE_WEATHER = 'space_weather';
}
