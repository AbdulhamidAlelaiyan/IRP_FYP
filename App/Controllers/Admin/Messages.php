<?php

namespace App\Controllers\Admin;

use App\Flash;
use App\Models\Message;
use \Core\View;

class Messages extends \Core\Controller
{
    /**
     * Load all messages for the admin dashboard
     *
     * @return void
     */
    public function indexAction()
    {
        $messages = Message::getAllMessages();
        View::renderTemplate('Admin/Messages/index.html.twig',
            [
                'messages' => $messages[0],
                'paginator' => $messages[1],
            ]);

    }

    /**
     * Load delete-confirm view
     *
     * @return void
     */
    public function delete()
    {
        $message_id = filter_var($this->route_params['isbn'], FILTER_VALIDATE_INT);
        if($message_id)
        {
            if($message = Message::getMessageByID($message_id, true))
            {
                View::renderTemplate('Admin/Messages/delete-confirm.html.twig',
                    [
                        'message' => $message,
                    ]);
            }
            else
            {
                Flash::addMessage('Message is not found', Flash::DANGER);
                $this->redirect('/admin/messages/index');
            }
        }
        else
        {
            Flash::addMessage('Message ID is not found', Flash::DANGER);
            $this->redirect('/admin/messages/index');
        }
    }

    /**
     * Delete the message from the messages table in the database
     *
     * @return void
     */
    public function destroy()
    {
        $message_id = filter_var($this->route_params['isbn'], FILTER_VALIDATE_INT);
        if($message_id)
        {
            if($message = Message::getMessageByID($message_id, true))
            {
                $message->delete();
                Flash::addMessage('Message Deleted');
                $this->redirect('/admin/messages/index');
            }
            else
            {
                Flash::addMessage('Message is not found', Flash::DANGER);
                $this->redirect('/admin/messages/index');
            }
        }
        else
        {
            Flash::addMessage('Message ID is not found', Flash::DANGER);
            $this->redirect('/admin/messages/index');
        }
    }

    /**
     * View the message content
     *
     * @return void
     */
    public function viewAction()
    {
        $message_id = filter_var($this->route_params['isbn'], FILTER_VALIDATE_INT);
        if($message_id && $message = Message::getMessageByID($message_id, true))
        {
            View::renderTemplate('Admin/Messages/view.html.twig',
                [
                    'message' => $message,
                ]);
        }
        else
        {
            Flash::addMessage('Message is not found', Flash::DANGER);
            $this->redirect('/admin/messages/index');
        }
    }
}