<?php

namespace Tests\Integrtion\App\Controllers;

use App\Models\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    protected $book;
    protected $IncorrectBook;

    protected $correct_data;
    protected $incorrect_data;

    public function setUp()
    {
        parent::setUp();
        $this->incorrect_data = [
            'isbn' =>  '11111',
            'title' => 'Book title',
            'authors' => 'Ahmed',
            'edition' => '1999-12-12',
            'description' => 'a book about something',
        ];
        $this->correct_data = [
            'isbn' =>  '1111111111',
            'title' => 'Book title',
            'authors' => 'Ahmed',
            'date' => '1999-12-12',
            'edition' => '5th edition',
            'description' => 'a book about something',
        ];
        $validateMethod = new \ReflectionMethod(Book::class, 'validate');
        $validateMethod->setAccessible(true);
        $this->book = new Book($this->correct_data);
        $validateMethod->invoke($this->book);
        $this->IncorrectBook = new Book($this->incorrect_data);
        $validateMethod->invoke($this->IncorrectBook);
    }

    public function testBookWillBeSavedIfDataIsCorrect()
    {
        Book::deleteBook($this->correct_data['isbn']);
        $this->assertTrue($this->book->save());
    }

    public function testBookWillNotBeSavedIfDataIsIncorrrect()
    {
        $this->assertFalse($this->IncorrectBook->save());
    }

    public function testBookWillNotBeSavedIfISBNIsIncorrect()
    {
        $newBook = new Book($this->incorrect_data);
        $this->assertFalse($newBook->save());
    }

    public function testBookWillNotBeSavedIfTitleIsEmpty()
    {
        $this->correct_data['title'] = '';
        $newBook = new Book($this->incorrect_data);
        $this->assertFalse($newBook->save());
    }

    public function testBookWillNotBeSavedIfAuthorsAreEmpty()
    {
        $this->correct_data['authors'] = '';
        $newBook = new Book($this->incorrect_data);
        $this->assertFalse($newBook->save());
    }

    public function testBookWillNotBeSavedIfEditionIsEmpty()
    {
        $this->correct_data['authors'] = '';
        $newBook = new Book($this->incorrect_data);
        $this->assertFalse($newBook->save());
    }

    public function testBookWillNotBeSavedIfDescriptionIsEmpty()
    {
        $this->correct_data['description'] = '';
        $newBook = new Book($this->incorrect_data);
        $this->assertFalse($newBook->save());
    }

    public function testGetBookByISBNWillReturnAnObjectOfTypeBook()
    {
        $this->assertEquals(get_class(Book::getBookByISBN('1111111111')), Book::class);
    }
}