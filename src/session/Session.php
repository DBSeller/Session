<?php

namespace DBSeller\Session;

use DateTime;
use Exception;

class Session
{
    /**
     * @var Session
     */
    public static $instance;

    /**
     * @var SessionData
     */
    private $session;

    /**
     * @var DateTime
     */
    private $data;

    /**
     * DefaultSession constructor.
     */
    private function __construct()
    {
        $this->data = new DateTime();
        $this->session = new SessionData();
    }

    /**
     * @return Session
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * @return SessionData
     */
    public function session()
    {
        return $this->session;
    }

    /**
     * @return SessionData
     * @throws Exception
     */
    public function start()
    {
        return $this->session->start();
    }

    /**
     * @return SessionData
     */
    public function destroy()
    {
        return $this->session->destroy();
    }
}
