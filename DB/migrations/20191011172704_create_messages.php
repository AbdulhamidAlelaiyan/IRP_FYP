<?php

use Phinx\Migration\AbstractMigration;

class CreateMessages extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $messages = $this->table('messages');
        $messages->addColumn('from_user', 'integer')
            ->addColumn('to_user', 'integer')
            ->addColumn('title', 'string', ['limit' => 255])
            ->addColumn('body', 'text')
            ->addForeignKey('from_user', 'users', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('to_user', 'users', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addTimestamps()
            ->save();
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('messages')->drop()->save();
    }
}
