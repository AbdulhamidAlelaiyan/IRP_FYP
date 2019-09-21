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
        $sql = 'SELECT * FROM replies WHERE post_id = :post_id ORDER BY created_at DESC';
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
            }
            return $replies;
        }
    }
}