<?php

namespace App\Webhook\Handlers;

use Shopify\Webhooks\Handler;

class AppUninstalled implements Handler
{
    public function handle(string $topic, string $shop, array $requestBody): void
    {
        // Handle your webhook here!
    }
}
