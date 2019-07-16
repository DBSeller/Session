<?php

namespace DBSeller\Session;

use Exception;

class Session
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
     * @var Session
     */
    public static $instance;

    /**
     * @var integer
     */
    private $status = Session::NONE;

    /**
     * @var boolean
     */
    private $writable = true;

    /**
     * @var array
     */
    private $data = array();

    /**
     * Session constructor.
     */
    private function __construct()
    {
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
     * @param integer $id
     * @return integer | Session
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
     * @return string | Session
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
     * @return Session
     */
    public function destroy()
    {

        if ($this->writable()) {
            session_destroy();
        }
        return $this;
    }

    /**
     * @return Session
     */
    public function close()
    {

        if ($this->writable()) {
            session_write_close();
        }
        $this->status = Session::NONE;
        return $this;
    }

    /**
     * @return array
     */
    private function getCurrentSessionData()
    {

        $result = array();
        $path = session_save_path() . DIRECTORY_SEPARATOR . 'sess_' . $this->id();

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
     * @param array $data
     * @return $this
     */
    private function replace(array $data = array())
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return Session
     * @throws Exception
     */
    public function start()
    {
        if (!$this->writable() && $this->status === Session::NONE) {
            $_SESSION = $this->getCurrentSessionData();
            $this->replace($_SESSION);
            $this->status = Session::ACTIVE;

            return $this;
        }

        if ($this->status === Session::NONE || !isset($_SESSION)) {
            if (headers_sent($file, $line)) {
                $error = "Headers already sent in $file:$line.";
                throw new Exception($error);
            }

            session_start();
            $this->status = Session::ACTIVE;
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
     * @param null $writable
     * @return $this|bool
     */
    public function writable($writable = null)
    {

        if ($writable === null) {
            return $this->writable;
        }

        $this->writable = (boolean)$writable;
        return $this;
    }

    /**
     * @param array $data
     * @param bool $replace
     * @return $this
     */
    public function merge(array $data, $replace = true)
    {
        $this->data = $replace ? array_merge($this->data, $data) : array_merge($data, $this->data);
        return $this;
    }

    /**
     * @param array $data
     * @param bool $replace
     * @return Session
     */
    public function add(array $data, $replace = false)
    {
        return $this->merge($data, $replace);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    private function get($key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->data);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->data);
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value)
    {
        return in_array($value, $this->data);
    }

    /**
     * @param $key
     * @return $this
     */
    public function remove($key)
    {
        unset($this->data[$key]);
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
}
