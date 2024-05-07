
<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class ItemsController extends Controller
{
    public function indexAction()
    {
        // Get items
        $itemsModel = new Items();
        $allItems = $itemsModel->getAllItems();

        $itemData = [];
        foreach ($allItems as $item) {
            $itemData[] = [
               
                "item_name" => $item->item_name,
                "item_url" => $item->item_url,
                "details" => $item->details,
                "price" => $item->price,
                "id" =>$item ->id
            ];
        }

        $response = new Response();
        $response->setStatusCode(200, 'OK');
        $response->setContentType('application/json');
        $response->setHeader('Access-Control-Allow-Origin', '*'); // Allow requests from any origin (for development)
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
        $response->setJsonContent(["status" => true, "data" => $itemData]);

        return $response;
    }

    public function createAction()
{
    $requestData = $this->request->getJsonRawBody();

    // Data validation: Check if required fields are present
    if (!isset($requestData->item_name) || !isset($requestData->price)) {
        $response = new Response();
        $response->setStatusCode(400); // Bad Request
        $response->setHeader('Access-Control-Allow-Origin', '*'); // Allow requests from any origin (for development)
        $response->setJsonContent(["error" => "Invalid data"]);
        return $response;
    }

    // Create a new item
    $newItem = new Items();
    $newItem->item_name = $requestData->item_name;
    $newItem->item_url = $requestData->item_url;
    $newItem->details = $requestData->details;
    $newItem->price = $requestData->price;

    // Save the item to the database
    if ($newItem->save()) {
        // Item saved successfully
        $response = new Response();
        $response->setStatusCode(201); 
        $response->setContentType('application/json');// Created
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        
        $response->setJsonContent(["message" => "Item added successfully"]);
        return $response;
    } else {
        // Handle database save errors
        $response = new Response();
        $response->setStatusCode(500);
        $response->setContentType('application/json');// Created
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        
        $response->setJsonContent(["error" => "Failed to save item"]);
        return $response;
    }
}

}
