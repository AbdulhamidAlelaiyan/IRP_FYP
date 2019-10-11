<?php

use Phinx\Migration\AbstractMigration;

class CreateRepliesPoints extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $repliesPoints = $this->table('replies_points', ['id' => false]);
        $repliesPoints->addColumn('user_id', 'integer')
            ->addColumn('reply_id', 'integer')
            ->addColumn('point', 'boolean')
            ->addForeignKey('user_id', 'users', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('reply_id', 'replies', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addTimestamps()
            ->save();
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('replies_points')->drop()->save();
    }
}

