<?php


use Phinx\Seed\AbstractSeed;

class UserAdminSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $users = $this->table('users');
        $data[] = [
            'name' => 'Admin',
            'email' => 'admin@admin.admin',
            'password_hash' => password_hash('admin', PASSWORD_DEFAULT),
            'is_active' => true,
            'type' => 'admin',
        ];
        $users->insert($data)->save();
    }
}
