<?php

namespace App\Controllers;

use App\Flash;
use App\Models\Post;
use App\Models\Reply;
use \Core\View;

class Replies extends \Core\Controller
{
    /**
     * Add a reply from a user for a post
     *
     * @return void
     */
    public function createAction()
    {
        $reply = new Reply($_POST);
        if($reply->validate())
        {
            if ($reply->save())
            {
                $this->redirect('/posts/view/' . $reply->post_id);
            }
            else
            {
                Flash::addMessage('There was an error adding the reply', Flash::WARNING);
                $this->redirect('/');
            }
        }
        else
        {
            Flash::addMessage('There was an error adding the reply', Flash::WARNING);
            $this->redirect('/');
        }
    }

}