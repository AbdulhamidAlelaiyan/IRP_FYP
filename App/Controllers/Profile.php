<?php

namespace App\Controllers;

use App\Models\User;
use \Core\View;
use \App\Auth;
use \App\Flash;

/**
 * Profile controller
 *
 * PHP version 7.0
 */
class Profile extends Authenticated
{

    /**
     * Before filter - called before each action method
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * Show the profile
     *
     * @return void
     */
    public function showAction()
    {
        View::renderTemplate('Profile/view.html.twig', [
            'user' => $this->user,
            'edit' => 'edit',
        ]);
    }

    /**
     * Show the form for editing the profile
     *
     * @return void
     */
    public function editAction()
    {
        View::renderTemplate('Profile/edit.html.twig', [
            'user' => $this->user
        ]);
    }

    /**
     * Update the profile
     *
     * @return void
     */
    public function updateAction()
    {
		if(hash_equals($_SESSION['csrf_token'], $_POST['token']) && $_POST['spam-protection'] === '')
		{
		    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);
			if ($this->user->updateProfile($_POST, false, $bio)) {

				Flash::addMessage('Changes saved');

				$this->redirect('/profile/show');

			} else {

				View::renderTemplate('Profile/edit.html.twig', [
					'user' => $this->user
				]);

			}
		}
    }

    /**
     * View the profile of a specific user
     *
     * @return void
     */
    public function viewAction()
    {
        $user_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($user = User::findByID($user_id))
        {
            View::renderTemplate('Profile/view.html.twig',
                [
                    'user' => $user,
                ]);
        }
        else
        {
            Flash::addMessage('User not found!', Flash::DANGER);
            $this->redirect('/');
        }
    }
}
