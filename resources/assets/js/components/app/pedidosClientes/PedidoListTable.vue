<template>
    <div class="wrapper-pedido-list-table">
        <loading 
            :active.sync="loading" 
            color="#0469c7"
            :can-cancel="false" 
            :is-full-page="true">
        </loading>
        <div class="row">
            <div class="form form-inline" >
                <div class="form-group col-md-4" style="padding-bottom-15px">
                    <multiselect placeholder="Buscar Cliente" 
                        id="findCustomer"
                        track-by="name" label="name"
                        :loading="show_spinner_multiselect"
                        v-model="customer"
                        :options="customers"
                        :searchable="true"
                        :internal-search="false" 
                        :clear-on-select="true" 
                        @search-change="asyncFind"
                        @select="selectedCustomer"
                        @remove="removeCustomer"
                        selectLabel="Seleccionar"
                        selectedLabel="Seleccionado"
                        deselectLabel="Quitar"
                        >
                        <span slot="noOptions">
                                Lista Vacía
                        </span>
                        <span slot="noResult">
                                La búsqueda no arrojó resultados
                        </span>
                    </multiselect>
                </div>
                <div class="form-group col-md-3" style="padding-bottom-15px">
                    <multiselect placeholder="Estados" 
                        track-by="name" label="name"
                        v-model="status"
                        :options="statuses"
                        @select="selectedStatus"
                        @remove="removeStatus"
                        selectLabel="Seleccionar"
                        selectedLabel="Seleccionado"
                        deselectLabel="Quitar"
                        >
                    </multiselect>
                </div>
                <div class="form-group col-md-3" style="padding-bottom-15px">
                    <multiselect placeholder="N° Pedido / Remito" 
                        track-by="code" label="code"
                        :loading="show_spinner_pedido"
                        v-model="code"
                        :options="codes"
                        @select="selectCode"
                        @remove="removeCode"
                        :searchable="true"
                        :internal-search="false" 
                        :clear-on-select="true" 
                        @search-change="asyncFindPedidos"
                        selectLabel="Seleccionar"
                        selectedLabel="Seleccionado"
                        deselectLabel="Quitar"
                        >
                        <span slot="noOptions">
                                Lista Vacía
                        </span>
                        <span slot="noResult">
                                La búsqueda no arrojó resultados
                        </span>
                    </multiselect>
                </div>
                <button 
                    class="btn btn-primary" 
                    type="submit"
                    @click.prevent="loadData()"
                    >Buscar Pedidos
                </button>
            </div>
        </div>
            <v-client-table
                ref="pedido_cliente_list"
                :columns="columns"
                :data="rows"
                :options="options"
            >
            
                <div slot="filter__id">
                    <input type="checkbox" 
                    class="form-control check-all" 
                    v-model='allMarked' 
                    @change="toggleAll()">
                </div>	

                <template slot="id" slot-scope="props">
                    <input type="checkbox" 
                        @change="unmarkAll()" 
                        class="form-control" 
                        :value="props.row.id"
                        :disabled="!props.row.has_delivery_date" 
                        v-model="markedRows"
                    >
                </template>
            
            </v-client-table>
        <div class="text-center">
            <paginate
                :page-count="www"
                :click-handler="loadData"
                :prev-text="'Ant.'"
                :next-text="'Sig.'"
                :container-class="'pagination'">
            </paginate>
        </div>
        
    </div>
</template>

