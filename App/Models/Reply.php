<?php


namespace App\Models;

use App\Auth;
use PDO;
use Core\Model;

class Reply extends Model
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
     * Constructor for the Reply model
     *
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }
    }


    /**
     * Validate data in the model
     *
     * @return boolean True if validated ok, False otherwise
     */
    public function validate()
    {
        $this->errors = [];
        if(!(isset($this->editordata)) || !(strlen($this->editordata) > 0))
        {
            $this->errors[] = 'Text need to be filled';
            return false;
        }
        else
        {
            $config = \HTMLPurifier_HTML5Config::createDefault();
            $purifier = new \HTMLPurifier($config);
            $this->editordata = $purifier->purify($this->editordata);
        }
        if(!isset($this->post_id))
        {
            $this->errors[] = 'you must reply to post!';
            return false;
        }
        return true;
    }

    /**
     * Store the reply into the database
     *
     * @return boolean True if stored, False otherwise
     */
    public function save()
    {
        if($this->validate())
        {
            $this->user_id = Auth::getUser();
            $this->user_id = $this->user_id->id;
            $db = static::getDB();
            $sql = 'INSERT INTO replies (text, user_id, post_id) VALUES(:text, :user_id, :post_id)';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':text', $this->editordata, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':post_id', $this->post_id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        else
        {
            return false;
        }
    }

    /**
     * Get all replies for a specific post
     *
     * @param int $id
     *
     * @return mixed Array of replies, False otherwise
     */
    public static function getRepliesByPostID($id)
    {
        $db = static::getDB();
        $paginator = new \Zebra_Pagination();
        $paginator->records_per_page(10);
        $paginator->records(static::getRepliesCount($id));
        $page = $paginator->get_page();
        $sqlPage = ($page - 1) * 10;
        $sql = 'SELECT * FROM replies WHERE post_id = :post_id ORDER BY created_at DESC LIMIT ' . $sqlPage . ', 10';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if(!$stmt->execute())
        {
            return false;
        }
        else
        {
            $replies = $stmt->fetchAll();
            foreach($replies as $reply)
            {
                $user = User::findByID($reply->user_id);
                $reply->username = $user->name;
                $reply->userType = $user->type;
                $points = static::calculatePoints($reply->id);
                $reply->up_points = $points[0];
                $reply->down_points = $points[1];
            }
            return $replies;
        }
    }

    /**
     * Return count of replies for a particular post
     *
     * @param int $id
     *
     * @return int
     */
    protected static function getRepliesCount($id)
    {
        $db = static::getDB();
        $sql = 'SELECT count(*) AS count FROM replies WHERE post_id = :post_id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $result = $stmt->fetch();
        $result = $result->count;
        return $result;
    }

    /**
     * Upvote reply based on its id
     *
     * @param int $reply_id id of the reply that going to be up voted
     *
     * @return boolean True if upvoted, False otherwise
     */
    public static function upvoteReplyByID($reply_id)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM replies_points WHERE reply_id = :reply_id AND user_id = :user_id";
        $user = Auth::getUser();
        $user_id = $user->id;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result['point'] == 1)
        {
            $sql = 'DELETE FROM replies_points WHERE reply_id = :reply_id AND user_id = :user_id AND point = 1';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            if($stmt->execute()) return 'removed';
            else return false;
        }
        else
        {
            $sql = 'DELETE FROM replies_points WHERE reply_id = :reply_id AND user_id = :user_id AND point = 0';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $sql = 'DELETE FROM replies_points WHERE reply_id = :reply_id AND user_id = :user_id AND point = 1';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
        }
        $sql = 'INSERT INTO replies_points (user_id, reply_id, point) VALUES (:user_id, :reply_id, 1)';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Downvote reply based on its id
     *
     * @param int $reply_id id of the reply that going to be down voted
     *
     * @return boolean True if downvoted, False otherwise
     */
    public static function downvoteReplyByID($reply_id)
    {
        $db = static::getDB();
        $sql = "SELECT * FROM replies_points WHERE reply_id = :reply_id AND user_id = :user_id";
        $user = Auth::getUser();
        $user_id = $user->id;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result['point'] === '0')
        {
            $sql = 'DELETE FROM replies_points WHERE reply_id = :reply_id AND user_id = :user_id AND point = 0';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            if($stmt->execute()) return 'removed';
            else return false;
        }
        else
        {
            $sql = 'DELETE FROM replies_points WHERE reply_id = :reply_id AND user_id = :user_id AND point = 1';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $sql = 'DELETE FROM replies_points WHERE reply_id = :reply_id AND user_id = :user_id AND point = 0';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
        }
        $sql = 'INSERT INTO replies_points (user_id, reply_id, point) VALUES (:user_id, :reply_id, 0)';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }


    /**
     * Calculate points of a reply
     *
     * @param int reply_id ID of the post that will be calculated
     *
     * @return array Points of the post
     */
    public static function calculatePoints($reply_id)
    {
        $db = static::getDB();
        $sql = 'SELECT count(*) AS count FROM replies_points WHERE reply_id = :reply_id AND point = 1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $count_upvotes = $result['count'];
        $sql = 'SELECT count(*) AS count FROM replies_points WHERE reply_id = :reply_id AND point = 0';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $count_downvotes = $result['count'];
        return [$count_upvotes, $count_downvotes];
    }

    /**
     * Delete reply based on its ID
     *
     * @param int $reply_id
     *
     * @return boolean True if reply deleted, False otherwise
     */
    public static function deleteReply($reply_id)
    {
        $db = static::getDB();
        $sql = 'DELETE FROM replies WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $reply_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Get reply by ID
     *
     * @param int $reply_id
     *
     * @return mixed Reply if found, False otherwise
     */
    public static function getReplyByID($reply_id)
    {
        $db = static::getDB();
        $sql = 'SELECT * FROM replies WHERE id = :id LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $reply_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if(!$stmt->execute()) return false;
        return $stmt->fetch();
    }
}