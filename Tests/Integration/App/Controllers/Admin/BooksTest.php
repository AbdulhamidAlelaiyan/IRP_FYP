<?php

namespace Tests\Integration\App\Controllers\Admin;

use App\Controllers\Admin\Books;
use App\Models\Book;
use Core\Model;
use PHPUnit\Framework\TestCase;


/**
 * @runTestsInSeparateProcesses
 */
class BooksTest extends TestCase
{

//    public function testViewChapterAction()
//    {
//
//    }
//
//    public function testSearchAction()
//    {
//
//    }
//
//
//    public function testDeleteChapterAction()
//    {
//
//    }
//
//    public function testFileFailureAction()
//    {
//
//    }
//
//    public function testUpdateAction()
//    {
//
//    }
//
//    public function testUploadFile()
//    {
//
//    }
//
//    public function testAddChapterAction()
//    {
//
//    }
//
//    public function testHistoryChapter()
//    {
//
//    }
//
//    public function testAddFile()
//    {
//
//    }
//
//    public function testNewChapterAction()
//    {
//
//    }

    public function testCreateAction()
    {
        Book::deleteBook('1111111111');
        $_POST = [
            'isbn' =>  '1111111111',
            'title' => 'Book title',
            'authors' => 'Ahmed',
            'date' => '1999-12-12',
            'edition' => '5th edition',
            'description' => 'a book about something',
        ];
        $booksController = new Books([]);
        $booksController->createAction();
        // Using Reflection to access protected method
        $protectedMethod = new \ReflectionMethod(Model::class, 'getDB');
        $protectedMethod->setAccessible(true);
        $db = $protectedMethod->invoke(null);
        $sql = 'SELECT isbn FROM books_information WHERE isbn = \'1111111111\'';
        $db->setFetchMode(\PDO::FETCH_CLASS, Book::class);
        $result = $db->query($sql);
        $this->assertEquals($result->isbn, '1111111111');
    }

//    public function testViewAction()
//    {
//
//    }
//
//    public function testUpdateChapterAction()
//    {
//
//    }
//
//    public function testDestroyCover()
//    {
//
//    }
//
//    public function testDestroyAction()
//    {
//
//    }
//
//    public function testAddCoverAction()
//    {
//
//    }
//
//    public function testUploadBookCover()
//    {
//
//    }
//
//    public function testDeleteAction()
//    {
//
//    }
//
//    public function testEditChapterAction()
//    {
//
//    }
//
//    public function testDeleteCover()
//    {
//
//    }
//
//    public function testDestroyChapterAction()
//    {
//
//    }
//
//    public function testFileSuccessAction()
//    {
//
//    }
}
