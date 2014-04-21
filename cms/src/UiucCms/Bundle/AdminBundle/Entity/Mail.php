<?php

namespace UiucCms\Bundle\AdminBundle\Entity;



/**
 * Mail entity for mass mailing
 */
class Mail
{
    private $to;
    private $from;
    private $subject;
    private $body;
    
    public function Mail() 
    {
        $this->to = array();
    }
    
    public function setTo($addresses)
    {
        $this->to = $addresses;
    }
    
    public function addTo($address)
    {
        $this->to[] = $address;
    }
    
    public function setFrom($address)
    {
        $this->from = $address;
    }
    
    public function getSubject()
    {
        return $this->subject;
    }
    
    public function setSubject($text)
    {
        $this->subject = $text;
    }
    
    public function getBody()
    {
        return $this->body;
    }
    
    public function setBody($text)
    {
        $this->body = $text;
    }
    
    public function sendMail($mailer)
    {
        $message = \Swift_Message::newInstance()
            ->setTo($this->to)
            ->setSubject($this->subject)
            ->setFrom($this->from)
            ->setBody($this->body)
                #$this->renderView(
                #    'UiucCmsAdminBundle:Default:mail.txt.twig',
                #    array('body' => $body)
                #)
            #)
        ;
        
        //get Swift_Message bundle working to use
        return $mailer->send($message);
    }   
}

?>