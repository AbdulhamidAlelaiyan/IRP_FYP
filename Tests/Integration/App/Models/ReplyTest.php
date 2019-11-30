<?php
namespace Tests\Integration\App\Models;
use App\Models\Reply;

use PHPUnit\Framework\TestCase;

class ReplyTest extends TestCase
{
    protected $reply;
    protected $IncorrectReply;

    protected $correct_data;
    protected $incorrect_data;

    protected $backupGlobalsBlacklist = array( '_SESSION' );

    public function setUp(): void
    {
        parent::setUp();
        $this->correct_data = [
            'post_id' => '1',
            'editordata' => 'Some Data',
        ];
        $this->incorrect_data = [
            'post_id' => '1',
        ];
        $_POST['editordata'] = 'Some body';
        $_SESSION['user_id'] = 1;
        $this->reply = new Reply($this->correct_data);
        $this->IncorrectReply = new Reply($this->incorrect_data);
        $this->reply->validate();
        $this->IncorrectReply->validate();
    }

    public function testReplyWillNotBeSavedIfDataIsInCorrect()
    {
        $this->assertFalse($this->IncorrectReply->save());
    }
}