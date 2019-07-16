<?php

namespace DBSeller\Session;

use Exception;

class SessionData extends RequestBag
{
    /**
     * @var integer
     */
    const DISABLED = 0;

    /**
     * @var integer
     */
    const NONE = 1;

    /**
     * @var integer
     */
    const ACTIVE = 2;

    /**
     * @var integer
     */
    private $status = SessionData::NONE;

    /**
     * @var boolean
     */
    private $writeable = true;

    /**
     * @var array
     */
    private $params;

    /**
     * Session constructor.
     * @param array $data
     * @param array $params
     */
    public function __construct($data = array(), $params = array())
    {
        parent::__construct($data);
        $this->params = $params;
    }


    /**
     * @param integer $id
     * @return integer | SessionData
     */
    public function id($id = null)
    {

        if (!is_null($id)) {
            session_id($id);
            return $this;
        }
        return session_id();
    }

    /**
     * @param string $name
     * @return string | SessionData
     */
    public function name($name = null)
    {

        if (!is_null($name)) {
            session_name($name);
            return $this;
        }
        return session_name();
    }

    /**
     * @return SessionData
     */
    public function destroy()
    {

        if ($this->writeable()) {
            session_destroy();
        }
        return $this;
    }

    /**
     * @return SessionData
     */
    public function close()
    {

        if ($this->writeable()) {
            session_write_close();
        }
        $this->status = SessionData::NONE;
        return $this;
    }

    /**
     * @return array
     */
    private function getCurrentSessionData()
    {

        $result = array();
        $path = session_save_path() . DS . 'sess_' . $this->id();

        if (!is_readable($path)) {
            return $result;
        }

        $content = file_get_contents($path);

        $name = session_name();
        $id = session_id();

        session_name('w' . (string)mt_rand());
        session_id(uniqid());
        session_start();
        session_decode($content);
        $result = $_SESSION;

        unset($_SESSION);
        session_destroy();

        header_remove('Set-Cookie');

        session_name($name);
        session_id($id);

        return $result;
    }

    /**
     * @return SessionData
     * @throws Exception
     */
    public function start()
    {
        if (!$this->writeable() && $this->status === SessionData::NONE) {
            $_SESSION = $this->getCurrentSessionData();
            $this->replace($_SESSION);
            $this->status = SessionData::ACTIVE;

            return $this;
        }

        if ($this->status === SessionData::NONE || !isset($_SESSION)) {
            if (headers_sent($file, $line)) {
                $error = "Erro ao iniciar sessão, cabeçalhos da requisição já enviadados no arquivo $file:$line.";
                throw new Exception($error);
            }

            session_start();
            $this->status = SessionData::ACTIVE;
            $this->data =& $_SESSION;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * @param null $writeable
     * @return $this|bool
     */
    public function writeable($writeable = null)
    {

        if ($writeable === null) {
            return $this->writeable;
        }

        $this->writeable = (boolean)$writeable;
        return $this;
    }

    /**
     * @param mixed $message Flash content
     * @param string $type
     * @return mixed          Flash content
     */
    public function flash($message = null, $type = '')
    {

        if (empty($message)) {
            $flash = $this->get('flash');
            $this->remove('flash');
            return $flash;
        }

        return $this->set('flash', "<p class='flash $type'>" . $message . "</p>");
    }

    public function addParameters(array $parameters)
    {
        $this->add($parameters);
        $_SESSION = $this->data;
        return $this;
    }
}
