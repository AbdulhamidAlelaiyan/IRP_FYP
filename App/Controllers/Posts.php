<?php

namespace App\Controllers;

use App\Auth;
use App\Flash;
use App\Models\Book;
use App\Models\Post;
use App\Models\User;
use \Core\View;

class Posts extends \Core\Controller
{
    /**
     * Load the view of the posts index
     *
     * @return void
     */
    public function indexAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        $book = Book::getBookByISBN($isbn);
        $booksPosts = Post::getPosts($isbn);
        foreach($booksPosts[0] as $post)
        {
            $user = User::findByID($post->user_id);
            $post->username = $user->name;
        }
        View::renderTemplate('Posts/index.html.twig',
            [
                'book' => $book,
                'posts' => $booksPosts[0],
                'pagination' => $booksPosts[1],
            ]);
    }

    /**
     * Load the view of new post
     *
     * @return void
     */
    public function newAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        $book = Book::getBookByISBN($isbn);
        View::renderTemplate('Posts/new.html.twig',
            [
                'book' => $book,
            ]);
    }

    /**
     * Create new post
     *
     * @return void
     */
    public function createAction()
    {
        $post = new Post($_POST);
        if($post->save())
        {
            Flash::addMessage('Post has been created.', Flash::SUCCESS);
            $this->redirect('/posts/index/' . $_POST['isbn']);
        }
    }

    /**
     * Load the view of a post
     *
     * @return void
     */
    public function viewAction()
    {
        $id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        $post = Post::getPostByID($id);
        $book = Book::getBookByISBN($post->isbn);
        View::renderTemplate('Posts/view.html.twig',
            [
                'book' => $book,
                'post' => $post,
            ]);
    }

}