<?php

namespace ZendTest\Mail\Transport;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mail\Message,
    Zend\Mail\Transport,
    Zend\Mail\Transport\Smtp,
    ZendTest\Mail\TestAsset\SmtpProtocolSpy;

class SmtpTest extends TestCase
{
    public $transport;
    public $connection;

    public function setUp()
    {
        $this->transport  = new Smtp();
        $this->connection = new SmtpProtocolSpy();
        $this->transport->setConnection($this->connection);
    }

    public function getMessage()
    {
        $message = new Message();
        $message->addTo('zf-devteam@zend.com', 'ZF DevTeam')
                ->addCc('matthew@zend.com')
                ->addBcc('zf-crteam@lists.zend.com', 'CR-Team, ZF Project')
                ->addFrom(array(
                    'zf-devteam@zend.com',
                    'Matthew' => 'matthew@zend.com',
                ))
                ->setSender('ralph.schindler@zend.com', 'Ralph Schindler')
                ->setSubject('Testing Zend\Mail\Transport\Sendmail')
                ->setBody('This is only a test.');
        $message->headers()->addHeaders(array(
            'X-Foo-Bar' => 'Matthew',
        ));
        return $message;
    }

    public function testReceivesMailArtifacts()
    {
        $message = $this->getMessage();
        $this->transport->send($message);

        $this->assertEquals('ralph.schindler@zend.com', $this->connection->getMail());
        $expectedRecipients = array('zf-devteam@zend.com', 'matthew@zend.com', 'zf-crteam@lists.zend.com');
        $this->assertEquals($expectedRecipients, $this->connection->getRecipients());

        $data = $this->connection->getData();
        $this->assertContains('To: ZF DevTeam <zf-devteam@zend.com>', $data);
        $this->assertContains('Subject: Testing Zend\Mail\Transport\Sendmail', $data);
        $this->assertContains("Cc: matthew@zend.com\r\n", $data);
        $this->assertNotContains("Bcc: \"CR-Team, ZF Project\" <zf-crteam@lists.zend.com>\r\n", $data);
        $this->assertNotContains("zf-crteam@lists.zend.com", $data);
        $this->assertContains("From: zf-devteam@zend.com, Matthew <matthew@zend.com>\r\n", $data);
        $this->assertContains("X-Foo-Bar: Matthew\r\n", $data);
        $this->assertContains("Sender: Ralph Schindler <ralph.schindler@zend.com>\r\n", $data);
        $this->assertContains("\r\n\r\nThis is only a test.", $data, $data);
    }
}
