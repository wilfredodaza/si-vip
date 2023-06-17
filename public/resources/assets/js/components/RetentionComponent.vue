<template>
    <div>
    <div class="row">
        <div class="col s9 m9"  v-if="invoice.invoice_status_id != 10 && typeCustomer != 5">
        <label>Cuenta contable </label>
        <select class="browser-default" v-model="idRetention">
            <option value="" disabled selected>Elige tu opción</option>
            <option :value="item.id" v-for="item of retentions">[{{ item.code }}] - {{ item.name }}</option>
        </select>
        </div>
        <div class="col s3" v-if="invoice.invoice_status_id != 10  && typeCustomer != 5 ">
            <button class="btn" style="margin-top:20px;" v-on:click="addAccounts(idRetention)"><i class="material-icons">add</i></button>
        </div>
    </div>
     <div class="row">
        <div class="col s12">
            <table class="table-responsive centered" v-if="typeCustomer != '5'">
                <thead>
                    <tr>
                        <th>#</th>
                        <th >Nombre</th>
                        <th width="50px">Porcentaje</th>
                        <th>Valor</th>
                        <th v-if="invoice.invoice_status_id != 10">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index ) of withholdings" :key="item.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.name }}</td>
                        <td>
                            <span v-if="invoice.invoice_status_id == 10">{{item.percent}}</span>
                            <input placeholder="Placeholder" id="first_name" type="number" class="validate" v-model="item.percent" maxlength="2" max="99" min="0" minlength="0" pattern="^[0-9]+"  v-if="invoice.invoice_status_id != 10"  v-on:change="withholdingsEdit(item.id, item.percent)">
                   
                        </td>
                        <td>{{  new Intl.NumberFormat("es-Es").format((invoice.payable_amount * item.percent / 100).toFixed(3)) }}</td>
                        <td v-if="invoice.invoice_status_id != 10">
                            <div class="btn-group" role="group">
                                <button class="btn btn-small red"  style="" v-on:click="withholdingsDelete(item.id)"><i class="material-icons">delete</i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table-responsive centered" v-if="typeCustomer == 5">
                 <thead>
                    <tr>
                        <th>#</th>
                        <th >Nombre</th>
                        <th width="50px">Porcentaje</th>
                        <th>Valor</th>
                    </tr>
                 </thead>
                   <tbody>
                    <tr v-for="(item, index ) of withholdings" :key="item.id">
                         <td>{{ index + 1 }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{item.percent}}</td>
                        <td>{{  new Intl.NumberFormat("es-Es").format((invoice.payable_amount * item.percent / 100).toFixed(3)) }}</td>
                    </tr>
                   </tbody>
            </table>
            <p class="center red-text" style="margin-top:20px;" v-if="withholdings.length == 0">No hay ninguna retención registrada.</p>
        </div>
    </div>
    </div>

</template>

<script>

import Axios from 'axios';
import M from 'materialize-css';
export default {
    name: "RetentionComponent",
   mounted() {
     this.url = localStorage.getItem('url');
    this.accountingAccounts();
    this.withholdingsAll();
    this.typeCustomer = localStorage.getItem('type_customer');
    M.AutoInit()
   },
   data(){
       return {
           url: '',
           retentions: [],
           idRetention: '',
           taxes: [],
           invoice: {},
           withholdings: [],
           typeCustomer: 2,
       }
   }, methods: {
        accountingAccounts() {
            Axios.get(`${this.url}/api/v1/tax_advance`).then((response) => {
                this.retentions = response.data.data;
            });

            Axios.get(`${this.url}/api/v1/document_support/show/${localStorage.getItem('item')}`).then((response) => {
                this.invoice = response.data.data;
            });
        },
        withholdingsAll() {
            Axios.get(`${this.url}/api/v1/document_support/withholdings/${localStorage.getItem('item')}`).then((response) => {
                this.withholdings = response.data.data;
            });
        },
        addAccounts(id) {
 
            for(let item of this.retentions) {
                if(item.id == id) {
                        const data = {
                        accounting_account_id: id,
                        percent: item.percent,
                        invoice_id: this.invoice.id,   
                    } 
                    Axios.post(`${this.url}/api/v1/document_support/withholding`, data).then((response) => {
                        this.withholdingsAll();
                         M.toast({
                            html: 'Retención agregada.',
                            position: 'bottom-center'
                        })

                        
                    });
                   
                }
            }

             
        },
        withholdingsDelete(id) {
            Axios.get(`${this.url}/api/v1/document_support/withholding/delete/${id}`).then((response) => {
                this.withholdingsAll();
                 M.toast({
                    html: 'Retención eliminada.',
                    position: 'bottom-center'
                })
            });
        },
        withholdingsEdit(id, value) {
            const data = {
                'percent':  value
            };
            if(value <= 99 && value >= 0) {
                Axios.put(`${this.url}/api/v1/document_support/withholding/update/${id}`, data).then((response) => {
                    M.toast({
                        html: 'Retención actualizada.',
                        position: 'bottom-center'
                    })
                });
            }else if(value > 99 ) {
                let i = 0;
                for(let dates of this.withholdings) {
                    if(id == dates.id) {
                        this.withholdings[i].percent = 99;
                        i++;
                    }
                }
            }else if(value < 0) {
                 let i = 0;
                for(let dates of this.withholdings) {
                    if(id == dates.id) {
                        this.withholdings[i].percent = 0;
                        i++;
                    }
                }
            }
            
        }
   }
}
</script>
