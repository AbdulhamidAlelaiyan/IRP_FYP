<?php

namespace App\Controllers;

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
        $post_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        if($post_id)
        {
            $reply = new Reply();
        }
        else
        {
            $this->redirect('/');
        }
    }
}