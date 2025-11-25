<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case CALISAN = 'calisan';
    case BIRIM_AMIRI = 'birim_amiri';
    case ALAN_AMIRI = 'alan_amiri';
    case ISG_UZMANI = 'isg_uzmani';
    case ISVEREN_VEKILI = 'isveren_vekili';
}
