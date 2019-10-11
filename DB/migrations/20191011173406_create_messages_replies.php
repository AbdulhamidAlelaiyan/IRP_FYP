<?php

use Phinx\Migration\AbstractMigration;

class CreateMessagesReplies extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $messages_replies = $this->table('messages_replies');
        $messages_replies->addColumn('message_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('textbody', 'text')
            ->addTimestamps()
            ->addForeignKey('user_id', 'users', 'id',
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
        $this->table('messages_replies')->drop()->save();
    }
}
