<template>
	<v-container>
		<v-card>
			<v-card-title class="display-1 text--primary">
				My Expenses
				<v-spacer></v-spacer>
				<v-breadcrumbs :items="items">
					<template v-slot:divider>
						<v-icon>chevron_right</v-icon>
					</template>
				</v-breadcrumbs>
			</v-card-title>
			<v-card-text>
				<v-layout row wrap class="pa-3">
					<v-flex md4>
						<table width="100%">
							<thead>
								<tr>
									<th>Expense Categories</th>
									<th class="text-center">Total</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(expenses,index) in expenseslist" :key="index">
									<td class="text-center">{{dataList(expenses.expense)}}</td>
									<td class="text-center">{{expenses.amount}}</td>
								</tr>
							</tbody>
						</table>
					</v-flex>
					<v-flex md8>
						<apexchart type="pie" :options="pieOptions" height="300px" :series="pieSeries"></apexchart>			  
					</v-flex>
				</v-layout>
			</v-card-text>
		</v-card>
	</v-container>
</template>
<script>
import VueApexCharts from 'vue-apexcharts';
import ApexCharts from "apexcharts";
import VueCookies from 'vue-cookies';
import axios from "axios";
	export default {
		components : {
			'apexchart' : VueApexCharts,
		},
		created: function(){
			this.role = VueCookies.get(this.cookieKey).data.role;
			this.userid = VueCookies.get(this.cookieKey).data.id;
			this.fetchCategory();
			this.fetchExpense();
		},
		data : function(){
			return {
				userid : '',
				items: [
					{
						text: 'Dashboard',
						disabled: false,
					}
				],
				pieOptions : {
					labels : [],
					dataLabels: {
						enabled: false
					},
					legend: {
						show: false
					}
				},
				expenseslist : [],
				categorieslist : [],
				pieSeries : [],
			}
		},
		methods : {
			fetchExpense : function(){
				let _this = this;
				this.pieSeries = [];
				_this.expenseslist = [],
				axios.create({
					headers : {
						'Authorization' : `Bearer ${this.token}`
					}
				})
				.get(this.apiUrl + '/expenses/list?userid='+_this.userid)
				.then(function(res){
					_this.expenseslist = res.data.data;
					for(let index in _this.expenseslist){
						_this.pieSeries.push(parseInt(_this.expenseslist[index].amount));
					}
						// console.log(_this.pieSeries);
				})
			},
			fetchCategory : function(){
				let _this = this,
				arr = [];
				_this.categorieslist = [],
				axios.create({
					headers : {
						'Authorization' : `Bearer ${this.token}`
					}
				})
				.get(this.apiUrl + '/categories/list')
				.then(function(res){
					_this.categorieslist = res.data.data;
					for(let index in _this.categorieslist){
						_this.pieOptions.labels.push(_this.categorieslist[index].category);
					}
				});
			},
			dataList : function(id){
				let arr = [];
				// for(let exindex in this.expenseslist){
						

					for(let catindex in this.categorieslist){
						if(id == this.categorieslist[catindex].id){
							return this.categorieslist[catindex].category;
						}
					}
				// }
			}
		}
	};
</script>