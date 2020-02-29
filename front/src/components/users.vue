<template>
	<v-container>
		<v-card>
			<v-card-title class="display-1 text--primary">
				Users
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
							<th class="font-weight-bold text-xs-center" style="width:20%;">Name</th>
							<th class="font-weight-bold text-xs-center" style="width:20%;">Email Address</th>
							<th class="font-weight-bold text-xs-center" style="width:20%;">Role</th>
							<th class="font-weight-bold text-xs-center" style="width:20%;">Created at</th>
						</tr>
					</thead>
					<tbody>
						<tr v-if="userlist.length == 0">
							<td colspan="4" class="text-center">No Data Found!</td>
						</tr>
						<tr v-else v-for="(user,index) in userlist" :key="index" @click="updateUser(index)">
							<td class="text-center">{{user.fullname}}</td>
							<td class="text-center">{{user.email}}</td>
							<td class="text-center">{{checkRoles(user.role)}}</td>
							<td class="text-center">{{user.created_at}}</td>
						</tr>
					</tbody>
				</table>
			</v-card-text>
			<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn class="green darken-4 white--text" @click="openModal">Add User</v-btn>
			</v-card-actions>
		</v-card>
		<v-dialog v-model="adduser" width="30%" persistent>
				<v-card>
					<v-card-title class="green darken-4 white--text" v-if="formType=='add'">Add User</v-card-title>
					<v-card-title class="green darken-4 white--text" v-if="formType=='update'">Update User</v-card-title>
					<v-card-title class="green darken-4 white--text" v-if="formType=='delete'">Delete User</v-card-title>
					<v-form ref="vForm" v-on:submit.prevent="submitForm">
						<template v-if="formType!='delete'">
							<v-card-text>
								<v-container>
									<v-layout row wrap>
										<v-flex md3 class="pa-3 text-right">
											Name
										</v-flex>
										<v-flex md9>
											<v-text-field solo v-model="form.name" :rules="[formRules.required]"></v-text-field>
										</v-flex>
										<v-flex md3 class="pa-3 text-right">
											Email Address
										</v-flex>
										<v-flex md9>
											<v-text-field solo v-model="form.email" :rules="[formRules.required, formRules.email]"></v-text-field>
										</v-flex>
										<v-flex md3 class="pa-3 text-right">
											Role
										</v-flex>
										<v-flex md9>
											<v-select solo v-model="form.role" :rules="[formRules.required]" :items="roleList" item-text='text' item-value="value"></v-select>
										</v-flex>
										<template v-if="form.role != 1 || formType=='add'">
											<v-flex md3 class="pa-3 text-right">
												Password
											</v-flex>
											<v-flex md9>
												<template v-if="formType=='add'">
													<v-text-field type="password" :rules="[formRules.required]" v-model="form.password" solo></v-text-field>
												</template>
												<template v-if="formType=='update'">
													<v-text-field type="password" v-model="form.password" solo></v-text-field>
												</template>
											</v-flex>
										</template>
									</v-layout>
								</v-container>
							</v-card-text>
						</template>
						<template v-else>
							<v-container>
								Are you sure you want to <i><b>Delete</b></i> this user?
							</v-container>
						</template>
					</v-form>
					<v-card-actions>
						<template v-if="form.role != 1">
							<v-btn v-if="formType=='update'" @click="deleteUser()">Delete</v-btn>
						</template>
						<v-spacer></v-spacer>
						<v-btn @click="adduser=false">Cancel</v-btn>
						<v-btn  @click="submitForm" v-if="formType=='add'">Save</v-btn>
						<template v-else>
							<template v-if="form.role != 1 && formType=='update'">
								<v-btn @click="submitForm">Update</v-btn>
							</template>
							<template v-if="form.role != 1 && formType=='delete'">
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
			this.fetchUsers();
		},
		data: function(){
            return {
				items: [
					{
						text: 'User Management',
						disabled: false,
					},
					{
						text: 'Users',
						disabled: false,
					},
				],
				userlist : [],
				adduser : false,
				form : {
					id : '',
					name : '',
					email : '',
					role : '',
					password : ''
				},
				roleList : [],
				formType : 'add',
			}
		},
		methods : {
			
			fetchUsers : function(){
				let _this = this;
				_this.userlist = [],
				axios.create({
					headers : {
						'Authorization' : `Bearer ${this.token}`
					}
				})
				.get(this.apiUrl + '/users/list')
				.then(function(res){
					_this.userlist = res.data.data;
				})
			},
			openModal: function(){
				if(this.form.name != ''){
					this.$refs.vForm.reset();
				}
				this.formType = 'add';
				this.adduser=true;
			},
			submitForm : function(){
				let _this = this,
				type = _this.formType,
				formData = new FormData();

				formData.append('id',_this.form.id);
				formData.append('name',_this.form.name);
				formData.append('email',_this.form.email);
				formData.append('role',_this.form.role);
				if(_this.form.password != ''){
					formData.append('password',_this.form.password);
				}
				if(_this.$refs.vForm.validate()){
					axios.create({
						headers : {
							'Authorization': `Bearer ${this.token}`
						}
					})
					.post(this.apiUrl+'/users/'+type,formData)
					.then(function(response) {
						_this.adduser = false;
						if(response.data.status){
							_this.eventHub.$emit('showSnackBar',{icon:'check',color:'success',message:response.data.message})
							_this.fetchUsers();
						}else{
							_this.eventHub.$emit('showSnackBar',{icon:'close',color:'error',message:'Error while submitting.'})							
						}
					});
				}
			},
			updateUser : function(id){
				this.formType = "update";
				this.form.id = this.userlist[id].id;
				this.form.name = this.userlist[id].fullname;
				this.form.email = this.userlist[id].email;
				this.form.role = parseInt(this.userlist[id].role);
				this.adduser = true;
			},
			deleteUser : function(id){
				this.formType = "delete";
			},
			fetchRoles : function(){
				let _this = this;
				axios.create({
					headers : {
						'Authorization' : `Bearer ${this.token}`
					}
				})
				.get(this.apiUrl + '/roles/list')
				.then(function(res){
					for(let role in res.data.data){
						_this.roleList.push({
							text : res.data.data[role].role,
							value: res.data.data[role].id
						});
					}
				})
			},
			checkRoles : function(id){
				let ret = "";
				for(let i in this.roleList){
					if(this.roleList[i].value == id){
						ret = this.roleList[i].text;
					}
				}
				return ret;
			}
		}
	};
</script>