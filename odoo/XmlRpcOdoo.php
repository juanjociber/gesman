<?php
    header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Access-Control-Allow-Methods: POST');

    require_once($_SERVER['DOCUMENT_ROOT'].'/mycloud/library/Odoo-REST-API-master/ripcord.php');


// Configuración
$url = 'https://gpem.icreat.pe';
$db = 'gpem';
$username = 'sistemas2023@mail.com';
$password = 'SysGp3m2023';

// URLs para los métodos XML-RPC
$common_url = $url . '/xmlrpc/2/common';
$models_url = $url . '/xmlrpc/2/object';

// Paso 1: Autenticación
function authenticate($common_url, $db, $username, $password) {
    $params = [
        $db,
        $username,
        $password,
        []
    ];

    $response = xmlrpc_request($common_url, 'authenticate', $params);

    return $response;
}

// Realiza una solicitud XML-RPC
function xmlrpc_request($url, $method, $params) {
    $xmlrpc = xmlrpc_encode_request($method, $params);
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: text/xml',
            'content' => $xmlrpc
        ]
    ]);

    $response = file_get_contents($url, false, $context);
    return xmlrpc_decode($response);
}

// Paso 2: Autenticarse con Odoo
$uid = authenticate($common_url, $db, $username, $password);

// Verificar que la autenticación fue exitosa
if ($uid) {
    echo "Autenticación exitosa! UID: " . $uid . "\n";
} else {
    echo "Error de autenticación.\n";
    exit;
}

// Paso 3: Crear la orden de venta con productos en una sola solicitud
function create_sale_order_with_lines($models_url, $db, $uid, $password, $partner_id, $products) {
    // Datos de la orden y líneas de productos
    $order_data = [
        'partner_id' => $partner_id,  // Cliente
        'date_order' => date('Y-m-d H:i:s'),  // Fecha de la orden
        'state' => 'draft',  // Estado de la orden
        'order_line' => []  // Inicializamos el array de líneas de productos
    ];

    // Añadir líneas de productos a la orden
    foreach ($products as $product) {
        $order_data['order_line'][] = [
            'product_id' => $product['product_id'],  // ID del producto
            'product_uom_qty' => $product['quantity'],  // Cantidad
            'price_unit' => $product['price_unit'],  // Precio unitario
        ];
    }

    // Crear la orden de venta
    $params = [
        $db,
        $uid,
        $password,
        'sale.order',
        'create',
        [$order_data]  // Pasamos todos los datos de la orden
    ];

    $response = xmlrpc_request($models_url, 'execute_kw', $params);
    return $response;
}

// Datos del cliente (partner)
$partner_id = 70;  // El ID del cliente (partner)

// Productos para la orden (puedes agregar más productos si lo deseas)
$products = [
    [
        'product_id' => 7260,  // ID del producto
        'quantity' => 2,    // Cantidad
        'price_unit' => 100 // Precio unitario
    ],
    [
        'product_id' => 5389,  // Otro producto (ID 2)
        'quantity' => 1,
        'price_unit' => 150
    ]
];

// Crear la orden de venta con las líneas de productos en una sola operación
$order_id = create_sale_order_with_lines($models_url, $db, $uid, $password, $partner_id, $products);

print_r($order_id);

?>