<?php


namespace App\Models;

use Core\View;
use \PDO;

class Book extends \Core\Model
{

    /**
     * errors array, contains validation errors
     * @var array
     */
    protected $errors;

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Constructor for the Book model
     *
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Validation of the data fed to the Book object, if any error it will be stored in $errors array
     *
     * @return void
     */
    protected function validate()
    {
        if(!isset($this->title) || empty($this->title))
        {
            $this->errors[] = 'Title must be provided.';
        }
        else
        {
            $this->title = filter_var($this->title, FILTER_SANITIZE_STRING);
        }

        if(!isset($this->authors) || empty($this->authors))
        {
            $this->errors[] = 'Author/s name/s must be provided.';
        }
        else
        {
            $this->authors = filter_var($this->authors, FILTER_SANITIZE_STRING);
        }

        if(!isset($this->isbn) || empty($this->isbn) || strlen($this->isbn) != 10 ||
            !filter_var($this->isbn, FILTER_VALIDATE_INT))
        // TODO: Check if the book is already added
        {
            $this->errors[] = 'ISBN must be provided and must be 10 numbers.';
        }

        if(!isset($this->edition) || empty($this->isbn))
        {
            $this->errors[] = 'Edition must be provided.';
        }

        if(!isset($this->date) || empty($this->date))
        {
            $this->errors[] = 'Publication Date must be provided.';
        }
        else
        {
            $this->date = strtotime($this->date);
            $this->date = date('Y-m-d');
        }
    }

    /**
     * Save the current instance to the database.
     *
     * @return boolean True if stored in the database, false otherwise
     */
    public function save()
    {
        $this->validate();

        if(is_null($this->errors))
        {
            $db = static::getDB();
            $sql = 'INSERT INTO books_information (title, isbn, edition, publication_date, authors) VALUES (:title, :isbn,
                                                                               :edition, :publication_date, :authors)';
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':isbn', $this->isbn, PDO::PARAM_STR);
            $stmt->bindValue(':edition', $this->edition, PDO::PARAM_STR);
            $stmt->bindValue(':publication_date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':authors', $this->authors, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
}