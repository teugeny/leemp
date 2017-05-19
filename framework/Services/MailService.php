<?php

/**
 * Class MailService
 */
class MailService {

    /** @var  string */
    private $to;

    /** @var  string */
    private $from;

    /** @var  string */
    private $subject;

    /** @var   */
    private $message;

    /**
     * @return MailService
     */
    public static function create()
    {
        return new self;
    }

    /**
     * @param $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @param $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return bool
     */
    public function send()
    {
        //return mail($this->to, $this->subject, $this->message, $this->getHeaders());
        return wp_mail($this->to, $this->subject, $this->message, $this->getHeaders());
    }

    private function getHeaders()
    {
        return 'From: wordpress@arkadium.com' . "\r\n" .
        'Reply-To: wordpress@arkadium.com' . "\r\n" .
        'Content-Type: text/html; charset=UTF-8' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    }
}