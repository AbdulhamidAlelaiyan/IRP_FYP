<?php

use Phinx\Migration\AbstractMigration;

class CreateChapterHistory extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $chapterHistory = $this->table('chapter_history');
        $chapterHistory->addColumn('isbn', 'string', ['limit' => 10])
            ->addColumn('chapter_no', 'integer', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_SMALL])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->addForeignKey('isbn','books_information', 'isbn',
                ['update' => 'CASCADE', 'delete' => 'CASCADE'])
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
        $this->table('chapter_history')->drop()->save();
    }
}
