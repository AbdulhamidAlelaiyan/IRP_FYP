<?php

use Phinx\Migration\AbstractMigration;

class CreateReplies extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $replies = $this->table('replies');
        $replies->addColumn('text', 'text')
            ->addColumn('post_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->addForeignKey('post_id', 'posts', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->save()
        ;
    }

    /**
     * Migrate downs
     */
    public function down()
    {
        $this->table('replies')->drop()->save();
    }
}
