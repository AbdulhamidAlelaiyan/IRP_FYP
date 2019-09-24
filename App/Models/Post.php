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
     * Return all posts in the database and the HTML pagination markup
     *
     * @return array all posts in the database and HTML pagination markup
     */
    public static function getAllPosts()
    {
        $db = static::getDB();
        $paginator = new \Zebra_Pagination();
        $paginator->records(static::getAllPostsCount());
        $paginator->records_per_page(10);
        $page = $paginator->get_page();
        $sqlPage = ($page - 1) * 10;
        $sql = 'SELECT * FROM posts LIMIT ' . $sqlPage . ', 10';
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return [$stmt->fetchAll(), $paginator->render(true)];
    }


    /**
     * Get the count of all the posts in the database
     *
     * @return int The count of records in the posts table
     */
    public static function getAllPostsCount()
    {
        $db = static::getDB();
        $sql = 'SELECT count(*) AS count FROM posts';
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $count = $stmt->fetch();
        return $count['count'];
    }

    /**
     * Upvote posts based on its id
     *
     * @param int $post_id id of the post that going to be up voted
     *
     * @return boolean True if upvoted, False otherwise
     */
    public static function upvotePostByID($post_id)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM posts_points WHERE post_id = :post_id AND user_id = :user_id";
        $user = Auth::getUser();
        $user_id = $user->id;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result['point'] == 1)
        {
            $sql = 'DELETE FROM posts_points WHERE post_id = :post_id AND user_id = :user_id AND point = 1';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            if($stmt->execute()) return 'removed';
            else return false;
        }
        else
        {
            $sql = 'DELETE FROM posts_points WHERE post_id = :post_id AND user_id = :user_id AND point = 0';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $sql = 'DELETE FROM posts_points WHERE post_id = :post_id AND user_id = :user_id AND point = 1';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
        }
        $sql = 'INSERT INTO posts_points (user_id, post_id, point) VALUES (:user_id, :post_id, 1)';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Downvote posts based on its id
     *
     * @param int $post_id id of the post that going to be down voted
     *
     * @return boolean True if downvoted, False otherwise
     */
    public static function downvotePostByID($post_id)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM posts_points WHERE post_id = :post_id AND user_id = :user_id";
        $user = Auth::getUser();
        $user_id = $user->id;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result['point'] === '0')
        {
            $sql = 'DELETE FROM posts_points WHERE post_id = :post_id AND user_id = :user_id AND point = 0';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            if($stmt->execute()) return 'removed';
            else return false;
        }
        else
        {
            $sql = 'DELETE FROM posts_points WHERE post_id = :post_id AND user_id = :user_id AND point = 1';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $sql = 'DELETE FROM posts_points WHERE post_id = :post_id AND user_id = :user_id AND point = 0';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
        }
        $sql = 'INSERT INTO posts_points (user_id, post_id, point) VALUES (:user_id, :post_id, 0)';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Calculate points of a post
     *
     * @param int post_id ID of the post that will be calculated
     *
     * @return array Points of the post
     */
    public static function calculatePoints($post_id)
    {
        $db = static::getDB();
        $sql = 'SELECT count(*) AS count FROM posts_points WHERE post_id = :post_id AND point = 1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $count_upvotes = $result['count'];
        $sql = 'SELECT count(*) AS count FROM posts_points WHERE post_id = :post_id AND point = 0';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $count_downvotes = $result['count'];
        return [$count_upvotes, $count_downvotes];
    }

    /**
     * Get post by reply id
     *
     * @param int $reply_id
     *
     * @return mixed Post if found, False otherwise
     */
    public static function getPostByReplyID($reply_id)
    {
        $db = static::getDB();
        $sql = 'SELECT * FROM replies WHERE id = :reply_id LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if(!$stmt->execute()) return false;
        $reply = $stmt->fetch();
        $post_id = $reply->post_id;
        return static::getPostByID($post_id);
    }

    /**
     * Update existing post
     *
     * @return boolean True if updated, False otherwise
     */
    public function update()
    {
        $db = static::getDB();
        $sql = 'UPDATE posts SET title = :title, body = :body WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}