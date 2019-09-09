<?php


namespace App\Controllers\Admin;

use \Core\View;

class Home extends AdminController
{

    public function indexAction()
    {
        View::renderTemplate('Admin/base.html.twig');
    }
}