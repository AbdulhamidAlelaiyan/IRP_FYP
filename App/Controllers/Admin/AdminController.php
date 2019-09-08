<?php

namespace App\Controllers\Admin;

use App\Auth;
use App\Flash;
use \Core\View;

class AdminController extends \App\Controllers\Authenticated
{

    /**
     * The before action filter will authenticate it is a user
     * and it is an admin
     *
     * @return boolean
     */
    protected function before()
    {
        parent::before();
        $user = Auth::getUser();
        if($user->type != 'admin')
        {
            Flash::addMessage('You must be an admin to access this page.', Flash::WARNING);
            Auth::rememberRequestedPage();
            $this->redirect('/login');
        }
    }

}