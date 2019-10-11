<?php

use Phinx\Migration\AbstractMigration;

class CreateBooksContent extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $books_content = $this->table('books_content');
        $books_content->addColumn('isbn', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('chapter', 'integer',
                ['limit' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'null' => false])
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('content', 'text',
                ['null' => false, 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_MEDIUM])
            ->addColumn('video_id', 'string', ['limit' => 255, 'null' => true])
            ->addTimestamps()
            ->save()
            ;
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('books_content')->drop()->save();
    }
}
