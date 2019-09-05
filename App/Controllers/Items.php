<?php

namespace App\Controllers;

use Core\Controller;
use \Core\View;

/**
 * Item controller (example)
 *
 * PHP version 7.0
 */
//class Item extends \Core\Controller
class Items extends Authenticated
{

    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     */
    /*
    protected function before()
    {
        $this->requireLogin();
    }
    */

    /**
     * Item index
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Item/index.html');
    }

    /**
     * Add a new item
     *
     * @return void
     */
    public function newAction()
    {
        echo "new action";
    }

    /**
     * Show an item
     *
     * @return void
     */
    public function showAction()
    {
        echo "show action";
    }
}
