<?php

namespace App\Controllers\Admin;

use App\Controllers\Authenticated;
use \Core\View;

class Books extends Authenticated
{
    protected function indexAction()
    {
        echo 'Admin namespace, Books controller, index action';
    }
}