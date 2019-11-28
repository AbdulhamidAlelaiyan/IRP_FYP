<?php

namespace App\Models;

use App\Auth;
use PDO;

class Message extends \Core\Model
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
     * Constructor for the Message model
     *
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Validate data in the message
     *
     * @return boolean True if data is ok, False otherwise
     */
    public function validate()
    {
        $this->errors = null;
        if(!($this->from = filter_var($this->from, FILTER_VALIDATE_INT)))
        {
            $this->errors[] = 'From must be specified and must be a string!';
        }
        if(!($this->to = filter_var($this->to, FILTER_VALIDATE_INT)))
        {
            $this->errors[] = 'To must be specified and must be a valid email!';
        }
        if(!($this->title = filter_var($this->title, FILTER_SANITIZE_STRING)))
        {
            $this->errors[] = 'Title must be specified and must be a string!';
        }
        if(!($this->body = filter_var($this->body, FILTER_SANITIZE_STRING)))
        {
            $this->errors[] = 'Body must be specified and must be a string';
        }

        return is_null($this->errors);
    }

    /**
     * Store the current instance of message to the messages table in the database
     *
     * @return boolean True if stored, False otherwise
     */
    public function save()
    {
        if(!$this->validate())
        {
            return false;
        }

        $db = static::getDB();
        $sql = 'INSERT INTO messages (from_user, to_user, title, body) VALUES (:from, :to, :title, :body)';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':from', $this->from, PDO::PARAM_INT);
        $stmt->bindValue(':to', $this->to, PDO::PARAM_INT);
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':body', $this->body, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Get all messages sent to a user
     *
     * @param int $user_id
     *
     * @return mixed array of messages if user exist, False otherwise
     */
    public static function getInboxMessages($user_id)
    {
        $messages = null;
        if($user = User::findByID($user_id))
        {
            $db = static::getDB();
            $sql = 'SELECT * FROM messages WHERE to_user = :user_id';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $user->id, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            if(!$stmt->execute())
            {
                return false;
            }
            $messages = $stmt->fetchAll();
            foreach($messages as $message)
            {
                $message_user = User::findByID($message->from_user);
                $message->username = $message_user->name;
            }
        }
        return $messages;
    }

    /**
     * Get message by ID
     *
     * @param int $message_id
     *
     * @return mixed Message if found, False otherwise
     */
    public static function getMessageByID($message_id, $adminFlag = false)
    {
        $db = static::getDB();
        $sql = 'SELECT * FROM messages WHERE id = :id LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $message_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if(!$stmt->execute())
        {
            return false;
        }
        $message = $stmt->fetch();
        $user = Auth::getUser();
        if($user->id == $message->to_user || $adminFlag)
        {
            $from_user = User::findByID($message->from_user);
            $to_user = User::findByID($message->to_user);
            $message->from_username = $from_user->name;
            $message->to_username = $to_user->name;
            return $message;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get all the messages in the messages table
     *
     * @return mixed array of all the messages in messages and messages_replies table, False otherwise
     */
    public static function getAllMessages()
    {
        $db = static::getDB();
        $numberOfMessages = 10;
        $paginator = new \Zebra_Pagination();
        $page = $paginator->get_page();
        $paginator->records(static::getCountOfMessages());
        $paginator->records_per_page($numberOfMessages);
        $sqlPage = ($page - 1) * $numberOfMessages;
        $sql = "SELECT * FROM messages LIMIT $sqlPage, $numberOfMessages";
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if(!$stmt->execute())
        {
            return false;
        }
        $messages = $stmt->fetchAll();
        foreach($messages as $message)
        {
            $from_user = User::findByID($message->from_user);
            $to_user = User::findByID($message->to_user);
            $message->from_username = $from_user->name;
            $message->to_username = $to_user->name;
        }
        return [$messages, $paginator->render(true)];
    }

    /**
     * Return count of messages
     *
     * @return int number of messages
     */
    public static function getCountOfMessages()
    {
        $db = static::getDB();
        $sql = "SELECT count(*) AS count FROM messages";
        $stmt = $db->prepare($sql);
        if(!$stmt->execute())
        {
            return false;
        }
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Delete the current instance of the message from the messages table in the database
     *
     * @return boolean True if message deleted, False otherwise
     */
    public function delete()
    {
        $db = static::getDB();
        $sql = 'DELETE FROM messages WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Add a reply to the message
     *
     * @param int $message_id
     * @param string $reply_body
     *
     * @return boolean True if added, False otherwise
     */
    public function addReply($message_id, $reply_body)
    {
        $db = static::getDB();
        $sql = 'INSERT INTO messages_replies (message_id, user_id, textbody) 
            VALUES (:message_id, :user_id, :textbody)';
        $user = Auth::getUser();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':message_id', $message_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user->id, PDO::PARAM_INT);
        $stmt->bindValue(':textbody', $reply_body, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Return all replies of message
     *
     * @return mixed array of replies, False otherwise
     */
    public function getAllReplies()
    {
        $db = static::getDB();
        $sql = 'SELECT * FROM messages_replies WHERE message_id = :message_id ORDER BY created_at DESC';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':message_id', $this->id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if($stmt->execute())
        {
            $replies = $stmt->fetchAll();
            foreach($replies as $reply)
            {
                $user = User::findByID($reply->user_id);
                $reply->username = $user->name;
            }
            return $replies;
        }
        else
        {
            false;
        }
    }
}