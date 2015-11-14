<?php
class POP3Client
{
    private $server;
    public $response;

    // initialize a new POP3Client object
    public function __construct()
    {
        $this->server = null;
        $this->response = '';
    }

    // return true if +OK response received
    private function isOk()
    {
        return substr($this->response, 0, 3) == '+OK';
    }

    // open a connection to the POP3 server
    public function connect($server, $port = 110)
    {
        if (!$this->server = @fsockopen('tcp://' . $server, 110))
        {
            return false;
        }

        $this->response = trim(fgets($this->server, 512));
        if (!$this->isOk())
        {
            fclose($this->server);
            return false;
        }
        return true;
    }

    // send USER command to server
    public function user($username)
    {
        fwrite($this->server, 'USER ' . $username . "\r\n");
        $this->response = trim(fgets($this->server, 512));
        return $this->isOk();
    }

    // send PASS command to server
    public function pass($password)
    {
        fwrite($this->server, 'PASS ' . $password . "\r\n");
        $this->response = trim(fgets($this->server, 512));
        return $this->isOk();
    }

    // send NOOP command to server (should always return true
    // or else something is seriously wrong with the POP3 server!)
    public function noop()
    {
        fwrite($this->server, 'NOOP' . "\r\n");
        $this->response = trim(fgets($this->server, 512));
        return $this->isOk();
    }

    // send STAT command to server
    public function _stat()
    {
        fwrite($this->server, 'STAT' . "\r\n");
        $this->response = trim(fgets($this->server, 512));
        return $this->isOk();
    }

    // send LIST command to server, may accept message id
    public function _list()
    {
        if (func_num_args())
        {
            $args = func_get_args();
            fwrite($this->server, 'LIST ' . $args[0] . "\r\n");
            $this->response = trim(fgets($this->server, 512));
            if (!$this->isOk())
            {
                return false;
            }
            else
            {
                $message = explode(' ', $this->response);
                array_shift($message);  // drop +OK
                $message[1] = trim($message[1]);  // trim trailing \r\n
                return $message;
            }
        }
        else
        {
            fwrite($this->server, 'LIST' . "\r\n");
            $this->response = trim(fgets($this->server, 512));
            if (!$this->isOk())
            {
                return false;
            }
            else
            {
                $messages = array();
                while (($line = fgets($this->server, 512)) != '.' . "\r\n")
                {
                    list($id, $size) = explode(' ', $line);
                    $messages[$id] = trim($size);
                }
                return $messages;
            }
        }
    }

    // send RETR command to server
    public function retr($id)
    {
        fwrite($this->server, 'RETR ' . $id . "\r\n");
        $this->response = trim(fgets($this->server, 512));
        if (!$this->isOk())
        {
            return false;
        }
        else
        {
            $message = '';
            while (($line = fgets($this->server, 512)) != '.' . "\r\n")
            {
                $message .= $line;
            }
            return $message;
        }
    }

    // send DELE command to server
    public function dele($id)
    {
        fwrite($this->server, 'DELE ' . $id . "\r\n");
        $this->response = trim(fgets($this->server, 512));
        return $this->isOk();
    }

    // send RSET command to server
    public function rset()
    {
        fwrite($this->server, 'RSET' . "\r\n");
        $this->response = trim(fgets($this->server, 512));
        return $this->isOk();
    }

    // send QUIT command to server
    public function quit()
    {
        fwrite($this->server, 'QUIT' . "\r\n");
        $this->response = trim(fgets($this->server, 512));
        fclose($this->server);
        return $this->isOk();
    }
}
?>
