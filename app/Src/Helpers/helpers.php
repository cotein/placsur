<?php

use App\Src\Models\PedidoCliente;
use App\Src\Models\PedidoClienteHistory;
use App\Src\Models\Product;
use App\Src\Models\ProductHistory;

function pedido_cliente_history(PedidoCliente $pedidoCliente, $log_name, $user_id, $data = null)
{
    $pd = new PedidoClienteHistory;
    $pd->pedido_cliente_id = $pedidoCliente->id;
    $pd->log_name = strtoupper( $log_name );
    $pd->status_id = $pedidoCliente->status_id;
    $pd->user_id = $user_id;
    $pd->data = $data;
    $pd->save();
}

function product_history(Product $product, $log_name, $user_id, $data = null)
{
    $product_history = new ProductHistory;
    $product_history->product_id = $product->id;
    $product_history->log_name = strtoupper( $log_name );
    $product_history->status_id = $product->status_id;
    $product_history->user_id = $user_id;
    $product_history->data = $data;
    $product_history->save();   
}

