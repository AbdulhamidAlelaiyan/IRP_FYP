<?php


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
        $this->user->validate();
        $this->IncorrectUser->validate();
    }

    public function testUserValidationReturnNoErrorIfDataIsCorrect()
    {
        $this->assertEmpty($this->user->errors);
    }

    public function testUserValidationReturnsErrorMessagesIfDataIsIncorrect()
    {
        $this->assertNotEmpty($this->IncorrectUser->errors);
    }

    public function testUserValidationReturnsErrorIfNameIsEmpty()
    {
        $this->data['name'] = '';
        $user = new \App\Models\User($this->data);
        $user->validate();
        $this->assertNotEmpty($user->errors);
    }

    public function testUserValidationReturnsErrorIfEmailIsEmpty()
    {
        $this->data['email'] = '';
        $user = new \App\Models\User($this->data);
        $user->validate();
        $this->assertNotEmpty($user->errors);
    }

    public function testUserValidationReturnsErrorIfPasswordIsEmpty()
    {
        $this->data['password'] = '';
        $user = new \App\Models\User($this->data);
        $user->validate();
        $this->assertNotEmpty($user->errors);
    }

    public function testUserValidationReturnsErrorIfPasswordIsLessThan6Characters()
    {
        $this->data['password'] = '111';
        $user = new \App\Models\User($this->data);
        $user->validate();
        $this->assertNotEmpty($user->errors);
    }

    public function testUserValidationReturnsErrorIfPasswordDoesNotContainANumber()
    {
        $this->data['password'] = 'aaaaaa';
        $user = new \App\Models\User($this->data);
        $user->validate();
        $this->assertNotEmpty($user->errors);
    }

    public function testUserValidationReturnsErrorIfPasswordDoesNotContainALetter()
    {
        $this->data['password'] = '123456';
        $user = new \App\Models\User($this->data);
        $user->validate();
        $this->assertNotEmpty($user->errors);
    }
}