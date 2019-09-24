<?php


namespace App\Controllers\Admin;

use App\Flash;
use App\Models\Reply;
use \Core\View;

class Replies extends AdminController
{
    /**
     * Delete specified reply
     */
    public function destroyAction()
    {
        $reply_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($reply_id)
        {
            $reply = Reply::getReplyByID($reply_id);
            if(Reply::deleteReply($reply_id))
            {
                Flash::addMessage('Reply Deleted', Flash::SUCCESS);
                $this->redirect('/admin/posts/view/' . $reply->post_id);
            }
            else
            {
                Flash::addMessage('Error in reply deletion', Flash::DANGER);
                $this->redirect('/admin/posts/view/' . $reply->post_id);
            }
        }
        else
        {
            Flash::addMessage('Reply was not found', Flash::DANGER);
            $this->redirect('/admin/posts/index');
        }
    }
}