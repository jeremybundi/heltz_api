<?php

use Phalcon\Mvc\Controller;

class LoginController extends Controller
{
  public function indexAction()
  {
    // Get request body as JSON string
    $json = $this->request->getRawBody();

    // Decode JSON string (assuming proper Content-Type header)
    $data = json_decode($json, true); // Set second argument to true for associative array

    if (isset($data['username'])) {
      $username = $data['username'];

      // Find user by username
      $user = Users::findFirst([
        'conditions' => 'username = :username:',
        'bind' => ['username' => $username],
      ]);

      if ($user) {
        // Verify password using password_verify()
        if (password_verify($data['password'], $user->password)) {
          // Login successful, create session (replace with your actual session management)
          $this->session->set('user_id', $user->id);

          $this->response->setStatusCode(200, 'Login successful');
          $data = ['success' => true, 'message' => 'Login successful']; // Add success property
          $this->response->setJsonContent($data);
        } else {
          $this->response->setStatusCode(401, 'Invalid credentials');
          $data = ['success' => false, 'message' => 'Invalid credentials'];
          $this->response->setJsonContent($data);
        }
      } else {
        $this->response->setStatusCode(401, 'User not found');
        $data = ['success' => false, 'message' => 'User not found'];
        $this->response->setJsonContent($data);
      }
    } else {
      // Handle missing username in request
      $this->response->setStatusCode(400, 'Invalid request format');
      $data = ['success' => false, 'message' => 'Missing username in request body'];
      $this->response->setJsonContent($data);
      return $this->response;
    }

    return $this->response;
  }
}
