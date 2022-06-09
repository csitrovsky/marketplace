<?php

namespace http\api;

use app\core\Api;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Marketplace extends Api
{

    public function __construct($request, $origin)
    {
        parent::__construct($request);
    }

    /**
     * Example of an Endpoint
     * @return object|string
     * @throws Exception
     */
    public function example()
    {
        $result = [];
        if ($this->verb !== 'get') {
            return 'Only accepts GET requests';
        }
        switch (array_shift($this->args)) {
            case 'wildberries':
                try {
                    if (!($action = $this->request->action)) {
                        throw new Exception('Unknown command');
                    }
                    $date = $this->request->date ?: date('Y-m-d');
                    $params = [
                        'dateFrom'  => $date,
                        'key'       => ''
                    ];
                    if ($action === 'reportDetailByPeriod') {
                        $params['dateTo'] = date('Y-m-d');
                    }
                    $response = (new Client())->get(
                        '' . $action, [
                            'query' => $params
                        ]
                    );
                    $response_contents = $response->getBody()->getContents();
                    $response_contents = json_decode($response_contents, true);
                    $result = $this->success(
                        ((empty($response_contents)) ? 'No data available' : ''),
                        $response_contents
                    );
                } catch (Exception $exception) {
                    $result = array(
                        'success'   => false,
                        'code'      => 404,
                        'message'   => 'Request limit exceeded'
                    );
                } catch (GuzzleException $exception) {
                    $result = [
                        'success'   => false,
                        'code'      => 404,
                        'Request limit exceeded',
                        ['error' => $exception->getMessage()]
                    ];
                }
                break;
            case 'yandex':
            case 'ozon':
                break;
        }
        return (object)$result;
    }

}
