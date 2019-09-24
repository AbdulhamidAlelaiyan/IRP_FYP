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

    /**
     * Up vote replies
     *
     * @return void
     */
    public function upvoteAction()
    {
        $reply_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        if($reply_id)
        {
            $post = Post::getPostByReplyID($reply_id);
            $post_id = $post->id;
            $upvoted = Reply::upvoteReplyByID($reply_id);
            if($upvoted === 'removed')
            {
                Flash::addMessage('Vote removed');
                $this->redirect('/posts/view/' . $post_id);
            }
            elseif($upvoted)
            {
                Flash::addMessage('Reply upvoted');
                $this->redirect('/posts/view/' . $post_id);
            }
            else
            {
                Flash::addMessage('Reply was not upvoted', Flash::WARNING);
                $this->redirect('/posts/view/' . $post_id);
            }
        }
        else
        {
            $this->redirect('/');
        }
    }

    /**
     * Down vote replies
     *
     * @return void
     */
    public function downvoteAction()
    {
        $reply_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        if($reply_id)
        {
            $post = Post::getPostByReplyID($reply_id);
            $post_id = $post->id;
            $downvoted = Reply::downvoteReplyByID($reply_id);
            if($downvoted === 'removed')
            {
                Flash::addMessage('Vote removed');
                $this->redirect('/posts/view/' . $post_id);
            }
            elseif($downvoted)
            {
                Flash::addMessage('Reply downvoted', Flash::DANGER);
                $this->redirect('/posts/view/' . $post_id);
            }
            else
            {
                Flash::addMessage('Reply was not downvoted', Flash::WARNING);
                $this->redirect('/posts/view/' . $post_id);
            }
        }
        else
        {
            $this->redirect('/');
        }
    }

}