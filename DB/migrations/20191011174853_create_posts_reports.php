<?php

use Phinx\Migration\AbstractMigration;

class CreatePostsReports extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $posts_reports = $this->table('posts_reports');
        $posts_reports->addColumn('post_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('text', 'text')
            ->addTimestamps()
            ->addForeignKey('post_id', 'posts', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->save()
            ;
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('posts_reports')->drop()->save();
    }
}
