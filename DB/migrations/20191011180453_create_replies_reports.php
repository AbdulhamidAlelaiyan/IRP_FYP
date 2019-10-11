<?php

use Phinx\Migration\AbstractMigration;

class CreateRepliesReports extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $repliesReports = $this->table('replies_reports');
        $repliesReports->addColumn('reply_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('text', 'text')
            ->addTimestamps()
            ->addForeignKey('reply_id', 'replies', 'id',
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
        $this->table('replies_reports')->drop()->save();
    }
}
