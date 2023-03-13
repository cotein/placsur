<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Src\Models\Product;
use App\Src\Models\SaleInvoiceItem;
use App\Transformers\Gains\GainsTransformer;

class GainsController extends Controller
{
    protected function findProductAtAcc($saleInvoiceItem, $array){

        $sale_price = $this->salePrice($saleInvoiceItem);

        foreach ( $array as $key => $pr ) {
            if ( (int)$pr['product_id'] === (int)$saleInvoiceItem->product_id && (float)$pr['sale_price'] === (float)$sale_price ) {
                return $key;
            }
        }

        return false;
    }

    protected function salePrice($saleInvoice)
    {
        $sale_price = 0;

        if ($saleInvoice->isCHP) {
            $sale_price = $saleInvoice->neto_import / $saleInvoice->mts_to_invoiced;
        }else{
            $sale_price = $saleInvoice->neto_import / $saleInvoice->quantity;
        }

        return $sale_price;
    }

    protected function quantity($saleInvoice)
    {
        return ($saleInvoice->isCHP) ? (float)$saleInvoice->mts_to_invoiced : (int)$saleInvoice->quantity;
    }

    public function index()
    {
        $sales = SaleInvoiceItem::query();

        $sales = $sales->where('voucher_id', '=', 88);

        if (request()->has('product_id')) {
            $sales = $sales->where('product_id', (int) request()->product_id);
        }
        
        if (!request()->from) {
            throw new \Exception('El dato Fecha Desde es obligatorio', 431);
        }
        if (!request()->to) {
            throw new \Exception('El dato Fecha Hasta es obligatorio', 431);
        }

        $sales = $sales->whereBetween('created_at', [request()->from, request()->to]);

        $sales = $sales->where('costo', '>', 0);
      
        $sales = $sales->orderBy('product_id')->paginate(31);

        $list = $sales->reduce( function ($acc, $saleInvoice){

            $sale_price = $this->salePrice($saleInvoice);

            $index = $this->findProductAtAcc($saleInvoice, $acc);

            $quantity = $this->quantity($saleInvoice);

            if (($index) || $index === 0) {
                $acc[$index]['quantity'] = (float)$acc[$index]['quantity'] + (float)$quantity;
                $acc[$index]['neto'] = (float)$acc[$index]['neto'] + (float)$saleInvoice->neto_import;
                $acc[$index]['earning'] = (float)$acc[$index]['earning'] + (float)$saleInvoice->earning;
            }else{
                $product = null;

                if (is_null($product)) {
                    $product = Product::find($saleInvoice->product_id);
                }else{
                    if (! (int)$product->id === (int)$saleInvoice->product_id) {
                        $product = Product::find($saleInvoice->product_id);
                    }
                }
                array_push($acc, [
                    'id' => time() + rand(10,100),
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'code' => $product->code,
                    'sale_price' => $sale_price,
                    'quantity' => $quantity,
                    'cost' => $saleInvoice->costo,
                    'neto' => $saleInvoice->neto_import,
                    'earning' => $saleInvoice->earning,
                    'isCHP' => $saleInvoice->isCHP
                ]);
            }

            return $acc;

        }, []);

        $response = [
            'pagination' => [
                'total' => $sales->total(),
                'per_page' => $sales->perPage(),
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'from' => $sales->firstItem(),
                'to' => $sales->lastItem()
            ],
            'data' => $list,
        ];

        return response()->json($response, 200);


    }
}
