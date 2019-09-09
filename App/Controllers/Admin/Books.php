<?php

namespace App\Controllers\Admin;

use App\Models\Book;
use \Core\View;

class Books extends AdminController
{
    public function indexAction()
    {
        echo 'Admin namespace, Books controller, index action';
    }


    /**
     * Load the view of new book form
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Admin/Books/new.html.twig');
    }

    /**
     * Add the new book to the database
     *
     * @return void
     */
    public function createAction()
    {
        $newBook = new Book($_POST);
        if($newBook->save())
        {
            $this->redirect('/admin/books/add-success');
        }
        else
        {
            View::renderTemplate('Admin/Books/new.html.twig',
                [
                    'errors' => $newBook->getErrors(),
                ]);
        }
    }

    /**
     * Load the add-success view
     *
     * @return void
     */
    public function addSuccessAction()
    {
        View::renderTemplate('Admin/Books/add-success.html.twig');
    }
}