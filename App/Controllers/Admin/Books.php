<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Flash;
use App\Models\Book;
use \Core\View;

class Books extends AdminController
{
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

    /**
     * Load the edit view
     *
     * @return void
     */
    public function indexAction()
    {
        $booksWithPagination = Book::getBooks();
        $books = $booksWithPagination[0];
        $pagination = $booksWithPagination[1];
        View::renderTemplate('Admin/Books/index.html.twig', [
            'books' => $books,
            'pagination' => $pagination,
        ]);
    }

    /**
     * return search results based on either ISBN, Title
     *
     * @return void
     */
    public function searchAction()
    {
        if($isbn = filter_input(INPUT_GET, 'isbn', FILTER_SANITIZE_STRING))
        {
            $bookfound = Book::getBookByISBN($isbn);
            $books[0] = $bookfound;
        }
        elseif($title = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_STRING))
        {
            $books = Book::getBooksByTitle($title);
        }

        $pagination = new \Zebra_Pagination();
        $pagination->records(count($books));
        $pagination->records_per_page(10);

        $books = array_slice(
            $books,
            (($pagination->get_page() - 1) * 10),
            10
        );

        $pagination = $pagination->render(true);
        View::renderTemplate('Admin/Books/index.html.twig', [
            'books' => $books,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Show the book information view
     *
     * @return void
     */
    public function viewAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $book = Book::getBookByISBN($isbn);
        View::renderTemplate('Admin/Books/view.html.twig',
            [
                'book' => $book
            ]);
    }

    /**
     * Load the view of the edit function
     *
     * @return void
     */
    public function editAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $book = Book::getBookByISBN($isbn); // TODO: Fix the issue of the publication date format.
        if($book)
        {
            View::renderTemplate('Admin/Books/edit.html.twig', [
                'book' => $book,
            ]);
        }
    }

    /**
     * Update book information
     *
     * @return void
     */
    public function updateAction()
    {
        $data['isbn'] = filter_input(INPUT_POST, 'isbn', FILTER_SANITIZE_STRING);
        if($data['isbn'])
        {
            $data['title'] = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $data['date'] = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
            $data['edition'] = filter_input(INPUT_POST, 'edition', FILTER_SANITIZE_STRING);
            $data['authors'] = filter_input(INPUT_POST, 'authors', FILTER_SANITIZE_STRING);
            $updatedBook = new Book($data);
            if($updatedBook->updateBook())
            {
                $this->redirect('/admin/books/edit-success');
            }
            else
            {
                View::renderTemplate('Admin/Books/edit.html.twig',
                [
                    'errors' => $updatedBook->getErrors(),
                ]);
            }
        }
    }

    /**
     * Update success View
     *
     * @return void
     */
    public function editSuccessAction()
    {
        View::renderTemplate('Admin/Books/edit-success.html.twig');
    }

    /**
     * Delete the specified book
     *
     * @return void
     */
    public function destroyAction()
    {
        $isbn = filter_input(INPUT_POST, 'isbn', FILTER_SANITIZE_STRING);
        Book::deleteBook($isbn);
        $this->redirect('/admin/books/index');
    }

    /**
     * Load the view of delete confirmation
     *
     * @return void
     */
    public function deleteAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $book = Book::getBookByISBN($isbn);
        View::renderTemplate('Admin/Books/delete.html.twig',[
            'book' => $book,
        ]);
    }

    /**
     * Load the view of uploading files
     *
     * @return void
     */
    public function addFile()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        View::renderTemplate('Admin/Books/add-file.html.twig', [
            'isbn' => $isbn,
        ]);
    }

    /**
     * Upload the file for the specified book.
     *
     *
     * @return void
     */
    public function uploadFile()
    {
        if(Book::storeFile())
        {
            $this->redirect('/admin/books/file-success');
        }
        else
        {
            $this->redirect('/admin/books/file-failure');
        }
    }

    /**
     * File success view
     *
     * @return void
     */
    public function fileSuccessAction()
    {
        View::renderTemplate('Admin/Books/upload-success.html.twig');
    }

    /**
     * File failure view
     *
     * @return void
     */
    public function fileFailureAction()
    {
        View::renderTemplate('Admin/Books/upload-failure.html.twig');
    }

    /**
     * Load the view adding book chapter
     *
     * @return void
     */
    public function addChapter()
    {
        // TODO: Implement the method.
    }
}