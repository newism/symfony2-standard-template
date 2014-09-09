<?php

namespace Nsm\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NsmUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
