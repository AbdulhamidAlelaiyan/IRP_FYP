<?php

namespace App\Models;

use App\Auth;
use PDO;

class Report extends \Core\Model
{
    /**
     * Add a report for the post
     *
     * @param string $text
     *
     * @return boolean True if stored, False otherwise
     */
    public static function addReport($text, $post_id)
    {
        $db = static::getDB();
        $sql = 'INSERT INTO posts_reports (post_id, user_id, text) VALUES (:post_id, :user_id, :text)';
        $user = Auth::getUser();
        $user_id = $user->id;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $text, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Add a report for the reply
     *
     * @param string $text
     *
     * @return boolean True if stored, False otherwise
     */
    public static function addReplyReport($text, $post_id)
    {
        $db = static::getDB();
        $sql = 'INSERT INTO replies_reports (reply_id, user_id, text) VALUES (:reply_id, :user_id, :text)';
        $user = Auth::getUser();
        $user_id = $user->id;
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':reply_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $text, PDO::PARAM_STR);
        return $stmt->execute();
    }
}