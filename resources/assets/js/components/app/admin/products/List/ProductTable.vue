<template>
    <div>
        <v-client-table
            ref="products_list"
            :columns="columns"
            :data="rows"
            :options="options"
        >
        
        </v-client-table>
        <div class="col-md-12 text-center">
            <paginate
                :page-count="pageCount"
                :click-handler="loadData"
                :prev-text="'Ant.'"
                :next-text="'Sig.'"
                :container-class="'pagination'">
            </paginate>
        </div>
        <BlockUI :message="msg" v-if="loading"></BlockUI>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import { Event } from 'vue-tables-2';
import Paginate from 'vuejs-paginate';
import StatusInfo from './StatusInfo.vue';
import EditAndTrashButton from './EditAndTrashButton.vue';
import Attribues from './Attributes.vue';
import row_number from '../../../publications/partials/row-number.vue';
    export default {
        
        components : {Paginate},

        data(){
            return {
                msg : 'Buscando datos...',
                loading : false,
                pageCount : 1,
                page : 1,
                columns : [
                    'number',
                    'code',
                    'name',
                    'attributes',
                    'critical_stock',
                    'stock',
                    'edit',
                ],
                rows : [],
                options: {
                    orderBy: { column: false },
                    sortable: [],
                    uniqueKey : 'id',
                    perPage : 31,
                    pagination: { dropdown:false },
                    headings: {
                        number : '#',
                        code : 'Código',
                        name : 'Nombre',
                        attributes : 'Atributos',
                        critical_stock : 'Stock crítico',
                        stock : 'Stock',
                        edit : 'Acción',
                    },
                    templates: {
                        number : row_number,
                        attributes : Attribues,
                        edit : EditAndTrashButton,
                    },
                    columnsClasses: {
                        stock : 'col-md-1 text-center',
                        critical_stock : 'col-md-1 text-center',
                        number : 'col-md-1 text-center',
                        code : 'col-md-2 text-left',
                        name : 'col-md-5 text-left',
                        attributes : 'col-md-2 text-left',
                    },
                    filterable: false,
                    cellClasses:{
                        number: [
                            {
                                class:'row-danger',
                                condition: row => row.isCriticalStock
                            }
                        ],
                        code: [
                            {
                                class:'row-danger',
                                condition: row => row.isCriticalStock
                            }
                        ],
                        name: [
                            {
                                class:'row-danger',
                                condition: row => row.isCriticalStock
                            }
                        ],
                        attributes: [
                            {
                                class:'row-danger',
                                condition: row => row.isCriticalStock
                            }
                        ],
                        critical_stock: [
                            {
                                class:'row-danger',
                                condition: row => row.isCriticalStock
                            }
                        ],
                        stock: [
                            {
                                class:'row-danger',
                                condition: row => row.isCriticalStock
                            }
                        ],
                        edit: [
                            {
                                class:'row-danger',
                                condition: row => row.isCriticalStock
                            }
                        ],
                    }

                } 
            }
        },

        computed : {
            ...mapGetters([
                'ListProductsGetter'
            ])
        },

        watch : {

            ListProductsGetter(n)
            {
                this.rows = n;
            }

        },

        methods : {

            async loadData(page=1){

                this.loading = true;

                const payload = {
                    page : page,
                    token : this.User.token
                }
            
                const response = await this.$store.dispatch('loadData', payload)
                .finally(() => this.loading = false);

                if (response) {
                    this.pageCount = response.data.pagination.last_page;
                    this.$store.commit('ADD_PRODUCTS_TO_LIST_PRODUCTS', response.data.data);
                }

            }
        },

        mounted(){

            this.loadData();

            Event.$on('delete_product_from_data_base', (data) => {
                console.log("🚀 ~ file: PedidoListTable.vue:725 ~ Event.$on ~ data", data)
                const ind = this.$refs.products_list.tableData.findIndex(row => {
                    return row.id == data.id;
                });
                this.$refs.products_list.tableData.splice(ind, 1);
            });
        }
       
    }
</script>
<style>
    .row-danger{
        background-color: pink;
        color: aliceblue;
        font-weight: bold;
    }
</style>
