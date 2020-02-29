<template>
	<v-container>
		<v-card>
			<v-card-title class="display-1 text--primary">
				Expenses
				<v-spacer></v-spacer>
				<v-breadcrumbs :items="items">
					<template v-slot:divider>
						<v-icon>chevron_right</v-icon>
					</template>
				</v-breadcrumbs>
			</v-card-title>
			
				
			<v-card-text>
				<table class="v-datatable v-table" style="border:1px solid #ddd;width:100%;">
					<thead>
						<tr class="grey lighten-4" style="border-bottom:1px solid #333">
							<th class="font-weight-bold text-xs-center" style="width:20%;">Expense Category</th>
							<th class="font-weight-bold text-xs-center" style="width:20%;">Amount</th>
							<th class="font-weight-bold text-xs-center" style="width:20%;">Entry Date</th>
							<th class="font-weight-bold text-xs-center" style="width:20%;">Created at</th>
						</tr>
					</thead>
					<tbody>
						<tr v-if="expenseslist.length == 0">
							<td colspan="4" class="text-center">No Data Found!</td>
						</tr>
						<tr v-else v-for="(expenses,index) in expenseslist" :key="index" @click="updateExpense(index)">
							<td class="text-center">{{checkExpenseType(expenses.expense)}}</td>
							<td class="text-center">{{expenses.amount}}</td>
							<td class="text-center">{{expenses.entrydate}}</td>
							<td class="text-center">{{expenses.created_at}}</td>
						</tr>
					</tbody>
				</table>
			</v-card-text>
			<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn class="green darken-4 white--text" @click="openModal">Add Expense</v-btn>
			</v-card-actions>
		</v-card>
		<v-dialog v-model="addexpense" width="30%" persistent>
				<v-card>
					<v-card-title class="green darken-4 white--text" v-if="formType=='add'">Add Expense</v-card-title>
					<v-card-title class="green darken-4 white--text" v-if="formType=='update'">Update Expense</v-card-title>
					<v-card-title class="green darken-4 white--text" v-if="formType=='delete'">Delete Expense</v-card-title>
					<v-form ref="vForm" v-on:submit.prevent="submitForm">
						<template v-if="formType!='delete'">
							<v-card-text>
								<v-container>
									<v-layout row wrap>
										<v-flex md3 class="pa-3 text-right">
											Expense Category 
										</v-flex>
										<v-flex md9>
											<v-select solo v-model="form.expense" :items="categorieslist" item-value='id' item-text='category' :rules="[formRules.required]"></v-select>
										</v-flex>
										<v-flex md3 class="pa-3 text-right">
											Amount
										</v-flex>
										<v-flex md9>
											<v-text-field :rules="[formRules.required]" autocomplete="false" v-model="form.amount" @keyup="formatAmount" solo></v-text-field>
										</v-flex>
										<v-flex md3 class="pa-3 text-right">
											Entry Date
										</v-flex>
										<v-flex md9>
											<v-menu v-model="menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y min-width="290px">
												<template v-slot:activator="{ on }">
													<v-text-field v-model="form.entrydate" append-icon="event" readonly solo v-on="on"></v-text-field>
												</template>
												<v-date-picker color="green darken-4" no-title v-model="form.entrydate" @input="menu = false"></v-date-picker>
											</v-menu>
										</v-flex>
									</v-layout>
								</v-container>
							</v-card-text>
						</template>
						<template v-else>
							<v-container>
								Are you sure you want to <i><b>Delete</b></i> this expense?
							</v-container>
						</template>
					</v-form>
					<v-card-actions>
						<v-btn v-if="formType=='update'" @click="deleteExpense()">Delete</v-btn>
						<v-spacer></v-spacer>
						<v-btn @click="addexpense=false">Cancel</v-btn>
						<v-btn  @click="submitForm" v-if="formType=='add'">Save</v-btn>
						<template v-else>
							<template v-if="formType=='update'">
								<v-btn @click="submitForm">Update</v-btn>
							</template>
							<template v-if="formType=='delete'">
								<v-btn @click="submitForm">Delete</v-btn>
							</template>
						</template>
					</v-card-actions>
				</v-card>
		</v-dialog>
	</v-container>
</template>
<script>
	import VueCookies from "vue-cookies";
	import axios from "axios";
	export default {
		created : function(){
			this.token = VueCookies.get(this.cookieKey).token;
			this.userid = VueCookies.get(this.cookieKey).data.id;
			this.fetchExpense();
			this.fetchCategory();
		},
		data: function(){
            return {
				userid : '',
				menu : false,
				items: [
					{
						text: 'Expense Management',
						disabled: false,
					},
					{
						text: 'Expenses',
						disabled: false,
					},
				],
				expenseslist : [],
				categorieslist : [],
				addexpense : false,
				form : {
					id : '',
					expense : {},
					amount : '',
					entrydate : ''
				},
				formType : 'add',
			}
		},
		methods : {
			fetchExpense : function(){
				let _this = this;
				_this.expenseslist = [],
				axios.create({
					headers : {
						'Authorization' : `Bearer ${this.token}`
					}
				})
				.get(this.apiUrl + '/expenses/list?userid='+_this.userid)
				.then(function(res){
					_this.expenseslist = res.data.data;
				})
			},
			openModal: function(){
				if(this.form.id != ''){
					this.$refs.vForm.reset();
				}
				this.formType = 'add';
				this.addexpense=true;
			},
			submitForm : function(){
				let _this = this,
				type = _this.formType,
				formData = new FormData();

				formData.append('id',_this.form.id);
				formData.append('expense',_this.form.expense);
				formData.append('amount',_this.form.amount);
				formData.append('entrydate',_this.form.entrydate);
				formData.append('userid',_this.userid);
				
				if(_this.$refs.vForm.validate()){
					axios.create({
						headers : {
							'Authorization': `Bearer ${this.token}`
						}
					})
					.post(this.apiUrl+'/expenses/'+type,formData)
					.then(function(response) {
						_this.addcategory = false;
						if(response.data.status){
							_this.eventHub.$emit('showSnackBar',{icon:'check',color:'success',message:response.data.message})
							_this.fetchExpense();
							_this.addexpense = false;
						}else{
							_this.eventHub.$emit('showSnackBar',{icon:'close',color:'error',message:'Error while submitting.'})	
						}
					});
				}
			},
			updateExpense : function(id){
				this.formType = "update";
				this.form.id = this.expenseslist[id].id;
				this.form.expense = parseInt(this.expenseslist[id].expense);
				this.form.amount = this.expenseslist[id].amount;
				this.form.entrydate = this.expenseslist[id].entrydate;
				this.addexpense = true;
			},
			deleteExpense : function(id){
				this.formType = "delete";
			},
			fetchCategory : function(){
				let _this = this;
				_this.categorieslist = [],
				axios.create({
					headers : {
						'Authorization' : `Bearer ${this.token}`
					}
				})
				.get(this.apiUrl + '/categories/list')
				.then(function(res){
					_this.categorieslist = res.data.data;
				})
			},
			checkExpenseType : function(id){
				for(let i in this.categorieslist){
					if(this.categorieslist[i].id == id){
						return this.categorieslist[i].category;
					}
				}
			},
			formatAmount : function(){
				this.form.amount = this.numberWithCommas(this.numberOnly(this.form.amount));
			}
		}
	};
</script>