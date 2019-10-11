<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $users = $this->table('users');
        $users->addColumn('name', 'string', ['limit' => 50])
        ->addColumn('email', 'string', ['limit' => 255])
        ->addColumn('password_hash', 'string', ['limit' => 255])
        ->addColumn('password_reset_hash', 'string', ['limit' => 64, 'null' => true])
        ->addColumn('password_reset_expires_at', 'datetime', ['null' => true])
        ->addColumn('activation_hash', 'string', ['limit' => 64, 'null' => true])
        ->addColumn('is_active', 'boolean', ['default' => false])
        ->addColumn('type', 'string', ['limit' => 255, 'default' => 'reader'])
        ->addColumn('bio', 'string', ['limit' => 255, 'null' => true])
        ->addColumn('profile_photo', 'string', ['limit' => 255, 'null' => true])
        ->addIndex(['password_reset_hash', 'email', 'activation_hash'], ['unique' => true])
        ->addTimestamps()
        ->save();
    }

    /**
     * Migrate down
     */
    public function down()
    {
        $this->table('users')->drop()->save();
    }
}
