<?php


namespace App\Controllers\Admin;

use App\Models\User;
use \Core\View;

class Users extends AdminController
{
    /**
     * List all users for the admin
     */
    public function indexAction()
    {
        $usersWithPaginator = User::getUsers();
        $users = $usersWithPaginator[0];
        $paginator = $usersWithPaginator[1];
        View::renderTemplate('Admin/users/index.html.twig',
            [
                'users' => $users,
                'paginator' => $paginator,
            ]);
    }
}