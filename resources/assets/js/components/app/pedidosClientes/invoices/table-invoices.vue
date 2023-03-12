<template>
    <table class="table">
        <caption>COMPROBANTES ASOCIADOS AL PEDIDO</caption>
        <thead>
            <tr>
                <th style="width: 5%" class="text-center">#</th>
                <th style="width: 45%" class="text-center">Comprobante</th>
                <th style="width: 10%" class="text-center">Fecha</th>
                <th style="width: 20%" class="text-center">Número</th>
                <th style="width: 20%" class="text-center">Importe</th>
                <th style="width: 20%" class="text-center">Imprimir</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(invoice, index) in this.PedidoListChildRowReactivityData.invoices" :key="index">
                <td style="width: 5%" class="text-right"> {{ index + 1 }}</td>
                <td style="width: 40%" class="text-left" >{{invoice.voucher}}</td>
                <td style="width: 10%" class="text-center">{{invoice.date}}</td>
                <td style="width: 15%" class="text-center">{{ invoice.number }}</td>
                <td style="width: 25%" class="text-right">{{ invoice.import | currency}}</td>
                <td style="width: 15%" class="text-right">
                    <printButton :invoice="invoice" />
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import { mapGetters } from 'vuex';
    import printButton from './print-button.vue';
    export default {

        components: {printButton},

        computed: {

            ...mapGetters(['PedidoListChildRowReactivityData'])
        }
    }
</script>

<style lang="scss" scoped>

</style>