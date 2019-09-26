<?php


namespace App\Controllers;


use App\Flash;
use App\Models\Post;
use App\Models\Reply;
use App\Models\Report;
use Core\View;

class Reports extends Authenticated
{

    /**
     * Load a view of submitting a report
     *
     * @return void
     */
    public function addAction()
    {
        $post_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($post_id)
        {
            if($post = Post::getPostByID($post_id))
            {
                View::renderTemplate('Reports/report.html.twig',
                    [
                        'post' => $post,
                    ]);
            }
            else
            {
                Flash::addMessage('Post was not found', Flash::DANGER);
                $this->redirect('/');
            }
        }
        else
        {
            Flash::addMessage('Post ID was not found', Flash::DANGER);
            $this->redirect('/');
        }
    }

    /**
     * Submit report of a post to the database
     *
     * @return void
     */
    public function createAction()
    {
        $post_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($post_id)
        {
            if($post = Post::getPostByID($post_id))
            {
                $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);
                if($text)
                {
                    if(Report::addReport($text, $post->id))
                    {
                        Flash::addMessage('Report was submitted', Flash::SUCCESS);
                        $this->redirect('/');
                    }
                    else
                    {
                        Flash::addMessage('Error in report submission!', Flash::DANGER);
                        $this->redirect('/');
                    }
                }
                else
                {
                    Flash::addMessage('Post text was not found', Flash::DANGER);
                    $this->redirect('/');
                }
            }
            else
            {
                Flash::addMessage('Post was not found', Flash::DANGER);
                $this->redirect('/');
            }
        }
        else
        {
            Flash::addMessage('Post ID was not found', Flash::DANGER);
            $this->redirect('/');
        }
    }

    /**
     * Load the view of reply report
     *
     * @return void
     */
    public function addReplyAction()
    {
        $reply_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($reply_id)
        {
            if($reply = Reply::getReplyByID($reply_id))
            {
                View::renderTemplate('Reports/report-reply.html.twig',
                    [
                        'reply' => $reply,
                    ]);
            }
            else
            {
                Flash::addMessage('Reply was not found', Flash::DANGER);
                $this->redirect('/');
            }
        }
        else
        {
            Flash::addMessage('Reply ID was not found', Flash::DANGER);
            $this->redirect('/');
        }
    }

    /**
     * Submit report of a reply to the database
     *
     * @return void
     */
    public function createReplyAction()
    {
        $reply_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($reply_id)
        {
            if($reply = Reply::getReplyByID($reply_id))
            {
                $text = filter_input(INPUT_POST, 'text', FILTER_SANITIZE_STRING);
                if($text)
                {
                    if(Report::addReplyReport($text, $reply->id))
                    {
                        Flash::addMessage('Report was submitted', Flash::SUCCESS);
                        $this->redirect('/');
                    }
                    else
                    {
                        Flash::addMessage('Error in report submission!', Flash::DANGER);
                        $this->redirect('/');
                    }
                }
                else
                {
                    Flash::addMessage('Reply text was not found', Flash::DANGER);
                    $this->redirect('/');
                }
            }
            else
            {
                Flash::addMessage('Reply was not found', Flash::DANGER);
                $this->redirect('/');
            }
        }
        else
        {
            Flash::addMessage('Reply ID was not found', Flash::DANGER);
            $this->redirect('/');
        }
    }
}