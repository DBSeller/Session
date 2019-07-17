<?php

namespace DBSeller\Session\Tests;

use DBSeller\Session\Session;
use Exception;
use PHPUnit\Framework\TestCase;
use function Sodium\add;

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
     * @dataProvider provideData
     * @param array $data
     * @throws Exception
     */
    function testAddParameters($data)
    {
        Session::getInstance()
            ->start()
            ->add($data);

        self::assertTrue(array_key_exists('VAR_1', $_SESSION));
        self::assertTrue(array_key_exists('VAR_2', $_SESSION));
    }

    /**
     * @dataProvider provideData
     * @param $data
     * @throws Exception
     */
    function testRemoveParameters($data)
    {
        Session::getInstance()
            ->start()
            ->add($data)
            ->remove('VAR_1');

        self::assertTrue(!array_key_exists('VAR_1', $_SESSION));
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

    /**
     * @dataProvider provideData
     * @throws Exception
     */
    function testReplaceSessionData($data)
    {
        Session::getInstance()
            ->add($data)
            ->start()
            ->replace(array(
                'NEW_DATA' => 'TO_SESSION'
            ));

        self::assertTrue(array_key_exists('NEW_DATA', $_SESSION));
        self::assertTrue(!array_key_exists('VAR_1', $_SESSION));
    }

    function provideData()
    {
        return array(
            array('data' =>
                array(
                    'VAR_1' => 'TO_SESSION_1',
                    'VAR_2' => 'TO_SESSION_2'
                )
            )
        );
    }
}
