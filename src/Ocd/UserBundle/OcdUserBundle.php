<?php
// src/Ocd/UserBundle/OcdUserBundle.php

namespace Ocd\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OcdUserBundle extends Bundle
{
  public function getParent()
  {
    return 'FOSUserBundle';
  }
}