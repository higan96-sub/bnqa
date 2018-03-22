<?php

namespace Acme\BnqaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeBnqaBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
