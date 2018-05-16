<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CustomerReorderAction
{
    public function __invoke(Request $request): Response
    {
        return new Response();
    }
}
