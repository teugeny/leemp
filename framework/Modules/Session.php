<?php

/**
 * Class Session
 */
class Session
{
    const SESSION_STARTED = TRUE;

    const SESSION_NOT_STARTED = FALSE;

    private $sessionState = self::SESSION_NOT_STARTED;

    private static $instance;


    /**
     *    Returns THE instance of 'Session'.
     *    The session is automatically initialized if it wasn't.
     *
     *    @return    object
     **/

    public static function getInstance()
    {
        if ( !isset(self::$instance))
        {
            self::$instance = new self;
        }

        self::$instance->startSession();

        return self::$instance;
    }


    /**
     *    (Re)starts the session.
     *
     *    @return    bool    TRUE if the session has been initialized, else FALSE.
     **/

    public function startSession()
    {
        if ( $this->sessionState == self::SESSION_NOT_STARTED )
        {
            $this->sessionState = session_start();
        }

        return $this->sessionState;
    }


    /**
     * @return bool
     */
    public function isCookieEnabled()
    {
        setcookie('check_cookies_enabled', 'test', time() + 3600, '/');
        return count($_COOKIE) > 0
            ? true
            : false;
    }

    public function isSessionCanBeUsed()
    {
        $_SESSION['test'] = "data";
        return isset($_SESSION['test'])
            ? true
            : false;
    }

    /**
     *    Stores datas in the session.
     *    Example: $instance->foo = 'bar';
     *
     *    @param    name
     *    @param    value
     *    @return    void
     **/

    public function __set( $name , $value )
    {
        $_SESSION[$name] = $value;
    }


    /**
     *    Gets datas from the session.
     *    Example: echo $instance->foo;
     *
     *    @param    name
     *    @return    mixed
     **/

    public function __get( $name )
    {
        if ( isset($_SESSION[$name]))
        {
            return $_SESSION[$name];
        }
    }


    /**
     * @param $name
     * @return bool
     */
    public function __isset( $name )
    {
        return isset($_SESSION[$name]);
    }


    /**
     * @param $name
     */
    public function __unset( $name )
    {
        unset( $_SESSION[$name] );
    }


    /**
     *    Destroys the current session.
     *
     *    @return    bool    TRUE is session has been deleted, else FALSE.
     **/

    public function destroy()
    {
        if ( $this->sessionState == self::SESSION_STARTED )
        {
            $this->sessionState = !session_destroy();
            unset( $_SESSION );

            return !$this->sessionState;
        }

        return FALSE;
    }

    /**
     * Session constructor.
     */
    private function __construct() {}
}