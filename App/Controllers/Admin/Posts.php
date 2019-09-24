<?php


namespace App\Controllers\Admin;

use App\Models\Post;
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
}