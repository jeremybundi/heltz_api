<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class ControllerBase extends Controller
{
    protected function setCorsHeaders()
    {
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
    }

    public function beforeExecuteRoute($dispatcher)
    {
        $this->setCorsHeaders();

        if ($this->request->isOptions()) {
            $this->response->setStatusCode(200, 'OK');
            $this->response->send();
            exit;
        }
    }
}
