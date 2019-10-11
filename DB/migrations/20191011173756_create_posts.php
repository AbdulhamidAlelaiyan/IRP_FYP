<?php

use Phinx\Migration\AbstractMigration;

class CreatePosts extends AbstractMigration
{
   /**
    * Migrate up
    */
   public function up()
   {
       $posts = $this->table('posts');
       $posts->addColumn('isbn', 'string', ['limit' => 10])
           ->addColumn('title', 'string', ['limit' => 255])
           ->addColumn('body', 'text')
           ->addColumn('user_id', 'integer')
           ->addForeignKey('user_id', 'users', 'id',
               ['delete' => 'CASCADE', 'update' => 'CASCADE'])
           ->addForeignKey('isbn', 'books_information', 'isbn',
               ['delete' => 'CASCADE', 'update' => 'CASCADE'])
           ->addTimestamps()
           ->save()
           ;
   }

   /**
    * Migrate down
    */
   public function down()
   {
        $this->table('posts')->drop()->save();
   }
}
