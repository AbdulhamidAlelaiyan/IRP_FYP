<?php


namespace App\Controllers\Admin;

use \Core\View;

class Config extends AdminController
{
    /**
     * Show the setting forms of the website
     *
     * @return void
     */
    public function show()
    {
        $settings = Config::getAllSettings();
        View::renderTemplate('Admin/Config/settings.html.twig',
            [
                'settings' => $settings,
            ]);
    }
}