<script>
    
    import {mapGetters} from 'vuex';
    import collect from 'collect.js';
    import {Event} from 'vue-tables-2';
    import Paginate from 'vuejs-paginate';
    import Loading from 'vue-loading-overlay';
    import Multiselect from 'vue-multiselect';
    import Datepicker from 'vuejs-datepicker';
    import CellEdit from './PedidoListCellEdit';
    import {es} from 'vuejs-datepicker/dist/locale';
    import CellStatus from './PedidoListCellStatus';
    import Comanda from './../../../src/Pdf/Comanda';
    import sleep from './../../../mixins/sleep-mixin';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import PedidoListChildRow from './PedidoListChildRow';
    import toast_mixin from './../../../mixins/toast-mixin';
    import * as constants from './../../../src/const/Status';
    import row_number from './../publications/partials/row-number';
    import CellInvoiceGenerate from './PedidoListCellInvoiceGenerate';
    import CustomerEdit_Modal from './../customers/modal/CustomerEdit';
    import CustomerEditModal from './../customers/edit/CustomerEditModal';
    import async_find_customer from './../../../mixins/asyc-find-customer';
    import FindOrdersOrRemitos from './../../../mixins/Orders/FindOrdersOrRemitos';
    export default {

        props : ['paginati'],

        components : {
            Loading, Paginate, Multiselect, Datepicker, CustomerEditModal, CustomerEdit_Modal
        },

        mixins : [async_find_customer, toast_mixin, sleep, FindOrdersOrRemitos],
        
        data() {
            return {
                spinner : false,
                comandaArray : [],
                show : false,
                visible : false,
                date : new Date(),
                status_listated_date_from : null,
                status_listated_date_to : null,
                delivery_date_from : null,
                delivery_date_to : null,
                es : es,
                allMarked:false,
			    markedRows:[],
                www : 1,
                loading : false,
                status : null,
                statuses : [
                   {name : 'TODOS' , status_id : null},
                   {name : 'PENDIENTE' , status_id : constants.PENDIENTE},
                   {name : 'REMITIDO' , status_id : constants.REMITIDO},
                   {name : 'PRESUPUESTADO' , status_id : constants.PRESUPUESTADO},
                   {name : 'FACTURADO' , status_id : constants.FACTURADO},
                   {name : 'PREPARADO' , status_id : constants.PREPARADO},
                   {name : 'RETIRADO' , status_id : constants.RETIRADO},
                   {name : 'DESPACHADO' , status_id : constants.DESPACHADO},
                   {name : 'ENTREGADO' , status_id : constants.ENTREGADO},
                   {name : 'COTIZADO' , status_id : constants.COTIZADO},
                   {name : 'CUMPLIDO' , status_id : constants.CUMPLIDO},

                ],
                columns : [
                    'id',
                    'number',
                    'customer',
                    'created',
                    'delivery_date',
                    'code',
                    'remito_code',
                    'total',
                    'status',
                    'edit',
                ],
                rows : [],
                options: {
                    showChildRowToggler : false,
                    orderBy: { column: false },
                    sortable: [],
                    uniqueKey : 'id',
                    perPage : 31,
                    pagination: { dropdown:false },
                    headings: {
                        id : 'Chk',
                        number : '#',
                        customer : 'Cliente',
                        created : 'Fecha del Pedido',
                        delivery_date : 'Fecha de entrega',
                        code : 'Código',
                        remito_code: 'Remito',
                        total : 'Importe',
                        status : 'Estado',
                        edit : 'Acción',

                    },
                    templates: {
                        number : row_number,
                        edit : CellEdit,
                        delete : CellInvoiceGenerate,
                        status : CellStatus,
                    },
                    columnsClasses: {
                        id : 'text-center',
                        number : 'col-md-1 text-center',
                        customer : 'text-left',
                        created : 'col-md-2 text-center',
                        delivery_date : 'col-md-2 text-center',
                        code : 'col-md-1 text-center',
                        remito_code : 'col-md-1 text-center',
                        total : 'col-md-2 text-right',
                        status : 'col-md-1 text-center',
                        edit : 'col-md-1 text-center',

                    },
                    //groupBy : 'customer',
                    //groupMeta: [],
                    cellClasses:{
                        id: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],
                        number: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],
                        created: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],
                        delivery_date: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],
                        code: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],
                        remito_code: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],
                        total: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],
                        status: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],
                        print: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],
                        edit: [
                            {
                                class:'success',
                                condition: row => (row.now)
                            }
                        ],

                    },
                    toggleGroups : true,
                    showChildRowToggler : true,
                    filterable: false,
                    childRow : PedidoListChildRow,
                } 
            }
        },

        computed : {
            ...mapGetters(
                [
                    'GetPedidos',
                    'GetCustomerPurchaserDocument',
                    'GetCustomersData',
                    'PedidoClientesAddNewAddressGetter'
                ]
            ),

            HasComandaPrint()
            {
                const markedRows = collect(this.markedRows);

                if (markedRows.count() > 0) {
                    return true;
                }

                return false;
            },

            Today(){
                return this.$moment(Date.now())
            },
        },

        watch : 
        {
            status_listated_date_from(n)
            {
                if (this.$moment(n) < this.$moment(this.delivery_date_from)) {
                    this.error_message('Fecha de Entrega DESDE no puede ser menor que Fecha estado listado DESDE', 'Fechas', 4000, 'center');   
                }
            },

            status_listated_date_to(n)
            {
                if(this.status_listated_date_from == null)
                {
                    this.info_message('Fecha desde no puede estar vacio', 'Fechas', 2000, 'center');
                    this.$refs.status_listated_date_to.clearDate();
                    this.status_listated_date_to = null
                }else if (this.$moment(n) < this.$moment(this.status_listated_date_from)) {
                    this.error_message('Fecha HASTA no puede ser menor que fecha DESDE', 'Fechas', 4000, 'center');
                    this.$refs.status_listated_date_to.clearDate();
                    this.status_listated_date_to = null;
                }
            },

            delivery_date_from(n)
            {
                if(this.$moment(n) < this.$moment(this.status_listated_date_from)) {
                    this.error_message('Fecha de entrega DESDE no puede ser menor que Fecha estado listado DESDE', 'Fechas', 4000, 'center');
                    this.$refs.delivery_date_from.clearDate();
                    this.delivery_date_from = null;
                }
            },

            delivery_date_to(n)
            {
                if(this.delivery_date_from == null)
                {
                    this.info_message('Fecha desde no puede estar vacio', 'Fechas', 2000, 'center');
                    this.$refs.delivery_date_to.clearDate();
                    this.delivery_date_to = null
                }else if (this.$moment(n) < this.$moment(this.delivery_date_from)) {
                    this.error_message('Fecha HASTA no puede ser menor que fecha DESDE', 'Fechas', 4000, 'center');
                    this.$refs.delivery_date_to.clearDate();
                }
            },

            GetPedidos(n)
            {
                this.rows = n;

                this.markedRows = [];

                this.rows.map(row => {

                    if (row.has_delivery_date) {
                        this.markedRows.push(row.id);
                    }
                });
            },

            GetCustomerPurchaserDocument(n)
            {
                let ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.customer_id == this.GetCustomersData.id
                });
                this.$refs.pedido_cliente_list.tableData[ind].customer = this.GetCustomersData.name;
                this.$refs.pedido_cliente_list.tableData[ind].customer_document_number = this.GetCustomersData.number;
                this.$refs.pedido_cliente_list.tableData[ind].customer_phone1 = this.GetCustomersData.phone_1;
                this.$refs.pedido_cliente_list.tableData[ind].customer_phone2 = this.GetCustomersData.phone_2;
                this.$refs.pedido_cliente_list.tableData[ind].customer_phone3 = this.GetCustomersData.phone_3;
                this.$refs.pedido_cliente_list.tableData[ind].has_afip_data = true;
                this.$refs.pedido_cliente_list.tableData[ind].customer_DocTipo = this.GetCustomersData.purchaser_document;
                this.$refs.pedido_cliente_list.tableData[ind].customer_inscription_id = this.GetCustomersData.inscription_id;
                this.$refs.pedido_cliente_list.tableData[ind].customer_inscription_name = this.GetCustomersData.inscription;
            },

            GetCustomersData(n)
            {
                this.$refs.pedido_cliente_list.tableData.forEach((row, index) => {
                    if (row.customer_id == this.GetCustomersData.id)
                    {
                        this.$refs.pedido_cliente_list.tableData[index].customer = this.GetCustomersData.name;
                        this.$refs.pedido_cliente_list.tableData[index].customer_document_number = this.GetCustomersData.number;
                        this.$refs.pedido_cliente_list.tableData[index].customer_phone1 = this.GetCustomersData.phone_1;
                        this.$refs.pedido_cliente_list.tableData[index].customer_phone2 = this.GetCustomersData.phone_2;
                        this.$refs.pedido_cliente_list.tableData[index].customer_phone3 = this.GetCustomersData.phone_3;
                        this.$refs.pedido_cliente_list.tableData[index].has_afip_data = this.GetCustomersData.customer_has_afip_data;
                        this.$refs.pedido_cliente_list.tableData[index].customer_has_afip_data = this.GetCustomersData.customer_has_afip_data;
                        this.$refs.pedido_cliente_list.tableData[index].customer_DocTipo = this.GetCustomersData.purchaser_document;
                        this.$refs.pedido_cliente_list.tableData[index].customer_inscription_id = this.GetCustomersData.inscription_id;
                        this.$refs.pedido_cliente_list.tableData[index].customer_inscription_name = this.GetCustomersData.inscription;
                    }
                });
            }
        },

        methods : {

            update_order_data(index, order){
        
                this.$refs.pedido_cliente_list.tableData[index].come_from_meli = order.come_from_meli;
                this.$refs.pedido_cliente_list.tableData[index].comments = order.comments;
                this.$refs.pedido_cliente_list.tableData[index].customer = order.customer;
                this.$refs.pedido_cliente_list.tableData[index].customer_DocTipo = order.customer_DocTipo;
                this.$refs.pedido_cliente_list.tableData[index].customer_DocTipo_id = order.customer_DocTipo_id;
                this.$refs.pedido_cliente_list.tableData[index].customer_DocTipo_afip_code = order.customer_DocTipo_afip_code;
                this.$refs.pedido_cliente_list.tableData[index].customer_document_number = order.customer_document_number;
                this.$refs.pedido_cliente_list.tableData[index].customer_id = order.customer_id;
                this.$refs.pedido_cliente_list.tableData[index].customer_inscription_id = order.customer_inscription_id;
                this.$refs.pedido_cliente_list.tableData[index].customer_inscription_name = order.customer_inscription_name;
                this.$refs.pedido_cliente_list.tableData[index].customer_addresses = order.customer_addresses;
                this.$refs.pedido_cliente_list.tableData[index].customer_cellphone = order.customer_cellphone;
                this.$refs.pedido_cliente_list.tableData[index].customer_phone1 = order.customer_phone1;
                this.$refs.pedido_cliente_list.tableData[index].customer_phone2 = order.customer_phone2;
                this.$refs.pedido_cliente_list.tableData[index].customer_phone3 = order.customer_phone3;
                this.$refs.pedido_cliente_list.tableData[index].customer_nick_name = order.customer_nick_name;
                this.$refs.pedido_cliente_list.tableData[index].customer_has_afip_data = order.customer_has_afip_data;
                this.$refs.pedido_cliente_list.tableData[index].customer_tipo_persona = order.customer_tipo_persona;
                this.$refs.pedido_cliente_list.tableData[index].customer_type_id = order.customer_type_id;
                this.$refs.pedido_cliente_list.tableData[index].customer_type = order.customer_type;
                this.$refs.pedido_cliente_list.tableData[index].customer_contact = order.customer_contact;
                this.$refs.pedido_cliente_list.tableData[index].customer_accounting_account_child = order.customer_accounting_account_child;
                this.$refs.pedido_cliente_list.tableData[index].date = order.date;
                this.$refs.pedido_cliente_list.tableData[index].created_on_meli = order.created_on_meli;
                this.$refs.pedido_cliente_list.tableData[index].email = order.email;
                this.$refs.pedido_cliente_list.tableData[index].invoices = order.invoices;
                this.$refs.pedido_cliente_list.tableData[index].is_meli_order = order.is_meli_order;
                this.$refs.pedido_cliente_list.tableData[index].items = order.items;
                this.$refs.pedido_cliente_list.tableData[index].payment_data = order.payment_data;
                this.$refs.pedido_cliente_list.tableData[index].phone_1 = order.phone_1;
                this.$refs.pedido_cliente_list.tableData[index].phone_2 = order.phone_2;
                this.$refs.pedido_cliente_list.tableData[index].status = order.status;
                this.$refs.pedido_cliente_list.tableData[index].status_id = order.status_id;
                this.$refs.pedido_cliente_list.tableData[index].status_list = order.status_list;
                this.$refs.pedido_cliente_list.tableData[index].total = order.total;
                this.$refs.pedido_cliente_list.tableData[index].total_raw = order.total_raw;
                this.$refs.pedido_cliente_list.tableData[index].user_on_list_status = order.user_on_list_status;
                this.$refs.pedido_cliente_list.tableData[index].has_delivery_date = order.has_delivery_date;
                this.$refs.pedido_cliente_list.tableData[index].delivery_date = order.delivery_date;
                this.$refs.pedido_cliente_list.tableData[index].first_contact = order.first_contact;
                this.$refs.pedido_cliente_list.tableData[index].pedido_fiscal_address = order.pedido_fiscal_address;
                this.$refs.pedido_cliente_list.tableData[index].pedido_delivery_address = order.pedido_delivery_address;
                this.$refs.pedido_cliente_list.tableData[index].meli_id = order.meli_id;
                this.$refs.pedido_cliente_list.tableData[index].meli_messages = order.meli_messages;
                this.$refs.pedido_cliente_list.tableData[index].message_pack_id = order.message_pack_id;
                this.$refs.pedido_cliente_list.tableData[index].message_seller_id = order.message_seller_id;
                this.$refs.pedido_cliente_list.tableData[index].message_seller_email = order.message_seller_email;
                this.$refs.pedido_cliente_list.tableData[index].message_to_user = order.message_to_user;
                this.$refs.pedido_cliente_list.tableData[index].payment_type = order.payment_type;
                this.$refs.pedido_cliente_list.tableData[index].status_listado_date = order.status_listado_date;
            },

            clear_dates()
            {
                this.$refs.status_listated_date_from.clearDate();
                this.$refs.status_listated_date_to.clearDate();
                this.$refs.delivery_date_from.clearDate();
                this.$refs.delivery_date_to.clearDate();
                this.status_listated_date_from = null;
                this.status_listated_date_to = null;
                this.delivery_date_from = null;
                this.delivery_date_to = null;
            },
            customFormatter(date){
                return this.$moment(date).format("DD/MM/YYYY");
            },
            toggleAll()
            {

            },

            unmarkAll()
            {

            },

            selectedStatus(el)
            {
                this.status = el;
            },

            removeStatus(el)
            {
                this.status = null;

                this.loadData();
            },

            goTo(customer_id)
            {
                window.location.href = "/clientes/edicion/" + customer_id;
            },

            loadData(page=1)
            {
                this.loading = true;
                
                this.comandaArray = [];
                this.allMarked = false;
                this.markedRows = [];
                
                let url = '/api/pedido_cliente/index?page=' + page;

                if (this.customer != null) {
                    url = url + '&customer='+this.customer.id;
                }

                if (this.status != null) {
                    url = url + '&status='+this.status.status_id;
                }

                if (this.status_listated_date_from != null) {
                    let status_listated_date_from = this.$moment(this.status_listated_date_from).format("YYYY-MM-DD 00:00:00");
                    url = url + '&status_listated_date_from='+status_listated_date_from;
                }

                if (this.status_listated_date_to != null) {
                    let status_listated_date_to = this.$moment(this.status_listated_date_to).format("YYYY-MM-DD 23:59:59");
                    url = url + '&status_listated_date_to='+status_listated_date_to;
                }

                if (this.delivery_date_from != null) {
                    let delivery_date_from = this.$moment(this.delivery_date_from).format("YYYY-MM-DD 00:00:00");
                    url = url + '&delivery_date_from='+delivery_date_from;
                }

                if (this.delivery_date_to != null) {
                    let delivery_date_to = this.$moment(this.delivery_date_to).format("YYYY-MM-DD 23:59:59");
                    url = url + '&delivery_date_to='+delivery_date_to;
                }

                if (this.code != null) {
                    url = url + '&code='+this.code.code;
                }

                axios.get(url).then((response) => {

                    this.$store.commit('SET_PEDIDOS', response.data.data);

                    this.www = response.data.pagination.last_page;

                    const customers_data = response.data.customers_data;

                    this.options.groupMeta = customers_data;

                }).catch((err) => {
                    
                }).finally( () => {
                    this.loading = false
                    this.removeCode();
                });
            },

            comandaPrint()
            {
                const markedRows = collect(this.markedRows);

                markedRows.map(id => {
                    collect(this.rows).map(row => {

                        if (row.id == id) {
                            this.comandaArray.push(row);
                            return false;
                        }
                    });
                    
                } );

                let data = [];

                collect(this.comandaArray).map(d => {
                    data.push(
                        {
                            customer : d.customer,
                            cv : '---------',
                            op : d.user_on_list_status[0],
                            address : d.pedido_delivery_address.name,
                            cellphone : (d.customer_cellphone)?d.customer_cellphone:'',
                            phone1 : (d.customer_phone1)? ' ' + d.customer_phone1:'',
                            phone2 : (d.customer_phone2)? ' ' +d.customer_phone2:'',
                            phone3 : (d.customer_phone3)? ' ' + d.customer_phone3:'',
                            code : d.code,
                            delivery_date : d.date,
                            items : d.items,
                            emition_date : this.$time(Date.now()).format("DD-MM-YYYY"),
                            origin : (d.is_meli_order) ? 'MercadoLibre' : 'Local',
                            comments : d.comments

                        }
                    )
                });
                
                this.spinner = true;

                setTimeout(() => {
                    let comanda = new Comanda();
                    comanda.generate(data);
                    comanda.print(); 
                    this.spinner = false;
                }, 500);
            },

            delete_pedido(pedido_id){
                const ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == pedido_id
                });
                this.$refs.pedido_cliente_list.tableData.splice(ind, 1);
            }

        },

        async mounted()
        {

            await this.$store.dispatch('get_company', this.User.token);
            
            if (sessionStorage.getItem("order_id") != null) {

                let payload = {
                    token : this.User.token,
                    code : sessionStorage.getItem("order_id")
                }
                sessionStorage.removeItem("order_id");
                this.sleep(500);
                const pedido = await this.$store.dispatch('pedido_show', payload)
                    .catch((err) => {
                    }).finally(()=>{
                        
                    });

                if(pedido)
                {
                    const order = [];
                    order.push(pedido.data);
                    this.$store.commit('SET_PEDIDOS', order);
                }
            }

            Event.$on('delete_product_of_pedido', data => {
                const { pedido_id } = data;
                const { product_id } = data;

                const ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == pedido_id
                });

                this.$refs.pedido_cliente_list.tableData.forEach((item, index) => {
                    if (item.product_id == product_id) {
                        delete this.$refs.pedido_cliente_list.tableData[ind].items[index];
                    }
                })

                collect(this.$refs.pedido_cliente_list.tableData[ind].items).filter();

            });

            Event.$on('pedido_cliente_list', data => {
                
                const ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.id
                });

                this.$refs.pedido_cliente_list.tableData[ind].status = data.status;
                this.$refs.pedido_cliente_list.tableData[ind].status_id = data.status_id;
                this.$refs.pedido_cliente_list.tableData[ind].customer_addresses = data.customer_addresses;
                this.$refs.pedido_cliente_list.tableData[ind].who_prepared = data.who_prepared;
                this.$refs.pedido_cliente_list.tableData[ind].who_delivered = data.who_delivered;
                this.$refs.pedido_cliente_list.tableData[ind].customer_addresses = data.customer_addresses;
                this.$refs.pedido_cliente_list.tableData[ind].customer_DocTipo = data.customer_DocTipo;
                this.$refs.pedido_cliente_list.tableData[ind].customer_inscription_name = data.customer_inscription_name;
                this.$refs.pedido_cliente_list.tableData[ind].customer_document_number = data.customer_document_number;
                this.$refs.pedido_cliente_list.tableData[ind].customer = data.customer;
                this.$refs.pedido_cliente_list.tableData[ind].customer_has_afip_data = data.customer_has_afip_data;
                this.$refs.pedido_cliente_list.tableData[ind].total = data.total;
                this.$refs.pedido_cliente_list.tableData[ind].remito_code = data.remito_code;

            });

            Event.$on('pedido_cliente_list_update_comments', data => {
                let ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.id
                });
                this.$refs.pedido_cliente_list.tableData[ind].comments = data.comments;

            });

            Event.$on('pedido_cliente_update_delivery_date', (data) => {
                const ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.id
                });
                this.$refs.pedido_cliente_list.tableData[ind].delivery_date = data.delivery_date;
                //this.$refs.pedido_cliente_list.tableData[ind].has_delivery_date = true;
            });

            Event.$on('pedido_cliente_delete', (data) => {
                const ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.id
                });
                this.$refs.pedido_cliente_list.tableData.splice(ind, 1);
            });

            Event.$on('set_customer_address', (data) => {
                
                let ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.data.id
                });
                
                this.$refs.pedido_cliente_list.tableData[ind].has_address = true;
                
                this.$refs.pedido_cliente_list.tableData.forEach((row, index) => {
                    
                    if (row.customer_id == data.address.person_id) {

                        if (this.$refs.pedido_cliente_list.tableData[index].customer_addresses) {
                            this.$refs.pedido_cliente_list.tableData[index].customer_addresses.push(data.address);
                            this.$refs.pedido_cliente_list.tableData[index].has_address = true;
                        }else{
                            this.$refs.pedido_cliente_list.tableData[index].customer_addresses = [];
                            this.$refs.pedido_cliente_list.tableData[index].customer_addresses.push(data.address);
                            this.$refs.pedido_cliente_list.tableData[ind].has_address = true;
                        }
                    }    
                });
            });

            Event.$on('set_customer_data_updated', (data) => {
                let ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.data.id
                });
                this.$refs.pedido_cliente_list.tableData[ind].customer_DocTipo = data.customer_updated_data.purchaser_document;
                this.$refs.pedido_cliente_list.tableData[ind].customer_inscription_name = data.customer_updated_data.inscription;
                this.$refs.pedido_cliente_list.tableData[ind].customer_document_number = data.customer_updated_data.number;
                this.$refs.pedido_cliente_list.tableData[ind].customer = data.customer_updated_data.name;
                this.$refs.pedido_cliente_list.tableData[ind].customer_has_afip_data = data.customer_updated_data.customer_has_afip_data;
                this.$refs.pedido_cliente_list.tableData[ind].customer_addresses = data.customer_updated_data.addresses;
                
                this.$refs.pedido_cliente_list.tableData.forEach((row, index) => {
                    
                    if (row.customer_id == data.customer_updated_data.id) {
                        this.$refs.pedido_cliente_list.tableData[index].customer_addresses = data.customer_updated_data.addresses;
                        this.$refs.pedido_cliente_list.tableData[index].customer_DocTipo = data.customer_updated_data.purchaser_document;
                        this.$refs.pedido_cliente_list.tableData[index].customer_inscription_id = data.customer_updated_data.inscription_id;
                        this.$refs.pedido_cliente_list.tableData[index].customer_inscription_name = data.customer_updated_data.inscription;
                        this.$refs.pedido_cliente_list.tableData[index].customer_document_number = data.customer_updated_data.number;
                        this.$refs.pedido_cliente_list.tableData[index].customer = data.customer_updated_data.name;
                    }    
                });
                
            });

            let address_type = await this.$store.dispatch('getAddressType', this.User.token);

            if (address_type) {
                this.$store.commit('SET_ADDRESS_TYPE', address_type.data);
            }

            Event.$on('open_child_row_component', (order) => {
                
                const openRows = collect(this.$refs.pedido_cliente_list.openChildRows);
                
                openRows.map(row_id => {
                    if (row_id != order.id) {
                        this.$refs.pedido_cliente_list.toggleChildRow(row_id);
                    }
                });
                
            });

            Event.$on('save_fiscal_address', (data) => {
                const ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.pedido_id
                });
                this.$refs.pedido_cliente_list.tableData[ind].pedido_fiscal_address = data.address.data;
            });

            Event.$on('remove_fiscal_address', (data) => {
                const ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.pedido_id
                });
                this.$refs.pedido_cliente_list.tableData[ind].pedido_fiscal_address = false;
            });

            Event.$on('save_delivery_address', (data) => {
                const ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.pedido_id
                });
                this.$refs.pedido_cliente_list.tableData[ind].pedido_delivery_address = data.address.data;
            });

            Event.$on('remove_delivery_address', (data) => {
                const ind = this.$refs.pedido_cliente_list.tableData.findIndex(row => {
                    return row.id == data.pedido_id
                });
                this.$refs.pedido_cliente_list.tableData[ind].pedido_delivery_address = false;
            });
            
            Event.$on('update_customer_from_modal_form', (customer) => {

                this.$refs.pedido_cliente_list.tableData.findIndex((row, index) => {
                    if (row.customer_id == customer.id) {
                        this.$refs.pedido_cliente_list.tableData[index].customer_addresses = customer.addresses;
                        this.$refs.pedido_cliente_list.tableData[index].customer_cellphone = customer.cell_phone;
                        this.$refs.pedido_cliente_list.tableData[index].customer_contact = customer.contact;
                        this.$refs.pedido_cliente_list.tableData[index].customer_has_afip_data = customer.customer_has_afip_data;
                        this.$refs.pedido_cliente_list.tableData[index].email = customer.email;
                        this.$refs.pedido_cliente_list.tableData[index].customer_phone_1 = customer.phone_1;
                        this.$refs.pedido_cliente_list.tableData[index].customer_phone_2 = customer.phone_2;
                        this.$refs.pedido_cliente_list.tableData[index].customer_phone_3 = customer.phone_3;
                    }
                });
            });

            Event.$on('open-order-from-notification', (notification)=> {
                this.$refs.pedido_cliente_list.tableData.findIndex((row, index) => {
                    if (row.meli_id == notification) {
                        this.$refs.pedido_cliente_list.toggleChildRow(row.id);
                    }
                });
            });

            Event.$on('open-order-from-notification_order', (notification)=> {
                this.$refs.pedido_cliente_list.tableData.findIndex((row, index) => {
                    if (row.meli_id == notification) {
                        this.$refs.pedido_cliente_list.toggleChildRow(row.id);
                    }
                });
            });

            Event.$on('restore_pedido', (pedido)=> {
                this.$refs.pedido_cliente_list.tableData.findIndex((row, index) => {
                    if (row.id == pedido.id) {
                        this.$refs.pedido_cliente_list.tableData[index].status = pedido.status;
                        this.$refs.pedido_cliente_list.tableData[index].status_id = pedido.status_id;
                    }
                });
            });

            Event.$on('changeToPedido', (pedido)=>{
                this.$store.commit('ADD_NEW_PEDIDO', pedido);

                this.delete_pedido(pedido.parent_id);
            });

            Event.$on('cotizacionItemsHasChanged', (cotiz)=>{

                this.$refs.pedido_cliente_list.tableData.findIndex((row, index) => {
                    if (row.id == cotiz.id) {
                        this.update_order_data(index, cotiz);
                    }
                });
            });

        }
    }
