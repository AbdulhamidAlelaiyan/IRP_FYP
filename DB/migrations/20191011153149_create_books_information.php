<?php

use Phinx\Migration\AbstractMigration;

class CreateBooksInformation extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $booksInformation = $this->table('books_information', ['id' => false, 'primary_key' => 'isbn']);
        $booksInformation->addColumn('isbn', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('publication_date', 'date', ['null' => false])
            ->addColumn('edition', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('authors', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('cover_image', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('description', 'string', ['null' => true])
            ->addTimestamps()
            ->save();
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('books_information')->drop()->save();
    }
}
