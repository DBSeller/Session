<?php

use DBSeller\Session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * @throws Exception
     */
    function testShouldStartSession()
    {
        Session::getInstance()
            ->start();

        self::assertTrue(session_status() === PHP_SESSION_ACTIVE);
    }

    /**
     * @throws Exception
     */
    function testAddParameters()
    {
        $parameters = array(
            'VAR_1' => 'SESSION'
        );

        $parameters2 = array(
            'VAR_2' => 'SESSION'
        );

        Session::getInstance()
            ->start()
            ->addParameters($parameters)
            ->addParameters($parameters2);

        self::assertTrue(array_key_exists('VAR_1', $_SESSION));
        self::assertTrue(array_key_exists('VAR_2', $_SESSION));
    }

    /**
     * @throws Exception
     */
    function testDestroySession()
    {
        Session::getInstance()
            ->start()
            ->destroy();

        self::assertTrue(session_status() === PHP_SESSION_NONE);
    }
}
