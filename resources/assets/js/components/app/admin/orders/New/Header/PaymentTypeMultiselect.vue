
<script>
    import { mapActions, mapGetters } from 'vuex';
    import PaymentMultiselectBase from './../../../paymentType/PaymentTypeMultiselectBase.vue';
    export default {

        extends : PaymentMultiselectBase,

        data() {
            return {
                show_spinner : false,
            }
        },

        methods : {

            ...mapActions(['updatePaymentTypeValue']),

            updatePaymentTypeValue(el){
                this.$store.commit('NEW_ORDER_SET_PAYMENT_TYPE', el);
            }
        },

        computed : {

            ...mapGetters([
                'PaymentTypesGetter',
                'PaymentTypeGetter',
                'NewOrderPaymentTypeGetter',
                'NewOrderTotalsNeto'
            ]),

            PaymentTypeGetter(){
                return this.NewOrderPaymentTypeGetter;
            }
        },

        watch : {

            NewOrderPaymentTypeGetter : {

                deep : true,

                handler(value){
                    let aditional_value = parseFloat(this.NewOrderTotalsNeto) * parseFloat(value.percentage) / 100;

                    this.$store.commit('NEW_ORDER_SET_PAYMENT_ADITIONAL_VALUE', aditional_value);
                }
            }

        }
    }
</script>
<style scoped>
    .multiselect {
        width: 100%;
    }
</style>
