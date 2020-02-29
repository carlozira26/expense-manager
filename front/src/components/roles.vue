<template>
	<v-container>
		<v-card>
			<v-card-title class="display-1 text--primary">
				Roles
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
						<tr v-if="roleslist.length == 0">
							<td colspan="4" class="text-center">No Data Found!</td>
						</tr>
						<tr v-else v-for="(role,index) in roleslist" :key="index" @click="updateRole(index)">
							<td class="text-center">{{role.role}}</td>
							<td class="text-center">{{role.description}}</td>
							<td class="text-center">{{role.created_at}}</td>
						</tr>
					</tbody>
				</table>
			</v-card-text>
			<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn class="green darken-4 white--text" @click="openModal">Add Role</v-btn>
			</v-card-actions>
		</v-card>
		<v-dialog v-model="addrole" width="30%" persistent>
				<v-card>
					<v-card-title class="green darken-4 white--text" v-if="formType=='add'">Add Role</v-card-title>
					<v-card-title class="green darken-4 white--text" v-if="formType=='update'">Update Role</v-card-title>
					<v-card-title class="green darken-4 white--text" v-if="formType=='delete'">Delete Role</v-card-title>
					<v-form ref="vForm" v-on:submit.prevent="submitForm">
						<template v-if="formType!='delete'">
						<v-card-text>
							<v-container>
								<v-layout row wrap>
									<v-flex md3 class="pa-3 text-right">
										Display Name
									</v-flex>
									<v-flex md9>
										<v-text-field solo v-model="form.rolename" :rules="[formRules.required]"></v-text-field>
									</v-flex>
									<template v-if="form.role != 'admin'">
										<v-flex md3 class="pa-3 text-right">
											Description
										</v-flex>
										<v-flex md9>
											<v-text-field :rules="[formRules.required]" v-model="form.description" solo></v-text-field>
										</v-flex>
									</template>
								</v-layout>
							</v-container>
						</v-card-text>
						</template>
						<template v-else>
							<v-container>
								Are you sure you want to <i><b>Delete</b></i> this role?
							</v-container>
						</template>
					</v-form>
					<v-card-actions>
						<v-btn v-if="formType=='update'" @click="deleteRole()">Delete</v-btn>
						<v-spacer></v-spacer>
						<v-btn @click="addrole=false">Cancel</v-btn>
						<v-btn type="submit" v-if="formType=='add'" @click="submitForm">Save</v-btn>
						<template v-else>
							<template v-if="form.rolename != 'admin' && formType!='delete'">
								<v-btn @click="submitForm">Update</v-btn>
							</template>
							<template v-else>
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
			this.fetchRoles();
		},
		data: function(){
            return {
				items: [
					{
						text: 'User Management',
						disabled: false,
					},
					{
						text: 'Roles',
						disabled: false,
					},
				],
				roleslist : [],
				addrole : false,
				form : {
					id : '',
					rolename : '',
					description : ''
				},
				formType : 'add',
			}
		},
		methods : {
			
			fetchRoles : function(){
				let _this = this;
				_this.roleslist = [],
				axios.create({
					headers : {
						'Authorization' : `Bearer ${this.token}`
					}
				})
				.get(this.apiUrl + '/roles/list')
				.then(function(res){
					_this.roleslist = res.data.data;
				})
			},
			openModal: function(){
				if(this.form.rolename != ''){
					this.$refs.vForm.reset();
				}
				this.formType = 'add';
				this.addrole=true;
			},
			submitForm : function(){
				let _this = this,
				type = _this.formType,
				formData = new FormData();

				formData.append('id',_this.form.id);
				formData.append('role',_this.form.rolename);
				formData.append('description',_this.form.description);

				if(_this.$refs.vForm.validate()){
					axios.create({
						headers : {
							'Authorization': `Bearer ${this.token}`
						}
					})
					.post(this.apiUrl+'/roles/'+type,formData)
					.then(function(response) {
						_this.addrole = false;
						if(response.data.status){
							_this.eventHub.$emit('showSnackBar',{icon:'check',color:'success',message:response.data.message})
							_this.fetchRoles();
						}else{
							_this.eventHub.$emit('showSnackBar',{icon:'close',color:'error',message:'Error while submitting.'})							
						}
					});
				}
			},
			updateRole : function(id){
				this.formType = "update";
				this.form.id = this.roleslist[id].id;
				this.form.rolename = this.roleslist[id].role;
				this.form.description = this.roleslist[id].description;
				this.addrole = true;
			},
			deleteRole : function(){
				this.formType = "delete";
			}
		}
	};
</script>