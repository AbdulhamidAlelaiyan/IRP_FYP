<?php


namespace App\Controllers\Admin;

use \Core\View;

class Home extends AdminController
{

    public function indexAction()
    {
        $this->redirect('/admin/books/index');
    }
}