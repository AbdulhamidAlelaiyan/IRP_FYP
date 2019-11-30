<?php

namespace Tests\Integrtion\App\Controllers;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected $user;
    protected $IncorrectUser;
    protected $data;
    protected $incorrect_data;

    public function setUp()
    {
        parent::setUp();
        $this->data = [
            'name' => 'Ahmed',
            'email' => 'email@email.email',
            'password' => '12#$56as',
        ];
        $this->incorrect_data = [
            'name' => 'Ahmed',
            'email' => 'email#email.email',
            'password' => '12#$56as',
        ];
        $this->user = new \App\Models\User($this->data);
        $this->IncorrectUser = new \App\Models\User($this->incorrect_data);
        User::deleteUserByEmail($this->data['email']);
        $this->user->validate();
        $this->IncorrectUser->validate();
    }

    public function testUserWillBeSavedIfDataIsCorrect()
    {
        $this->assertTrue($this->user->save());
    }

    public function testUserWillNotBeSavedIfDataIsInCorrect()
    {
        $this->assertFalse($this->IncorrectUser->save());
    }

    public function testUserWillNotBeSavedIfNameIsEmpty()
    {
        $this->data['name'] = '';
        $user = new \App\Models\User($this->data);
        $this->assertFalse($user->save());
    }

    public function testUserWillNotBeSavedIfEmailIsEmpty()
    {
        $this->data['email'] = '';
        $user = new \App\Models\User($this->data);
        $this->assertFalse($user->save());
    }

    public function testUserWillNotBeSavedIfPasswordIsEmpty()
    {
        $this->data['password'] = '';
        $user = new \App\Models\User($this->data);
        $this->assertFalse($user->save());
    }

    public function testUserWillNotBeSavedIfPasswordIsShort()
    {
        $this->data['password'] = '111';
        $user = new \App\Models\User($this->data);
        $this->assertFalse($user->save());
    }

    public function testUserWillNotBeSavedIfPasswordDoesNotContainANumber()
    {
        $this->data['password'] = 'aaaaaa';
        $user = new \App\Models\User($this->data);
        $this->assertFalse($user->save());
    }

    public function testUserWillNotBeSavedIfPasswordDoesNotContainALetter()
    {
        $this->data['password'] = '123456';
        $user = new \App\Models\User($this->data);
        $this->assertFalse($user->save());
    }
}