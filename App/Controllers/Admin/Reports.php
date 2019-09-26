<?php

namespace App\Controllers\Admin;

use App\Flash;
use App\Models\Report;
use Core\View;

class Reports extends AdminController
{
    /**
     * List all reports of posts sorted by the latest
     *
     * @return void
     */
    public function indexPostsAction()
    {
        $reports = Report::getAllPostReports();
        View::renderTemplate('Admin/Reports/index-posts.html.twig',
            [
                'reports' => $reports,
            ]);
    }

    /**
     * List all reports of posts sorted by the latest
     *
     * @return void
     */
    public function indexRepliesAction()
    {
        $reports = Report::getAllRepliesReports();
        View::renderTemplate('Admin/Reports/index-replies.html.twig',
            [
                'reports' => $reports,
            ]);
    }

    /**
     * Delete post report
     *
     * @return void
     */
    public function deletePost()
    {
        $report_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($report_id)
        {
            if($report = Report::getPostReportByID($report_id))
            {
                if($report->deletePostReport())
                {
                    Flash::addMessage('Report deleted!', Flash::SUCCESS);
                    $this->redirect('/admin/reports/index-posts');
                }
                else
                {
                    Flash::addMessage('Report NOT deleted!', Flash::DANGER);
                    $this->redirect('/admin/reports/index-posts');
                }
            }
            else
            {
                Flash::addMessage('Report NOT found!', Flash::DANGER);
                $this->redirect('/admin/reports/index-posts');
            }
        }
        else
        {
            Flash::addMessage('Report ID NOT found!', Flash::DANGER);
            $this->redirect('/admin/reports/index-posts');
        }
    }

    /**
     * Delete reply report
     *
     * @return void
     */
    public function deleteReply()
    {
        $report_id = filter_var($this->route_params['isbn'], FILTER_SANITIZE_NUMBER_INT);
        if($report_id)
        {
            if($report = Report::getReplyReportByID($report_id))
            {
                if($report->deleteReplyReport())
                {
                    Flash::addMessage('Report deleted!', Flash::SUCCESS);
                    $this->redirect('/admin/reports/index-replies');
                }
                else
                {
                    Flash::addMessage('Report NOT deleted!', Flash::DANGER);
                    $this->redirect('/admin/reports/index-replies');
                }
            }
            else
            {
                Flash::addMessage('Report NOT found!', Flash::DANGER);
                $this->redirect('/admin/reports/index-replies');
            }
        }
        else
        {
            Flash::addMessage('Report ID NOT found!', Flash::DANGER);
            $this->redirect('/admin/reports/index-replies');
        }
    }

}