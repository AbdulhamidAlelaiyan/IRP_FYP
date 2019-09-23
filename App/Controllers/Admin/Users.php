<?php


namespace App\Controllers\Admin;

use App\Flash;
use App\Models\User;
use \Core\View;

class Users extends AdminController
{
    /**
     * List all users for the admin
     *
     * @return void
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

    /**
     * Load the view of edit user
     *
     * @return void
     */
    public function editAction()
    {
        $id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $user = User::findByID($id);
        View::renderTemplate('Admin/Users/edit.html.twig',
            [
                'user' => $user,
            ]);
    }

    /**
     * Update user record
     *
     * @return void
     */
    public function updateAction()
    {
        $id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $user = User::findByID($id);
        $user_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $user_type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $user_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $user_password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        if(strlen($user_password) > 6)
        {
            if (preg_match('/.*[a-z]+.*/i', $user_password) == 0)
            {
                $password_match = false;
            }

            if (preg_match('/.*\d+.*/i', $user_password) == 0)
            {
                $password_match = false;
            }
            $user_confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);
            $password_match = $user_password === $user_confirm_password;
        }
        else
        {
            $password_match = false;
        }
        if($user_name && $user_type && $user_email)
        {
            $updated_data = [
                'name' => $user_name,
                'email' => $user_email,
                'type' => $user_type,
            ];
            if($password_match)
            {
                $updated_data['password'] = $user_password;
            }
            else
            {
                $updated_data['password'] = '';
            }
            $updated = $user->updateProfile($updated_data);
            if($updated)
            {
                Flash::addMessage('User updated', Flash::SUCCESS);
                $this->redirect('/admin/users/index');
            }
            else
            {
                Flash::addMessage('Error in user update', Flash::WARNING);
                View::renderTemplate('Admin/Users/edit.html.twig',
                    [
                        'user' => $user,
                        'errors' => $user->errors,
                    ]);
            }
        }
    }


    /**
     * Load the view of confirm delete user
     *
     * @return void
     */
    public function deleteAction()
    {
        $user = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $user = User::findByID($user);
        View::renderTemplate('Admin/Users/delete-confirm.html.twig',
            [
                'user' => $user,
            ]);
    }

    /**
     * Delete the specified user
     *
     * @return void
     */
    public function destroyAction()
    {
        $id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        if(User::deleteUserByID($id))
        {
            Flash::addMessage('User Deleted!', Flash::INFO);
            $this->redirect('/admin/users/index');
        }
        else
        {
            Flash::addMessage('Error in user deletion!', Flash::WARNING);
            $this->redirect('/admin/users/index');
        }
    }

    /**
     * Find results of search
     *
     * @return void
     */
    public function searchAction()
    {
        $email = rtrim($_GET['email']);
        $email = filter_var( $email, FILTER_VALIDATE_EMAIL);
        $name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
        if($email)
        {
            $users = [User::findByEmail($email)];
            View::renderTemplate('Admin/Users/index.html.twig',
            [
                'users' => $users,
            ]);
        }
        elseif($name)
        {
            $users = User::findByName($name);
            View::renderTemplate('Admin/Users/index.html.twig',
            [
                'users' => $users,
            ]);
        }
    }
}