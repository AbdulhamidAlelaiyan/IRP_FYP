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

    /**
     * Get all the reports of posts in the database
     *
     * @return array of all the reports
     */
    public static function getAllPostReports()
    {
        $numberOfPostsReports = static::getCountOfPostsReports();
        $paginator = new \Zebra_Pagination();
        $page = $paginator->get_page();
        $paginator->records($numberOfPostsReports);
        $paginator->records_per_page(10);
        $sqlPage = ($page - 1) * 10;
        $db = static::getDB();
        $sql = "SELECT * FROM posts_reports LIMIT $sqlPage, 10";
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $reports = $stmt->fetchAll();
        foreach($reports as $report)
        {
            $user = User::findByID($report->user_id);
            $username = $user->name;
            $report->username = $username;
            $post = Post::getPostByID($report->post_id);
            $post_title = $post->title;
            $report->post_title = $post_title;
        }
        return [$reports, $paginator->render(true)];
    }

    /**
     * Get all the reports of posts in the database
     *
     * @return array of all the reports
     */
    public static function getAllRepliesReports()
    {
        $numberOfRepliesReports = static::getCountOfRepliesReports();
        $paginator = new \Zebra_Pagination();
        $page = $paginator->get_page();
        $paginator->records($numberOfRepliesReports);
        $paginator->records_per_page(10);
        $sqlPage = ($page - 1) * 10;
        $db = static::getDB();
        $sql = "SELECT * FROM replies_reports LIMIT $sqlPage, 10";
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $reports = $stmt->fetchAll();
        foreach($reports as $report)
        {
            $user = User::findByID($report->user_id);
            $username = $user->name;
            $report->username = $username;
        }
        return [$reports, $paginator->render(true)];
    }

    /**
     * Return a post report of a given ID
     *
     * @param int $id The ID of the report
     *
     * @return mixed Report with ID of $id, False otherwise
     */
    public static function getPostReportByID($id)
    {
        $db = static::getDB();
        $sql = 'SELECT * FROM posts_reports WHERE id = :id LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if($stmt->execute())
        {
            return $stmt->fetch();
        }
        else
        {
            return false;
        }
    }

    /**
     * Delete the current instance of the post report from the posts_reports table in the database
     *
     * @return boolean True if deleted, False otherwise
     */
    public function deletePostReport()
    {
        $db = static::getDB();
        $sql = 'DELETE FROM posts_reports WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Return a post report of a given ID
     *
     * @param int $id The ID of the report
     *
     * @return mixed Report with ID of $id, False otherwise
     */
    public static function getReplyReportByID($id)
    {
        $db = static::getDB();
        $sql = 'SELECT * FROM replies_reports WHERE id = :id LIMIT 1';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        if($stmt->execute())
        {
            return $stmt->fetch();
        }
        else
        {
            return false;
        }
    }

    /**
     * Delete the current instance of the reply report from the posts_reports table in the database
     *
     * @return boolean True if deleted, False otherwise
     */
    public function deleteReplyReport()
    {
        $db = static::getDB();
        $sql = 'DELETE FROM replies_reports WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id,PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Return the number of post reports in the database
     *
     * @return int count of reports of posts
     */
    protected static function getCountOfPostsReports()
    {
        $db = static::getDB();
        $sql = 'SELECT count(*) AS count FROM posts_reports';
        $result = $db->query($sql);
        $result = $result->fetch();
        return $result['count'];
    }

    /**
     * Return the number of replies reports in the database
     *
     * @return int count of reports of replies
     */
    protected static function getCountOfRepliesReports()
    {
        $db = static::getDB();
        $sql = 'SELECT count(*) AS count FROM replies_reports';
        $result = $db->query($sql);
        $result = $result->fetch();
        return $result['count'];
    }
}