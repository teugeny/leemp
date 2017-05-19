<?php

/**
 * Service to work with reports
 * Class ReportService
 */
class ReportService
{
    /**
     * Data of DataStorage.
     * Consist of previews tasks results and output which we can use in other tasks
     * @var
     */
    private $storageData;

    /**
     * Template path
     * @var
     */
    private $template;

    /**
     * Walker is a class which work with special report.
     * For example.
     * We have Parser application which should send a report after work and Diagnostic tool.
     * They have different ways to work with a report.
     * And in this situation we use Walkers to create special logic for each application
     * @var  ReportWalkerInterface
     */
    private $walker;

    /**
     * Subject of email report
     * @var  string
     */
    private $subject;

    /**
     * Current instance
     * @var
     */
    private static $instance;

    /**
     * @return ReportService
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $walker
     * @return $this
     */
    public function setWalker($walker)
    {
        /** @var  $walker ReportWalkerInterface */
        $this->walker = $walker;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setStorageData($data)
    {
        $this->storageData = $data;
        return $this;
    }

    /**
     * @param $item
     * @return $this
     */
    public function addReportItem($item)
    {
        $this->walker->addReportItem($item);
        return $this;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
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
     * Set data to walker
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->walker->setData($data);
        return $this;
    }
    /**
     * Get data from walker
     * @return mixed
     */
    public function getData()
    {
        return $this->walker->get();
    }

    /**
     * Run walker save method to create a record in DB
     */
    public function save()
    {
        $this->walker->save();
    }

    public function get($what, $ratio, $target)
    {
        return $this->walker->getReport($what, $ratio, $target);
    }

    /**
     * Send emails to list
     * By default list of emails is in Env->managers_email
     * @param null array $recipients
     * @return $this
     */
    public function send($recipients = null)
    {
        $data = gettype($this->getData()) != 'array'
            ? (array)$this->getData()
            : $this->getData();

        $email = View::render($this->template,$data,true);
        $recipients = ($recipients == null)
            ? Env::me()->get('managers_email')
            : $recipients;

        foreach ($recipients as $manager) {
            MailService::create()
                ->setTo($manager)
                ->setSubject($this->subject)
                ->setMessage($email)
                ->send();
        }

        return $this;
    }

    /**  */
    private function __clone() {}

    /**  */
    private function __construct() {}
}