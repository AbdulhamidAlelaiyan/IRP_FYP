<?php

use Phinx\Migration\AbstractMigration;

class CreateRememberedLogins extends AbstractMigration
{
    /**
     * Migrate Up
     */
    public function up()
    {
        $remembered_logins = $this->table('remembered_logins', ['id' => false, 'primary_key' => 'token_hash']);
        $remembered_logins->addColumn('token_hash', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('user_id', 'integer', ['limit' => 11, 'null' => false])
            ->addColumn('expires_at', 'datetime', ['null' => false])
            ->addForeignKey('user_id', 'users', 'id',
                ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addTimestamps()
            ->save();

    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('remembered_logins')->drop()->save();
    }
}
