<?php


namespace App\Controllers;


use App\Auth;
use App\Flash;
use App\Models\User;
use Core\View;
use App\Models\Message;

class Messages extends \Core\Controller
{
    /**
     * Load the new message view
     *
     * @return void
     */
    public function newAction()
    {
        $user_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($user_id)
        {
            if($user = User::findByID($user_id))
            {
                View::renderTemplate('Messages/new.html.twig',
                    [
                        'user' => $user,
                    ]);
            }
            else
            {
                Flash::addMessage('User not foubd', Flash::DANGER);
                $this->redirect('/profile/view/' . $user_id);
            }
        }
        else
        {
            Flash::addMessage('User id not found', Flash::DANGER);
            $this->redirect('/');
        }
    }

    /**
     * Create a new message
     *
     * @return void
     */
    public function createAction()
    {
        $message = new Message($_POST);
        if ($message->save())
        {
            Flash::addMessage('Message sent', Flash::SUCCESS);
            $this->redirect('/profile/show');
        }
        else
        {
            if($message->to)
            {
                $user = User::findByID($message->to);
                $current_user = Auth::getUser();
                View::renderTemplate('/Messages/new.html.twig',
                    [
                        'user' => $user,
                        'current_user' => $current_user,
                        'errors' => $message->getErrors(),
                    ]);
            }
            else
            {
                Flash::addMessage('Error in sending message', Flash::DANGER);
                $this->redirect('/profile/show');
            }
        }
    }

    /**
     * Load the inbox of a user
     *
     * @return void
     */
    public function indexAction()
    {
        $user = Auth::getUser();
        $user_id = $user->id;
        $messages = Message::getInboxMessages($user_id);
        View::renderTemplate('Messages/inbox.html.twig',
            [
                'messages' => $messages,
            ]);
    }

    /**
     * Load the view of a message
     *
     * @return void
     */
    public function viewAction()
    {
        $message_id = filter_var($this->route_params['isbn'], FILTER_VALIDATE_INT);
        if($message_id)
        {
            if($message = Message::getMessageByID($message_id))
            {
                View::renderTemplate('Messages/view.html.twig',
                    [
                        'message' => $message,
                    ]);
            }
            else
            {
                Flash::addMessage('Message not found', Flash::DANGER);
                $this->redirect('/');
            }
        }
        else
        {
            Flash::addMessage('Message not found', Flash::DANGER);
            $this->redirect('/');
        }
    }
}