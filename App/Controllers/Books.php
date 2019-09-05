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
		$books = Book::getBooks();
		View::renderTemplate('Books/index.html.twig', [
			'links' => $books['links'],
			'books' => $books['books']
		]);
	}
}