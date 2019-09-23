<?php

namespace App\Models;

use App\Auth;
use PDO;

class Post extends \Core\Model
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
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Constructor for the Post model
     *
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Return all posts of a particular discussion board
     *
     * @param int $isbn
     *
     * @return mixed Array of posts and paginator, False otherwise
     */
    public static function getPosts($isbn)
    {
        $db = static::getDB();
        $paginator = new \Zebra_Pagination();
        $paginator->records(static::getPostsCount($isbn));
        $paginator->records_per_page(10);
        $page = $paginator->get_page();
        $sqlPage = ($page - 1) * 10;
        $stmt = $db->prepare('SELECT * FROM posts WHERE isbn = :isbn ORDER BY created_at DESC LIMIT ' . $sqlPage . ', 10');
        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if($stmt->execute())
        {
            return [$stmt->fetchAll(), $paginator->render(true)];
        }
        else
        {
            return false;
        }
    }

    /**
     * Return number of posts for a particular isbn
     *
     * @param int $isbn
     *
     * @return int number of posts for a particular isbn
     */
    protected static function getPostsCount($isbn)
    {
        $db = static::getDB();
        $sql = 'SELECT count(*) AS count FROM posts WHERE isbn = :isbn';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isbn', $isbn, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Store the post into the database
     *
     * @return boolean True if stored, False otherwise
     */
    public function save()
    {
        if($this->validate())
        {
            $db = static::getDB();
            $stmt = $db->prepare('INSERT INTO posts (isbn, title, body, user_id) VALUES(:isbn, :title, :body, :user_id)');
            $stmt->bindValue(':isbn', $this->isbn, PDO::PARAM_STR);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        else
        {
            return false;
        }
    }

    /**
     * Validate post values
     *
     * @return boolean True if values are ok, False otherwise
     */
    public function validate()
    {
        $this->isbn = filter_var($this->isbn, FILTER_SANITIZE_NUMBER_INT);
        $this->user = Auth::getUser();
        $this->user_id = $this->user->id;
        $this->title = filter_var( $this->title, FILTER_SANITIZE_STRING);
        $config = \HTMLPurifier_HTML5Config::createDefault();
        $purifier = new \HTMLPurifier($config);
        $this->body = $purifier->purify($_POST['editordata']);
        if($this->isbn && $this->user_id && $this->title
            && $this->body)
        {
            if(!(strlen($this->title) > 0))
            {
                $this->errors[] = 'Title need to be filled';
            }
            elseif(!(strlen($this->body) > 0))
            {
                $this->errors[] = 'Body need to be filled';
            }
            else
            {
                return true;
            }
        }
        else
        {
            if(!$this->body)
            {
                $this->errors[] = 'Body need to be filled';
            }
            elseif(!$this->title)
            {
                $this->errors[] = 'Title need to be filled';
            }
            return false;
        }
    }

    /**
     * Find Post By ID
     *
     * @param int $id
     *
     * @return mixed Post if exist, False otherwise
     */
    public static function getPostByID($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM posts WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if($stmt->execute())
        {
            $post = $stmt->fetch();
            $post->user = User::findByID($post->user_id);
            $post->userType = $post->user->type;
            $post->username = $post->user->name;
            return $post;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return all posts in the database
     *
     * @return array all posts in the database
     */
    public static function getAllPosts()
    {
        $db = static::getDB();
        $sql = 'SELECT * FROM posts';
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll();
    }
}