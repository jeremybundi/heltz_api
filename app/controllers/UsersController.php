<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class UsersController extends Controller
{
    private function setResponseHeaders(Response $response)
    {
        $response->setHeader('Access-Control-Allow-Origin', '*');
        //$response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        //$response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        //$response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setContentType('application/json', 'utf-8');
    }

    public function postAction()
    {
        $requestData = $this->request->getJsonRawBody();
        $response = new Response();

        // Set headers
        $this->setResponseHeaders($response);

        // Data validation
        if (!isset($requestData->name) || !isset($requestData->email)) {
            $response->setStatusCode(422, 'Unprocessable Entity');
            $response->setJsonContent(["error" => "Name and email are required"]);
            return $response;
        }

        // Create a new User
        $user = new Users();
        $user->name = $requestData->name;
        $user->phone = $requestData->phone;
        $user->email = $requestData->email;
        $user->username = $requestData->username;
        $user->password = $this->security->hash($requestData->password);

        if ($user->save()) {
            $response->setStatusCode(201, 'Created');
            $response->setJsonContent(["message" => "User added successfully"]);
        } else {
            $response->setStatusCode(500, 'Internal Server Error');
            $messages = [];
            foreach ($user->getMessages() as $message) {
                $messages[] = $message->getMessage();
            }
            $response->setJsonContent(["error" => "Failed to add user", "details" => $messages]);
        }

        $response->setStatusCode(200, 'success');

        return $response->send();
    }
}
