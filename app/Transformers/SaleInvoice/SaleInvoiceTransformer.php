<?php

namespace App\Transformers\SaleInvoice;

use App\Src\Models\SaleInvoice;
use App\Src\Traits\ZeroLeftTrait;
use App\Src\Traits\DateFormatTrait;
use League\Fractal\TransformerAbstract;

class SaleInvoiceTransformer extends TransformerAbstract
{
    use ZeroLeftTrait, DateFormatTrait;

    public function transform(SaleInvoice $si) :array
    {
        $neto = 0;

        if ($si->pedidos()->exists()) {
            $pedido = $si->pedidos->first();
            if ($pedido->payment_type_aditional()->exists()) {
                $payment_type_aditional = $pedido->payment_type_aditional->value;
            }
        }else{
            $payment_type_aditional = 0;
        }
        
        $neto = $si->items->sum('neto_import') + $payment_type_aditional + $si->percerp_iibb;

        return [
            'customer_name' => $si->customer->name,
            'name' => $si->voucher->name,
            'number' => "{$this->zeroLeft($si->pto_vta, 5)} - {$this->zeroLeft($si->cbte_desde, 8)}",
            'date' => $si->cbte_fch,
            'cae' => ($si->cae) ? $si->cae : ' --- ',
            'neto' => $neto,
        ];
    }
}