</script>

<style slot-scope>
    .v-collapse-content{
        max-height: 0;
        transition: max-height 0.5s ease-out;
        overflow: hidden;
        padding: 0;
    }

    .v-collapse-content-end{
        transition: max-height 0.5s ease-in;
    }

    .VueTables__child-row-toggler {
        width: 16px;
        height: 16px;
        line-height: 16px;
        display: block;
        margin: auto;
        text-align: center;
    }

    .VueTables__child-row-toggler--closed::before {
       /* font-family: FontAwesome; 
        content: "\f067";
        color :green; */
        content:  "+";
        font-size: 150%
    }

    .VueTables__child-row-toggler--open::before {
        /* font-family: FontAwesome;
        content: "\f111";
        color : red; */
        content: "-";
        font-size: 150%
    }

    .VueTables--client {
        min-height: 500px; 
    }
    div.row {
        margin-bottom: 1.5rem;
    }
    .wrapper-pedido-list-table table thead tr th:nth-child(1),
    .wrapper-pedido-list-table table thead tr th:nth-child(2),
    .wrapper-pedido-list-table table thead tr th:nth-child(3) {
        width: 2%;
        text-align: center;
        text-transform: uppercase;
        font-size: 14px !important;
        vertical-align: middle;
        
    }
    
    .wrapper-pedido-list-table table thead tr th:nth-child(4)
    {
        width: 27%;
        text-align: center;
        text-transform: uppercase;
        font-size: 14px !important;
        vertical-align: middle;
    }
    .wrapper-pedido-list-table table thead tr th:nth-child(5),
    .wrapper-pedido-list-table table thead tr th:nth-child(6),
    .wrapper-pedido-list-table table thead tr th:nth-child(7)
    {
        width: 10%;
        text-align: center;
        text-transform: uppercase;
        font-size: 14px !important;
        vertical-align: middle;
    }
    .wrapper-pedido-list-table table thead tr th:nth-child(8)
    {
        width: 17%;
        text-align: center;
        text-transform: uppercase;
        font-size: 14px !important;
        vertical-align: middle;
    }
    .wrapper-pedido-list-table table thead tr th:nth-child(9)
    {
        width: 15%;
        text-align: center;
        text-transform: uppercase;
        font-size: 14px !important;
        vertical-align: middle;
    }
    .wrapper-pedido-list-table table thead tr th:nth-child(10)
    {
        width: 5%;
        text-align: center;
        text-transform: uppercase;
        font-size: 14px !important;
        vertical-align: middle;
    }
    .wrapper-pedido-list-table table thead tr {
        background-color: #00bbf0;
        color: aliceblue;
        font-weight: bold;
        
    }
</style>