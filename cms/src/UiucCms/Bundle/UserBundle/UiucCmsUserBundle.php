<?php

namespace UiucCms\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UiucCmsUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
