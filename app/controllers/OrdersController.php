<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class OrdersController extends ControllerBase
{    
    //get
    public function indexAction()
    {
        $orderNumber = $this->request->getQuery('orderNumber', 'int');
        $startDate = $this->request->getQuery('startDate');
    
        // Create the base query
        $query = Orders::query()
            ->columns([
                'Orders.id',
                'Orders.customerName',
                'Orders.phoneNumber',
                'Orders.deliveryAddress',
                'Orders.paymentMethod',
                'Orders.totalPrice',
                'Orders.createdAt',
                'Items.item_url',
                'Items.item_name',
                'Items.details',
                'OrderItems.quantity'
            ])
            ->join('OrderItems', 'OrderItems.orderId = Orders.id')
            ->join('Items', 'Items.id = OrderItems.itemId');
    
        
        if ($orderNumber) {
            $query->where('Orders.id = :orderNumber:', ['orderNumber' => $orderNumber]);
        }
    

        if ($startDate) {
            $query->andWhere('Orders.createdAt >= :startDate:', ['startDate' => $startDate]);
        }
    
        // Execute the query
        $orders = $query->execute();
    
        $ordersArray = [];
    
        foreach ($orders as $order) {
            // Check if the order ID already exists in the array
            if (!isset($ordersArray[$order->id])) {
                $ordersArray[$order->id] = [
                    'id' => $order->id,
                    'customerName' => $order->customerName,
                    'phoneNumber' => $order->phoneNumber,
                    'deliveryAddress' => $order->deliveryAddress,
                    'paymentMethod' => $order->paymentMethod,
                    'totalPrice' => $order->totalPrice,
                    'createdAt' => $order->createdAt,
                    'items' => [] // Initialize items array
                ];
            }
    
            // Append the item to the items array of the order
            $ordersArray[$order->id]['items'][] = [
                'item_url' => $order->item_url,
                'item_name'=> $order->item_name,
                'details' => $order->details,
                'quantity' => $order->quantity
            ];
        }
         
        // Remove the keys so that the array is reindexed
        $ordersArray = array_values($ordersArray);
    
        // Return the response in JSON format
        $this->response->setJsonContent($ordersArray);
        return $this->response;
    }
    
    //post
    public function createAction()
    {
        $response = new Response();

        if ($this->request->isPost()) {
            $orderData = $this->request->getJsonRawBody();
            $order = new Orders();

            // Assign fields to the order model
            $order->customerName = $orderData->customerName;
            $order->phoneNumber = $orderData->phoneNumber;
            $order->deliveryAddress = $orderData->deliveryAddress;
            $order->paymentMethod = $orderData->paymentMethod;
            $order->totalPrice = $orderData->totalPrice;

            // Begin transaction
            $this->db->begin();

            if ($order->save()) {
                // Save each order item
                foreach ($orderData->items as $itemData) {
                    $orderItem = new OrderItems();
                    $orderItem->orderId = $order->id;
                    $orderItem->itemId = $itemData->itemId;
                    $orderItem->quantity = $itemData->quantity;

                    if (!$orderItem->save()) {
                        // Rollback transaction if an item fails to save
                        $this->db->rollback();
                        // Handle errors
                        $errors = [];
                        foreach ($orderItem->getMessages() as $message) {
                            $errors[] = $message->getMessage();
                        }

                        $response->setJsonContent([
                            'status' => 'error',
                            'messages' => $errors
                        ]);
                        return $response;
                    }
                }

                // Commit transaction
                $this->db->commit();

                $response->setJsonContent([
                    'status' => 'success',
                    'message' => 'Order and order items have been created successfully'
                ]);
            } else {
                // Handle errors
                $errors = [];
                foreach ($order->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }

                $response->setJsonContent([
                    'status' => 'error',
                    'messages' => $errors
                ]);
            }
        } else {
            $response->setJsonContent([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
        }

        return $response;
    }
}
