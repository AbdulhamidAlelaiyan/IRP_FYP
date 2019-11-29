<?php


use PHPUnit\Framework\TestCase;

/**
 * Class BookTest
 *
 * Testing The book class
 */
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
        $validateMethod = new ReflectionMethod(\App\Models\Book::class, 'validate');
        $validateMethod->setAccessible(true);
        $this->book = new \App\Models\Book($this->correct_data);
        $validateMethod->invoke($this->book);
        $this->IncorrectBook = new \App\Models\Book($this->incorrect_data);
        $validateMethod->invoke($this->IncorrectBook);
    }

    public function testBookValidationReturnNoErrorIfDataIsCorrect()
    {
        $this->assertEmpty($this->book->getErrors());
    }

    public function testBookValidationReturnsErrorIfDataIsIncorrect()
    {
        $this->assertNull($this->book->getErrors());
    }

    public function testBookValidationReturnsErrorIfDataIsEmpty()
    {
        $book = new \App\Models\Book([]);
        $validateMethod = new ReflectionMethod(\App\Models\Book::class, 'validate');
        $validateMethod->setAccessible(true);
        $validateMethod->invoke($book);
        $this->assertNotNull($book->getErrors());
    }

    public function testBookValidationReturnsErrorIfTitleIsEmpty()
    {
        unset($this->correct_data['title']);
        $book = new \App\Models\Book($this->correct_data);
        $validateMethod = new ReflectionMethod(\App\Models\Book::class, 'validate');
        $validateMethod->setAccessible(true);
        $validateMethod->invoke($book);
        $this->assertNotEmpty($book->getErrors());
    }

    public function testBookValidationReturnsErrorIfISBNIsEmpty()
    {
        unset($this->correct_data['isbn']);
        $book = new \App\Models\Book($this->correct_data);
        $validationMethod = new ReflectionMethod(\App\Models\Book::class, 'validate');
        $validationMethod->setAccessible(true);
        $validationMethod->invoke($book);
        $this->assertNotEmpty($book->getErrors());
    }

    public function testBookValidationReturnsErrorIfAuthorsIsEmpty()
    {
        unset($this->correct_data['authors']);
        $book = new \App\Models\Book($this->correct_data);
        $validationMethod = new ReflectionMethod(\App\Models\Book::class, 'validate');
        $validationMethod->setAccessible(true);
        $validationMethod->invoke($book);
        $this->assertNotEmpty($book->getErrors());
    }

    public function testBookValidationReturnsErrorIfEditionIsEmpty()
    {
        unset($this->correct_data['edition']);
        $book = new \App\Models\Book($this->correct_data);
        $validationMethod = new ReflectionMethod(\App\Models\Book::class, 'validate');
        $validationMethod->setAccessible(true);
        $validationMethod->invoke($book);
        $this->assertNotEmpty($book->getErrors());
    }

    public function testBookValidationReturnsErrorIfDateIsEmpty()
    {
        unset($this->correct_data['date']);
        $book = new \App\Models\Book($this->correct_data);
        $validationMethod = new ReflectionMethod(\App\Models\Book::class, 'validate');
        $validationMethod->setAccessible(true);
        $validationMethod->invoke($book);
        $this->assertNotEmpty($book->getErrors());
    }
}