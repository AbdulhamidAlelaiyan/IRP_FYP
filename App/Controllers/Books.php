<?php


namespace App\Controllers;

use App\Models\Book;
use Core\View;

class Books extends \Core\Controller
{

	/**
	 * Show all the books in a paginating form
	 *
	 * @return void
	 */
	public function indexAction()
	{
        $booksWithPagination = Book::getBooks();
        $books = $booksWithPagination[0];
        $pagination = $booksWithPagination[1];
        View::renderTemplate('Books/index.html.twig', [
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
        View::renderTemplate('Books/index.html.twig', [
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
        View::renderTemplate('Books/view.html.twig',
            [
                'book' => $book,
                'files' => $files,
                'chapters' => $chapters,
            ]);
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
            View::renderTemplate('Books/view-chapter.html.twig',
                [
                    'chapter' => $chapter,
                ]);
        }
        else
        {
            // TODO: Return ERROR page
        }
    }
}