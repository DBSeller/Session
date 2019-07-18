<?php

namespace DBSeller\Session\Tests;

use DBSeller\Session\Session;
use Exception;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testShouldStartSession()
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
    public function testShouldAddParameters($data)
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
    public function testShouldRemoveParameters($data)
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
    public function testShouldDestroySession()
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
    public function testShouldReplaceSessionData($data)
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

    /**
     * @dataProvider provideData
     * @param array $data
     * @throws Exception
     */
    public function testShouldGetSessionParameters($data)
    {
        $session = Session::getInstance()
            ->add($data)
            ->start();

        self::assertEquals('TO_SESSION_1', $session->get('VAR_1'));
        self::assertEquals('TO_SESSION_1', $_SESSION['VAR_1']);
    }

    /**
     * @throws Exception
     */
    public function testShouldSetSessionParameters()
    {
        $session = Session::getInstance()
            ->start()
            ->set('VAR_1', 'TO_SESSION_1');

        self::assertEquals('TO_SESSION_1', $_SESSION['VAR_1']);
        self::assertEquals('TO_SESSION_1', $session->get('VAR_1'));
    }

    /**
     * @dataProvider provideData
     * @param $data
     * @throws Exception
     */
    public function testShouldParseSessionToJson($data)
    {
        $session = Session::getInstance()
            ->add($data)
            ->start();

        self::assertEquals(json_encode($data), $session->toJSON());
    }

    public function provideData()
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
