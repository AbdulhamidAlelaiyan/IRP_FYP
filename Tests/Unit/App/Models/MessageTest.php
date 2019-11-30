<?php


use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    protected $message;
    protected $incorrect_message;

    protected $correct_data;
    protected $incorrect_data;

    public function setUp(): void
    {
        parent::setUp();
        $this->correct_data = [
            'from' => '1',
            'to' => '2',
            'title' => 'Ahmed',
            'body' => 'Some Body',
        ];
        $this->incorrect_data = [
            'from' => '1',
            'to' => '2',
            'title' => 'Ahmed',
            'body' => '',
        ];
        $this->message = new \App\Models\Message($this->correct_data);
        $this->incorrect_message = new \App\Models\Message($this->incorrect_data);
        $this->message->validate();
        $this->incorrect_message->validate();
    }

    public function testMessageReturnsNoErrorIfDataIsCorrect()
    {
        $this->assertNull($this->message->getErrors());
    }

    public function testMessageReturnsAnErrorIfDataIsIncorrect()
    {
        $this->assertNotNull($this->incorrect_message->getErrors());
    }

    public function testMessageReturnsAnErrorIfTitleIsMissing()
    {
        $this->correct_data['title'] = null;
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->newMessage->validate();
        $this->assertNotNull($this->newMessage->getErrors());
    }

    public function testMessageReturnsAnErrorIfBodyIsMissing()
    {
        $this->correct_data['body'] = null;
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->newMessage->validate();
        $this->assertNotNull($this->newMessage->getErrors());
    }

    public function testMessageReturnsAnErrorIfFromIdIsMissing()
    {
        $this->correct_data['from'] = null;
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->newMessage->validate();
        $this->assertNotNull($this->newMessage->getErrors());
    }

    public function testMessageReturnsAnErrorIfToIdIsMissing()
    {
        $this->correct_data['to'] = null;
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->newMessage->validate();
        $this->assertNotNull($this->newMessage->getErrors());
    }

    public function testMessageReturnsAnErrorIfFromIsNotInt()
    {
        $this->correct_data['from'] = 'a';
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->newMessage->validate();
        $this->assertNotNull($this->newMessage->getErrors());
    }

    public function testMessageReturnsAnErrorIfToIsNotInt()
    {
        $this->correct_data['to'] = 'a';
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->newMessage->validate();
        $this->assertNotNull($this->newMessage->getErrors());
    }
}