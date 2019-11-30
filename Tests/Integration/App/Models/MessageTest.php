<?php

namespace Tests\Integrtion\App\Models;

use App\Models\Message;
use Core\Model;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    protected $message;
    protected $incorrect_message;

    protected $correct_data;
    protected $incorrect_data;

    protected $method;

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

    public function testMessageWillBeSavedIfDataIsCorrect()
    {
        $this->message->deleteByFromAndTo();
        $this->assertTrue($this->message->save());
    }

    public function testMessageWillNotBeSavedDataIsIncorrect()
    {
        $this->assertFalse($this->incorrect_message->save());
    }

    public function testMessageWillNotBeSavedIfTitleIsMissing()
    {
        $this->correct_data['title'] = null;
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->assertFalse($this->newMessage->save());
    }

    public function testMessageWillNotBeSavedIfBodyIsMissing()
    {
        $this->correct_data['body'] = null;
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->assertFalse($this->newMessage->save());
    }

    public function testMessageWillNotBeSavedIfFromIdIsMissing()
    {
        $this->correct_data['from'] = null;
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->assertFalse($this->newMessage->save());
    }

    public function testMessageWillNotBeSavedIfToIdIsMissing()
    {
        $this->correct_data['to'] = null;
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->assertFalse($this->newMessage->save());
    }

    public function testMessageWillNotBeSavedIfFromIsNotInt()
    {
        $this->correct_data['from'] = 'a';
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->assertFalse($this->newMessage->save());
    }

    public function testMessageWillNotBeSavedIfToIsNotInt()
    {
        $this->correct_data['to'] = 'a';
        $this->newMessage = new \App\Models\Message($this->correct_data);
        $this->assertFalse($this->newMessage->save());
    }
}