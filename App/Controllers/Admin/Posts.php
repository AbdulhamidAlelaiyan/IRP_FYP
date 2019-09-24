<?php


namespace App\Controllers\Admin;

use App\Flash;
use App\Models\Book;
use App\Models\Post;
use App\Models\Reply;
use App\Models\User;
use \Core\View;

class Posts extends AdminController
{
    /**
     * Load the admin index view for posts
     *
     * @return void
     */
    public function indexAction()
    {
        $posts = Post::getAllPosts();
        View::renderTemplate('Admin/Posts/index.html.twig',
            [
                'posts' => $posts[0],
                'pagination' => $posts[1],
            ]);
    }

    /**
     * View the post specified by the id
     *
     * @return void
     */
    public function viewAction()
    {
        $post_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($post_id)
        {
            $post = Post::getPostByID($post_id);
            $writer = User::findByID($post->user_id);
            $points = Post::calculatePoints($post->id);
            $book = Book::getBookByISBN($post->isbn);
            $replies = Reply::getRepliesByPostID($post->id);
            View::renderTemplate('Admin/Posts/view.html.twig',
                [
                    'post' => $post,
                    'writer' => $writer,
                    'up_points' => $points[0],
                    'down_points' => $points[1],
                    'book' => $book,
                    'replies' => $replies,
                ]);
        }
        else
        {
            Flash::addMessage('Post specified was not found!', Flash::DANGER);
            $this->redirect('/admin/posts');
        }
    }

    /**
     * Load the view of Post Edit
     *
     * @return void
     */
    public function editAction()
    {
        $post_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($post_id)
        {
            $post = Post::getPostByID($post_id);
            View::renderTemplate('Admin/Posts/edit.html.twig',
                [
                    'post' => $post,
                ]);
        }
    }

    /**
     * Update a particular post
     *
     * @return void
     */
    public function updateAction()
    {
        $post_id = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        if(isset($_POST['editordata']))
        {
            $config = \HTMLPurifier_HTML5Config::createDefault();
            $purifier = new \HTMLPurifier($config);
            $body = $purifier->purify($_POST['editordata']);
        }
        else
        {
            $body = '';
        }
        $post = Post::getPostByID($post_id);
        $post->title = $title;
        $post->body = $body;
        if($post->update())
        {
            Flash::addMessage('Post Updated successfully', Flash::SUCCESS);
        }
        else
        {
            Flash::addMessage('Error in Post update!', Flash::DANGER);
        }
        $this->redirect('/admin/posts/index');
    }

    /**
     * Load the delete-confirm view
     *
     * @return void
     */
    public function deleteAction()
    {
        $post_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($post_id)
        {
            $post = Post::getPostByID($post_id);
            if($post)
            {
                $book = Book::getBookByISBN($post->isbn);
                View::renderTemplate('Admin/Posts/delete-confirm.html.twig',
                    [
                        'post' => $post,
                        'book' => $book,
                    ]);
            }
            else
            {
                Flash::addMessage('Post was not found!', Flash::DANGER);
                $this->redirect('/admin/posts/index');
            }
        }
    }

    /**
     * Delete the post from the database
     *
     * @return void
     */
    public function destroyAction()
    {
        $post_id = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);
        if($post_id)
        {
            $post = Post::getPostByID($post_id);
            if($post)
            {
                if($post->delete())
                {
                    Flash::addMessage('Post deleted');
                }
                else
                {
                    Flash::addMessage('Error in post deletion', Flash::DANGER);
                }
            }
            else
            {
                Flash::addMessage('post was not found');
            }
        }
        else
        {
            Flash::addMessage('post was not found');
        }
        $this->redirect('/admin/posts/index');
    }
}