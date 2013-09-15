<?php

namespace Amap\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AmapUserBundle extends Bundle
{
	public function getParent()
    {
        return 'SonataUserBundle';
    }
}
