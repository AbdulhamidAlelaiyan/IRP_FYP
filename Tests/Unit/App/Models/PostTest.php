<?php


use App\Models\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    protected $post;
    protected $incorrect_post;

    protected $correct_data;
    protected $incorrect_data;

    protected $backupGlobalsBlacklist = array( '_SESSION' );

    public function setUp(): void
    {
        parent::setUp();
        $this->correct_data = [
            'isbn' => '1111111111',
            'user_id' => '1',
            'user' => 'User Name',
            'title' => 'Post Title',
        ];
        $this->incorrect_data = [
            'isbn' => '1111111111',
            'user_id' => '1',
            'user' => 'User Name',
            'title' => '',
        ];
        $_POST['editordata'] = 'Some body';
        $_SESSION['user_id'] = 1;
        $this->post = new Post($this->correct_data);
        $this->incorrect_post = new Post($this->incorrect_data);
        $this->post->validate();
        $this->incorrect_post->validate();
    }

    public function testPostReturnsNoErrorIfDataIsCorrect()
    {
        $this->assertEmpty($this->post->getErrors());
    }

    public function testPostReturnsAnErrorIfDataIsIncorrect()
    {
        $this->assertNotNull($this->incorrect_post->getErrors());
    }

    public function testPostReturnsAnErrorIfTitleIsMissing()
    {
        $this->correct_data['title'] = '';
        $this->newPost = new Post($this->correct_data);
        $this->newPost->validate();
        $this->assertNotNull($this->newPost->getErrors());
    }

    public function testPostReturnsAnErrorIfBodyIsMissing()
    {
        $_POST['editordata'] = '';
        $this->newPost = new Post($this->correct_data);
        $this->newPost->validate();
        $this->assertNotNull($this->newPost->getErrors());
    }
}