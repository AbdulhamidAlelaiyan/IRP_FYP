<?php

use Phinx\Migration\AbstractMigration;

class CreatePostsPoints extends AbstractMigration
{
    /**
     * Migrate up
     */
    public function up()
    {
        $postsPoints = $this->table('posts_points', ['id' => false]);
        $postsPoints->addColumn('user_id', 'integer')
            ->addColumn('post_id', 'integer')
            ->addColumn('point', 'boolean')
            ->addForeignKey('user_id', 'users', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('post_id', 'posts', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addTimestamps()
            ->save();
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('posts_points')->drop()->save();
    }
}
