<?php

namespace App\Interfaces;

Interface IManager
{
    public function activateNotification(): bool;

    public function notification();
}