<?php


namespace App\Models;

use App\Config;
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
     * Return errors of validation.
     *
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
     * @return boolean True if everything is ok
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
            $this->date = date('Y-m-d', $this->date);
        }

        return true;
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

    /**
     * Return the books with pagination markup
     *
     * @param int $booksPerPage number of books for each page
     *
     * @return array
     */
    public static function getBooks($booksPerPage = 10)
    {
        $numberOfBooks = static::getNumberOfBooks();
        $paginator = new \Zebra_Pagination();
        $page = $paginator->get_page();
        $paginator->records($numberOfBooks);
        $paginator->records_per_page($booksPerPage);
        $sqlPage = ($page - 1) * $booksPerPage;
        $sql = "SELECT * FROM books_information LIMIT $sqlPage, $booksPerPage";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $result = $stmt->fetchAll();

        return [$result, $paginator->render(true)];
    }

    /**
     * Get the number of books stored in the database
     *
     * @return int Number of books
     */
    public static function getNumberOfBooks()
    {
        $db = static::getDB();
        $sql = 'SELECT count(*) AS count FROM books_information';
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result->count;
    }

    /**
     * Get the book by the ISBN number
     *
     * @param string isbn
     *
     * @return mixed the book with the isbn, false otherwise
     */
    public static function getBookByISBN($isbn)
    {
        $db = static::getDB();
        $sql = 'SELECT * FROM books_information WHERE isbn = :isbn';
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
        if(!$stmt->execute()) return false;
        return $stmt->fetch();
    }

    /**
     * Get book/s by title
     *
     * @param string book
     *
     * @return mixed Book/s that have same or similar title, false otherwise
     */
    public static function getBooksByTitle($title)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM books_information WHERE title LIKE '%$title%'";
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        if(!$stmt->execute()) return false;
        return $stmt->fetchAll();
    }

    /**
     * Update book information
     *
     * @return boolean True if success, false otherwise
     */
    public function updateBook()
    {
        if(!$this->validate()) return false;
        if(empty($this->erorrs))
        {
            $db = static::getDB();
            $sql = 'UPDATE books_information SET title = :title, authors = :authors, publication_date = :date,
edition = :edition WHERE isbn = :isbn';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':authors', $this->authors, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':edition', $this->edition, PDO::PARAM_STR);
            $stmt->bindValue(':isbn', $this->isbn, PDO::PARAM_STR);
            if($stmt->execute()) return (isset($this->files) ?  static::deleteFiles($this->files, $this->isbn) : true);
        }
    }

    /**
     * Delete book files
     *
     * @param array $files
     *
     * @param $isbn
     * @return boolean True if successfully deleted files, false otherwise
     */
    protected static function deleteFiles($files, $isbn)
    {
        if($files)
        {
            foreach($files as $file)
            {
                unlink(Config::APP_DIRECTORY . 'public/resources/' . $isbn . '/' . $file);
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Delete book
     *
     * @param int isbn
     *
     * @return boolean True if success, False otherwise
     */
    public static function deleteBook($isbn)
    {
        $db = static::getDB();
        $sql = 'DELETE FROM books_information WHERE isbn = :isbn';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
        if($stmt->execute()) return static::deleteFiles(static::getBookFiles($isbn), $isbn);
    }

    /**
     * Add Book files to the resources directory
     *
     * @return boolean True if Successfully stored the file, false otherwise
     */
    public static function storeFile()
    {
        if(!isset($_POST['isbn']))
        {
            return false;
        }
        if(isset($_FILES['upload']))
        {
            $isbn = filter_input(INPUT_POST, 'isbn', FILTER_SANITIZE_STRING);
            $allowed_mime_types =
                [
                    'application/zip',
                    'application/pdf',
                    'application/msword',
                    'text/plain',
                    'image/jpeg',
                    'image/tiff',
                    'image/webp',
                ];
            if(in_array($_FILES['upload']['type'], $allowed_mime_types))
            {
                if(!file_exists(Config::APP_DIRECTORY . 'public/resources/' . $isbn  . '/'))
                {
                    mkdir(Config::APP_DIRECTORY . 'public/resources/' . $isbn  . '/');
                }
                if(move_uploaded_file($_FILES['upload']['tmp_name'],
                    Config::APP_DIRECTORY . 'public/resources/' . $isbn  . '/' . $_FILES['upload']['name']))
                {
                    return true;
                }
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Get all files of a book
     *
     * @param string $isbn
     *
     * @return mixed
     */
    public static function getBookFiles($isbn)
    {
        if(file_exists(Config::APP_DIRECTORY . 'public/resources/' . $isbn . '/'))
        {
            $files = array_diff(scandir(Config::APP_DIRECTORY . 'public/resources/' . $isbn . '/'),
                ['.', '..']);
            if(count($files) == 0)
            {
                $files =  false;
            }
        }
        else
        {
            $files = false;
        }
        return $files;
    }

    /**
     * Add chapters to books
     *
     * @param $isbn
     * @param $title
     * @param $editordata
     * @param $chapter
     * @param null $chapter_video
     *
     * @return boolean True if successfully added, false otherwise
     */
    public static function addChapter($isbn, $title, $editordata, $chapter, $chapter_video = null)
    {
        $db = static::getDB();
        $sql_check_duplication = 'SELECT * FROM books_content WHERE isbn = :isbn AND chapter = :chapter';
        $stmt = $db->prepare($sql_check_duplication);
        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->bindValue(':chapter', $chapter, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        if(!$result)
        {
            $sql = "INSERT INTO books_content (isbn, chapter, title, content, video_id) 
                    VALUES (:isbn, :chapter, :title, :content, :chapter_video)";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
            $stmt->bindValue(':chapter', $chapter, PDO::PARAM_INT);
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(":content", $editordata, PDO::PARAM_STR);
            if($chapter_video)
            {
                $stmt->bindValue(":chapter_video", $chapter_video, PDO::PARAM_STR);
            }
            else
            {
                $stmt->bindValue(":chapter_video", $chapter_video, PDO::PARAM_NULL);
            }
            return $stmt->execute();
        }
        else
        {
            return false;
        }
    }

    /**
     * Return books chapters
     *
     * @param int $isbn
     *
     * @return array chapters of the book
     */
    public static function getBookChapters($isbn)
    {
        $db = static::getDB();
        $sql =  'SELECT * FROM books_content WHERE isbn = :isbn ORDER BY chapter';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Return Book Chapter
     *
     * @param string $isbn
     * @param int $chapter
     *
     * @return mixed Book Chapter if found, false otherwise
     */
    public static function getBookChapter($isbn, $chapter)
    {
        $db = static::getDB();
        $sql = 'SELECT * FROM books_content WHERE isbn = :isbn and chapter = :chapter LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->bindValue(':chapter', $chapter, PDO::PARAM_INT);
        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
        if(!$stmt->execute()) return false;
        return $stmt->fetch();
    }

    /**
     * Update chapter content
     *
     * @param string $isbn
     * @param string $title
     * @param string $editordata
     * @param int $chapter_number
     * @param string $chapter_video
     *
     * @return mixed True if update was successful, false otherwise
     */
    public static function updateChapter($isbn, $title, $editordata, $chapter_number, $chapter_video)
    {
        $db = static::getDB();
        $sql = 'UPDATE books_content SET title = :title, content = :content, chapter = :chapter, video_id = :video_id
                WHERE isbn = :isbn and chapter = :chapter';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':content', $editordata, PDO::PARAM_STR);
        $stmt->bindValue(':chapter', $chapter_number, PDO::PARAM_INT);
        $stmt->bindValue(':video_id', $chapter_video, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Delete chapter from a book
     *
     * @return boolean True if successfully deleted, false otherwise
     */
    public static function deleteChapter($isbn, $chapter)
    {
        $db = static::getDB();
        $sql = 'DELETE FROM books_content WHERE isbn = :isbn AND chapter = :chapter';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->bindValue(':chapter', $chapter, PDO::PARAM_INT);
        return $stmt->execute();
    }
}