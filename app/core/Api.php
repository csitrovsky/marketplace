<?php

namespace app\core;

use Exception;

abstract class Api
{

    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     * @var array|false|string[]
     */
    protected $args = array();

    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     * @var mixed|string|null
     */
    protected $endpoint = '';

    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     * @var mixed|string|null
     */
    protected $verb = '';

    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     * @var mixed|string
     */
    protected $method = '';

    /**
     * Property: file
     * Stores the input of the PUT request
     * @var null
     */
    protected $file = null;

    protected $request;

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     * @param $request
     * @throws Exception
     */
    public function __construct($request)
    {
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Content-Type: application/json');
        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method === 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    /**
     * @return false|string
     */
    public function process()
    {
        switch ($this->method) {
            case 'GET':
                $this->request = (object)$this->_clean_inputs($_GET);
                break;
            case 'DELETE':
            case 'POST':
                $this->request = (object)$this->_clean_inputs($_POST);
                break;
            case 'PUT':
                $this->request = (object)$this->_clean_inputs($_GET);
                $this->file = file_get_contents('php://input');
                break;
            default:
                $this->_response('Invalid Method', 405);
                break;
        }
        if ((int)method_exists($this, $this->endpoint) > 0) {
            return $this->_response(
                $this->{$this->endpoint}($this->args),
                200
            );
        }
        return $this->_response(
            'No Endpoint: '. $this->endpoint . '...',
            404
        );
    }

    /**
     * @param $data
     * @return array|string
     */
    private function _clean_inputs($data)
    {
        $clean_input = [];
        if (!is_array($data)) {
            $clean_input = trim(strip_tags((string)$data));
        }
        else {
            foreach ($data as $key => $value) {
                $clean_input[$key] = $this->_clean_inputs($value);
            }
        }
        return $clean_input;
    }

    /**
     * @param $message
     * @param int $code
     * @return false|string
     */
    protected function _response($message, int $code = 200)
    {
        header('HTTP/1.1 ' . $code . ' ' . $this->_request_status($code));
        return json_encode($message, JSON_PRETTY_PRINT);
    }

    /**
     * @param $code
     * @return string
     */
    private function _request_status($code): string
    {
        $status = [
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ];
        return ($status[$code]) ?: $status[500];
    }

    /**
     * @param array $object
     * @param string $message
     * @return mixed|string
     */
    public function success(string $message, array &$object = [
        'success'   => false,
        'code'      => 404
    ])
    {
        if ($message) {
            return (object)$object['message'] = $message;
        }
        return (object)$object;
    }

}