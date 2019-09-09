<?php

namespace App\Controllers;

use App\Auth;
use \Core\View;
use \App\Models\User;

/**
 * Password controller
 *
 * PHP version 7.0
 */
class Password extends \Core\Controller
{

    /**
     * Show the forgotten password page
     *
     * @return void
     */
    public function forgotAction()
    {
        $this->logger->addInfo('Forget form requested');
        View::renderTemplate('Password/forgot.html.twig');
    }

    /**
     * Send the password reset link to the supplied email
     *
     * @return void
     */
    public function requestResetAction()
    {
        User::sendPasswordReset($_POST['email']);
        $this->logger->addInfo('Password reset sent', ['user-email', Auth::getUserEmailForLogger()]);
        View::renderTemplate('Password/reset_requested.html.twig');
    }

    /**
     * Show the reset password form
     *
     * @return void
     */
    public function resetAction()
    {
        $token = $this->route_params['token'];

        $user = $this->getUserOrExit($token);

        View::renderTemplate('Password/reset.html.twig', [
            'token' => $token
        ]);
    }

    /**
     * Reset the user's password
     *
     * @return void
     */
    public function resetPasswordAction()
    {
        $token = $_POST['token'];

        $user = $this->getUserOrExit($token);

        if ($user->resetPassword($_POST['password'])) {

            $this->logger->addInfo('Password got reset for ' . $user->email, ['user-email', Auth::getUserEmailForLogger()]);
            View::renderTemplate('Password/reset_success.html.twig');
        
        } else {
            View::renderTemplate('Password/reset.html.twig', [
                'token' => $token,
                'user' => $user
            ]);
        }
    }

    /**
     * Find the user model associated with the password reset token, or end the request with a message
     *
     * @param string $token Password reset token sent to user
     *
     * @return mixed User object if found and the token hasn't expired, null otherwise
     */
    protected function getUserOrExit($token)
    {
        $user = User::findByPasswordReset($token);

        if ($user) {

            return $user;

        } else {
            $this->logger->addInfo('Password reset request failed with token ' . $token, ['user-email' => Auth::getUserEmailForLogger()]);
            View::renderTemplate('Password/token_expired.html.twig');
            exit;

        }
    }
}
