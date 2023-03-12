<template>
    <div>
        <div>
            <div id="grid">
                <div class="item-grid1">
                    <MultiselectProduct :index="index"/>
                </div>
                <div class="item-grid9">
                    <MultiselectPriceList :index="index"/>
                </div>
                <div class="item-grid2">
                    <label>Código</label>
                    <p >{{Code}}</p>
                </div>
                <div class="item-grid3">
                    <label class="form--label" for="unit-price-input">P. Unitario</label>
                    <input 
                        class="form--input" 
                        :class="{'isUserOfSale':isUserOfSale}"
                        :disabled="isUserOfSale"
                        type="text" 
                        id="unit-price-input"
                        v-model="unit_price"
                        @focus="$event.target.select()"
                        @blur="isDecimal($event)"
                        @keypress="isDecimal($event)">
                    <p class="formulario__input-error"></p>
                </div>
                <div class="item-grid4">
                    <label class="form--label" for="quantity-input">Cantidad</label>
                    <input 
                        autocomplete="off"
                        class="form--input" 
                        type="text" 
                        v-model="quantity"
                        id="quantity-input"
                        ref="quantity_input"
                        @focus="$event.target.select()"
                        @keypress="isDecimal($event)"
                        @blur="chequeo_stock($event)"
                    >
                    
                </div>
                <div class="item-grid11">
                    <label class="form--label" for="mts_by_unity-input">Metros por unidad</label>
                    <!-- <p class="form--input">{{mts_by_unity}}</p> -->
                    <input 
                        class="form--input"
                        :disabled="!IsCHP"
                        :class="{'isDisabled':!IsCHP}"
                        type="text" 
                        ref="mts_by_unity_input"
                        id="mts_by_unity-input"
                        v-model="mts_by_unity"
                        @focus="$event.target.select()"
                        @keypress="isDecimal($event)"
                        @blur="chequeo_stock()"
                        >
                </div>
                <div class="item-grid5">
                    <MultiselectIva :index="index" />
                </div>
                <div class="item-grid12">
                    <label>Importe Neto</label>
                    <p class="form--input">
                        {{neto | currency}}
                    </p>
                </div>
                <div class="item-grid6">
                    <label>Importe Iva</label>
                    <p class="form--input">
                        {{IvaImport | currency}}
                    </p>
                </div>
                <div class="item-grid7">
                    <label class="form--label" for="discount-input">Descuento</label>
                    <input 
                        class="form--input"
                        :class="{'isUserOfSale':isUserOfSale}"
                        type="text" 
                        id="discount-input"
                        v-model="descuento"
                        @focus="$event.target.select()"
                        @keypress="isNumber($event)">
                </div>
                <div class="item-grid8">
                    <label class="form--label" for="total-input">Total</label>
                    <input 
                        class="form--input" 
                        :class="{'isUserOfSale':isUserOfSale}"
                        :disabled="isUserOfSale"
                        type="text" 
                        id="total-input"
                        v-model="total"
                        @focus="$event.target.select()"
                        @blur="isDecimal($event)"
                        @keypress="isDecimal($event)">
                </div>
            </div>
        </div>
        {{CheckSheetMetalStockMessage}}
    </div>
</template>

<script>
export default {
    props : ['index'],

}
</script>

<style >
    .isDisabled {
        color: currentColor;
        cursor: not-allowed;
        opacity: 0.5;
        text-decoration: none;
        pointer-events: none;
    }
</style>