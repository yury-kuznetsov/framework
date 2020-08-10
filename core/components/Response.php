<?php

namespace core\components;

use core\base\Component;

class Response extends Component
{
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_RAW  = 'raw';

    /**
     * @var string
     */
    public $format = self::FORMAT_HTML;
    /**
     * @var string
     */
    public $charset = 'UTF-8';
    /**
     * @var mixed
     */
    public $data;
    /**
     * @var mixed
     */
    public $content;
    /**
     * @var array
     */
    public static $httpStatuses = [
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
    ];

    /**
     * @var int
     */
    private $_statusCode = 200;
    /**
     * @var string
     */
    private $_statusText = 'OK';
    /**
     * @var array
     */
    private $_headers;


    /**
     * Sends the response.
     */
    public function send()
    {
        $this->prepare();
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * Prepares the response.
     */
    protected function prepare()
    {
        if ($this->format == self::FORMAT_HTML) {
            $this->setHeader('Content-Type', 'text/html; charset=' . $this->charset);
            if ($this->data !== null) {
                $this->content = $this->data;
            }
        } elseif ($this->format == self::FORMAT_JSON) {
            if ($this->data !== null) {
                $this->content = json_encode($this->data, 320);
            }
        } elseif ($this->format == self::FORMAT_RAW) {
            if ($this->data !== null) {
                $this->content = $this->data;
            }
        }
    }

    /**
     * Sends the response headers.
     */
    protected function sendHeaders()
    {
        $headers = $this->getHeaders();
        if (count($headers) > 0) {
            foreach ($headers as $name => $values) {
                $replace = true;
                foreach ($values as $value) {
                    header("$name: $value", $replace);
                    $replace = false;
                }
            }
        }

        $version = $this->getVersion();
        $statusCode = $this->getStatusCode();
        $statusText = $this->getStatusText();
        header("HTTP/{$version} {$statusCode} {$statusText}");
    }

    /**
     * Sends the response content.
     */
    protected function sendContent()
    {
        echo $this->content;
        return;
    }

    /**
     * Returns HTTP-protocol version.
     *
     * @return string
     */
    public function getVersion()
    {
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
            return '1.0';
        } else {
            return '1.1';
        }
    }

    /**
     * Sets the response status code.
     *
     * @param int  $value
     * @param null $text
     *
     * @return $this
     */
    public function setStatusCode($value, $text = null)
    {
        $this->_statusCode = is_null($value) ? 200 : (int) $value;

        if ($text === null) {
            if (isset(static::$httpStatuses[$this->_statusCode])) {
                $this->_statusText = static::$httpStatuses[$this->_statusCode];
            } else {
                $this->_statusText = '';
            }
        } else {
            $this->_statusText = $text;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * @return string
     */
    public function getStatusText()
    {
        return $this->_statusText;
    }

    /**
     * Set header value.
     *
     * @param string $key
     * @param string $value
     */
    public function setHeader($key, $value)
    {
        if (!isset($this->_headers[$key])) {
            $this->_headers[$key] = [];
        }
        $this->_headers[$key][] = $value;
    }

    /**
     * Returns headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Redirects the browser to the URL.
     *
     * @param string $url
     * @param int    $statusCode
     *
     * @return $this
     */
    public function redirect($url, $statusCode = 302)
    {
        $this->setHeader('Location', $url);
        $this->setStatusCode($statusCode);

        return $this;
    }
}