<?php


namespace App\Models;

use Core\View;
use \PDO;

class Book extends \Core\Model
{
	/**
	 * Get all books for a single page
	 *
	 * @param int $book_per_page
	 * @return array
	 */
	public static function getBooks(int $book_per_page = 3): array
	{
		// get the DB connection
		$db = static::getDB();
		// get the Zebra paginator
		$pg = new \Zebra_Pagination();

		$total_books_sql = "SELECT count(*) AS count FROM books";
		$current_page_books_sql = "SELECT title, author FROM books 
LIMIT " . ($pg->get_page() - 1) * $book_per_page . ", $book_per_page";

		$stmt = $db->query($total_books_sql);
		$total_books = $stmt->fetch()['count'];

		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		$current_page_books = $db->query($current_page_books_sql);

		$pg->records_per_page($book_per_page);
		$pg->records($total_books);

		return ['links' => $pg->render(true), 'books' => $current_page_books];
	}
}