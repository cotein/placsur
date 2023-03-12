<template>
<div>
    <table id="product-list" class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>P.Unit.</th>
                <th>Importe</th>
                <th>IVA</th>
                <th>Total</th>
                <th>Borrar</th>
            </tr>
        </thead>
        <tbody>
            <template v-if="PedidoListChildRowReactivityData && PedidoListChildRowReactivityData.items">
                <tr v-for="(item, index) in PedidoListChildRowReactivityData.items" :key="index" >
                    <td class="text-center">{{index + 1}}</td>
                    <td class="text-left">{{
                            (item.isCHP) 
                                ? `${item.product_name} - ${(parseFloat(item.real_mts) / parseFloat(item.quantity)).toFixed(2)} mts. x unid.`
                                : item.product_name
                        }}

                    </td>
                    <td class="text-center" ><small>{{
                        (item.isCHP) 
                            ? `${item.quantity} unid. ${item.mts_to_invoiced} mts.` 
                            : item.quantity
                    }}</small></td>
                    <!-- <td class="text-right">{{(item.isCHP) ? item.mts_to_invoiced : item.quantity | currency}}</td> -->
                    <td class="text-right">{{item.unit_price | currency}}</td>
                    <td class="text-right">{{item.neto_import | currency}}</td>
                    <td class="text-right">{{item.iva_import | currency}}</td>
                    <td class="text-right">{{item.total_raw  | currency}}</td>
                    <td class="text-center"><PedidoClienteProductsDeleteProduct :index="index" :data="data" :tooltip="'Eliminar producto'" /> </td>
                </tr>
            </template>
        </tbody>
        <!-- <tfoot>
            {{pp.www}}
        </tfoot> -->
    </table>
    <div class="flex">
            <div class="item-1"><span>Neto:</span> {{Neto | currency}}</div>
            <div class="item-2"><span>Iva:</span> {{Iva | currency}}</div>
            <div class="item-2"><span>Descuento:</span> {{Discount | currency}}</div>
            <div class="item-2" v-if="data.shipping"><span>Ad. envío:</span> {{data.shipping.value | currency}}</div>
            <div class="item-2"><span>Total:</span> {{Total | currency}}</div>
    </div>
</div>
</template>

<script>
import collect from 'collect.js';
import { mapGetters } from 'vuex';
import { Event } from 'vue-tables-2';
import PedidoClienteProductsDeleteProduct from './PedidoClienteProductsDeleteProduct.vue'
export default {

    props : ['data'],

    components : {PedidoClienteProductsDeleteProduct},

    //inject: ['pp'],

    computed : {

        ...mapGetters([
            'PedidoListChildRowReactivityData'
        ]),

        Neto(){
            console.log("🚀 ~ file: PedidoClienteProductsDetail.vue ~ line 76 ~ Neto ~ this.PedidoListChildRowReactivityData.items", this.PedidoListChildRowReactivityData.items)
            return collect(this.PedidoListChildRowReactivityData.items).sum('neto_import');
        },

        Iva(){
            return collect(this.PedidoListChildRowReactivityData.items).sum('iva_import');
        },

        Discount(){

            let discount = collect(this.PedidoListChildRowReactivityData.items).sum('discount');
            
            if (this.PedidoListChildRowReactivityData && this.PedidoListChildRowReactivityData.payment_data.value && this.PedidoListChildRowReactivityData.payment_data.value != null) {
                return discount + parseFloat(this.PedidoListChildRowReactivityData.payment_data.value);
            }

            return discount;
        },

        Shipping(){

            if (this.PedidoListChildRowReactivityData.shipping) {
                return parseFloat(this.PedidoListChildRowReactivityData.shipping.value);
            }

            return 0;
        },

        Total(){
            return this.Neto + this.Iva + this.Discount + this.Shipping;
        }

    },
   
}
</script>

<style scoped>
    .flex{
        display: flex;
        justify-content: space-between;
        background-color: #2c3e50;
        height: 5rem;
        align-items: center;
        margin-top: 2.5rem;
    }
    div[class*="item"]{
        font-size: 1.5rem;
        padding: 2rem;
        color: white;
    }
    div[class*="item"] span{
        font-weight: bold;
        color: white;
    } 
    #product-list thead tr th:nth-child(1),
    #product-list thead tr th:nth-child(2),
    #product-list thead tr th:nth-child(3),
    #product-list thead tr th:nth-child(4),
    #product-list thead tr th:nth-child(5),
    #product-list thead tr th:nth-child(6),
    #product-list thead tr th:nth-child(7),
    #product-list thead tr th:nth-child(8)
    {
        
        background-color: #00bbf0;
        color: aliceblue;
        font-weight: bold;
        vertical-align: middle;
        text-transform: uppercase;
        font-size: 14px !important;
    }
    #product-list thead tr th:nth-child(1),
    #product-list thead tr th:nth-child(8)
    {
        width: 3%;
    }
    #product-list thead tr th:nth-child(2){
        width: 35%;
    }

    #product-list thead tr th:nth-child(3){
        width: 19%;
    }
    #product-list thead tr th:nth-child(4),
    #product-list thead tr th:nth-child(5),
    #product-list thead tr th:nth-child(6),
    #product-list thead tr th:nth-child(7)
    {
        width: 10%;
    }
    
</style>