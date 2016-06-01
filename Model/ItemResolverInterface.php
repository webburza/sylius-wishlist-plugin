<?php

namespace Webburza\Sylius\WishlistBundle\Model;

use Symfony\Component\HttpFoundation\Request;

interface ItemResolverInterface
{
    public function resolve(Request $request);
}
