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
}