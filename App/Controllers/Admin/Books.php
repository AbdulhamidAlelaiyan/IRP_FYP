<?php

namespace App\Controllers\Admin;

use App\Config;
use App\Flash;
use App\Models\Book;
use \Core\View;
use phpDocumentor\Reflection\Types\Void_;

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
        $files = Book::getBookFiles($isbn);
        $chapters = Book::getBookChapters($isbn);
        View::renderTemplate('Admin/Books/view.html.twig',
            [
                'book' => $book,
                'files' => $files,
                'chapters' => $chapters,
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
        $files = Book::getBookFiles($isbn);
        if($book)
        {
            View::renderTemplate('Admin/Books/edit.html.twig', [
                'book' => $book,
                'files' => $files,
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
            $files = isset($_POST['files']) ? $_POST['files'] : null;
            $data['files'] = is_array($files) ? $_POST['files'] : null;
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
     * Load a view for adding book chapter
     *
     * @return void
     */
    public function newChapterAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $book = Book::getBookByISBN($isbn);
        View::renderTemplate('Admin/Books/new-chapter.html.twig',
            [
                'book' => $book,
            ]);
    }

    /**
     * Add the chapter to the book
     *
     * @return void
     */
    public function addChapterAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        if(isset($_POST['editordata']))
        {
            $editordata = $_POST['editordata'];
        }
        $chapter_number = filter_input(INPUT_POST, 'chapter-number', FILTER_SANITIZE_NUMBER_INT);
        $chapter_video = filter_input(INPUT_POST, 'chapter-video', FILTER_SANITIZE_URL);

        if(Book::addChapter($isbn, $title, $editordata, $chapter_number, $chapter_video))
        {
            View::renderTemplate('Admin/Books/chapter-success.html.twig');
        }
        else
        {
            View::renderTemplate('Admin/Books/chapter-failure.html.twig');
        }
    }

    /**
     * View the content of a chapter
     *
     * @return void
     */
    public function viewChapterAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $chapter = filter_input(INPUT_GET, 'chapter', FILTER_VALIDATE_INT);
        if($isbn && $chapter)
        {
            $chapter = Book::getBookChapter($isbn, $chapter);
            View::renderTemplate('Admin/Books/view-chapter.html.twig',
                [
                    'chapter' => $chapter,
                ]);
        }
        else
        {
            // TODO: Return ERROR page
        }
    }

    /**
     * Return a view for editing chapter
     *
     * @return void
     */
    public function editChapterAction()
    {

        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $chapter = filter_input(INPUT_GET, 'chapter', FILTER_VALIDATE_INT);
        if($isbn && $chapter)
        {
            $book = Book::getBookByISBN($isbn);
            $chapter = Book::getBookChapter($isbn, $chapter);
            View::renderTemplate('Admin/Books/edit-chapter.html.twig',
                [
                    'book' => $book,
                    'chapter' => $chapter,
                ]);
        }
        else
        {
            // TODO: Return ERROR page
        }
    }

    /**
     * Update an existing chapter
     *
     * @return void
     */
    public function updateChapterAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        if(isset($_POST['editordata']))
        {
            $editordata = $_POST['editordata'];
        }
        $chapter_number = filter_input(INPUT_POST, 'chapter-number', FILTER_SANITIZE_NUMBER_INT);
        $chapter_video = filter_input(INPUT_POST, 'chapter-video', FILTER_SANITIZE_URL);

        if(Book::updateChapter($isbn, $title, $editordata, $chapter_number, $chapter_video))
        {
            View::renderTemplate('Admin/Books/update-chapter-success.html.twig');
        }
        else
        {
            View::renderTemplate('Admin/Books/update-chapter-failure.html.twig');
        }
    }

    /**
     * Load view of delete confirmation
     *
     * @return void
     */
    public function deleteChapterAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $chapter = filter_input(INPUT_GET, 'chapter', FILTER_VALIDATE_INT);
        if($chapter && $isbn)
        {
            $chapter = Book::getBookChapter($isbn, $chapter);
            $book = Book::getBookByISBN($isbn);
            View::renderTemplate('Admin/Books/delete-confirm.html.twig',
                [
                    'book' => $book,
                    'chapter' => $chapter,
                ]);
        }
    }

    /**
     * Delete chapter
     *
     * @return void
     */
    public function destroyChapterAction()
    {
        $isbn = filter_var($this->route_params['isbn'], FILTER_SANITIZE_STRING);
        $chapter = filter_input(INPUT_POST, 'chapter', FILTER_VALIDATE_INT);
        if($chapter && $isbn)
        {
            if(Book::deleteChapter($isbn, $chapter))
            {
                View::renderTemplate('Admin/Books/delete-chapter-success.html.twig');
            }
            else
            {
                View::renderTemplate('Admin/Books/delete-chapter-failure.html.twig');
            }
        }
    }
}