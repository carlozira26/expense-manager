<template>
	<v-container>
		<v-card>
			<v-card-title class="display-1 text--primary">
				Expense Categories
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
							<th class="font-weight-bold text-xs-center" style="width:20%;">Display Name</th>
							<th class="font-weight-bold text-xs-center" style="width:20%;">Description</th>
							<th class="font-weight-bold text-xs-center" style="width:20%;">Created at</th>
						</tr>
					</thead>
					<tbody>
						<tr v-if="categorylist.length == 0">
							<td colspan="4" class="text-center">No Data Found!</td>
						</tr>
						<tr v-else v-for="(category,index) in categorylist" :key="index" @click="updateCategory(index)">
							<td class="text-center">{{category.category}}</td>
							<td class="text-center">{{category.description}}</td>
							<td class="text-center">{{category.created_at}}</td>
						</tr>
					</tbody>
				</table>
			</v-card-text>
			<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn class="green darken-4 white--text" @click="openModal">Add Category</v-btn>
			</v-card-actions>
		</v-card>
		<v-dialog v-model="addcategory" width="30%" persistent>
				<v-card>
					<v-card-title class="green darken-4 white--text" v-if="formType=='add'">Add Category</v-card-title>
					<v-card-title class="green darken-4 white--text" v-if="formType=='update'">Update Category</v-card-title>
					<v-card-title class="green darken-4 white--text" v-if="formType=='delete'">Delete Category</v-card-title>
					<v-form ref="vForm" v-on:submit.prevent="submitForm">
						<template v-if="formType!='delete'">
							<v-card-text>
								<v-container>
									<v-layout row wrap>
										<v-flex md3 class="pa-3 text-right">
											Display Name
										</v-flex>
										<v-flex md9>
											<v-text-field solo v-model="form.categoryname" :rules="[formRules.required]"></v-text-field>
										</v-flex>
										<v-flex md3 class="pa-3 text-right">
											Description
										</v-flex>
										<v-flex md9>
											<v-text-field :rules="[formRules.required]" v-model="form.description" solo></v-text-field>
										</v-flex>
									</v-layout>
								</v-container>
							</v-card-text>
						</template>
						<template v-else>
							<v-container>
								Are you sure you want to <i><b>Delete</b></i> this category?
							</v-container>
						</template>
					</v-form>
					<v-card-actions>
						<v-btn v-if="formType=='update'" @click="deleteCategory()">Delete</v-btn>
						<v-spacer></v-spacer>
						<v-btn @click="addcategory=false">Cancel</v-btn>
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
			this.role = VueCookies.get(this.cookieKey).data.role;
			if(this.role != 1){
				this.$router.push('/dashboard');
			}

			this.token = VueCookies.get(this.cookieKey).token;
			this.fetchCategory();
		},
		data: function(){
            return {
				items: [
					{
						text: 'Expense Management',
						disabled: false,
					},
					{
						text: 'Expense Categories',
						disabled: false,
					},
				],
				categorylist : [],
				addcategory : false,
				form : {
					id : '',
					categoryname : '',
					description : ''
				},
				formType : 'add',
			}
		},
		methods : {
			fetchCategory : function(){
				let _this = this;
				_this.categorylist = [],
				axios.create({
					headers : {
						'Authorization' : `Bearer ${this.token}`
					}
				})
				.get(this.apiUrl + '/categories/list')
				.then(function(res){
					console.log(res);
					_this.categorylist = res.data.data;
				})
			},
			openModal: function(){
				if(this.form.categoryname != ''){
					this.$refs.vForm.reset();
				}
				this.formType = 'add';
				this.addcategory=true;
			},
			submitForm : function(){
				let _this = this,
				type = _this.formType,
				formData = new FormData();

				formData.append('id',_this.form.id);
				formData.append('categoryname',_this.form.categoryname);
				formData.append('description',_this.form.description);
				
				if(_this.$refs.vForm.validate()){
					axios.create({
						headers : {
							'Authorization': `Bearer ${this.token}`
						}
					})
					.post(this.apiUrl+'/categories/'+type,formData)
					.then(function(response) {
						_this.addcategory = false;
						if(response.data.status){
							_this.eventHub.$emit('showSnackBar',{icon:'check',color:'success',message:response.data.message})
							_this.fetchCategory();
						}else{
							_this.eventHub.$emit('showSnackBar',{icon:'close',color:'error',message:'Error while submitting.'})							
						}
					});
				}
			},
			updateCategory : function(id){
				this.formType = "update";
				this.form.id = this.categorylist[id].id;
				this.form.categoryname = this.categorylist[id].category;
				this.form.description = this.categorylist[id].description;
				this.addcategory = true;
			},
			deleteCategory : function(id){
				this.formType = "delete";
			},
		}
	};
</script>