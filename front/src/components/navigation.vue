<template>
	<div>
		<template>
			<v-toolbar color="green darken-4" dark app fixed>
				<v-spacer></v-spacer>
				<div class="mr-4">Welcome to Expense Manager</div>
				<v-btn tile :to="'/logout'">
					Log Out
				</v-btn>
			</v-toolbar>
			<v-navigation-drawer v-model="appnav" app dark permanent width="300">
				<v-list>
					<v-list-item two-line>
						<v-list-item-avatar>
							<img src="https://randomuser.me/api/portraits/men/1.jpg">
						</v-list-item-avatar>
						<v-list-item-content>
							<v-list-item-title>
								{{name}}
							</v-list-item-title>
							<v-list-item-subtitle>
								{{checkRoles(role)}}
							</v-list-item-subtitle>
						</v-list-item-content>
					</v-list-item>
					<br>
					<v-list-item active-class="highlighted green darken-4" class="v-list-item" to="/dashboard">
						<v-list-item-action>
							<v-icon>dashboard</v-icon>
						</v-list-item-action>
						<v-list-item-title>Dashboard</v-list-item-title>
					</v-list-item>

					<v-list-group prepend-icon="account_circle" active-class="white--text" v-if="role == 1">
						<template v-slot:activator>
							<v-list-item-title>User Management</v-list-item-title>
						</template>
						<v-list-item active-class="highlighted green darken-4 white--text" link to="/roles">
							<v-list-item-title>Roles</v-list-item-title>
						</v-list-item>
						<v-list-item active-class="highlighted green darken-4 white--text" link to="/users">
							<v-list-item-title>Users</v-list-item-title>
						</v-list-item>
					</v-list-group>

					<v-list-group prepend-icon="insert_chart" active-class="white--text">
						<template v-slot:activator>
							<v-list-item-content>
								<v-list-item-title>Expense Management</v-list-item-title>
							</v-list-item-content>
						</template>

						<v-list-item link to="/expense-category" v-if="role == 1" active-class="highlighted green darken-4 white--text">
							<v-list-item-title>Expense Categories</v-list-item-title>
						</v-list-item>
						<v-list-item link to="/expenses" active-class="highlighted green darken-4 white--text">
							<v-list-item-title>Expenses</v-list-item-title>
						</v-list-item>
					</v-list-group>
					<template v-if="role!=1">
						<v-list-item active-class="highlighted green darken-4" class="v-list-item" @click="changePassModal">
							<v-list-item-action>
								<v-icon>lock</v-icon>
							</v-list-item-action>
							<v-list-item-title>Change Password</v-list-item-title>
						</v-list-item>
					</template>
				</v-list>
			</v-navigation-drawer>
		</template>
		<v-dialog v-model="changePassword" persistent width="400">
			<v-card>
				<v-form ref="vForm" v-on:submit.prevent="submitPassword">
					<v-card-title class="green darken-4 white--text">Change Password</v-card-title>
					<v-card-text>
					<h3 class="subheading red--text text-center pa-1" v-if="errorPass"><v-icon color="red">error_outline</v-icon> {{ errmessage }}</h3>
				</v-card-text>
					<v-card-text class="pa-2">
						<label>Old Password</label>
						<v-text-field dense solo v-model="form.oldpass" :rules="[formRules.required]" type="password"></v-text-field>
						<label>New Password</label>
						<v-text-field dense solo v-model="form.newpass" :rules="[formRules.required]" type="password"></v-text-field>
						<label>Confirm Password</label>
						<v-text-field dense solo v-model="form.confirmpass" :rules="[formRules.required, passwordConfirmationRule]" type="password"></v-text-field>
					</v-card-text>
					<v-card-actions>
						<v-spacer></v-spacer>
						<v-btn @click="changePassword=false">Cancel</v-btn>
						<v-btn type="submit">Submit</v-btn>
					</v-card-actions>
				</v-form>
			</v-card>
		</v-dialog>
		<v-content>
			<router-view></router-view>
		</v-content>
		<v-snackbar v-model="sbar" :color="snackBarColor" right top>
			<v-icon class="white--text" left>{{ icon }}</v-icon>
			{{ message }}
			<v-btn text icon @click.native="sbar = false"> &times; </v-btn>
		</v-snackbar>
	</div>
</template>
<script>
import VueCookies from 'vue-cookies';
import axios from "axios";

export default {
	created : function(){
		this.role = VueCookies.get(this.cookieKey).data.role;
		this.user = VueCookies.get(this.cookieKey).data;
		this.name = this.user.fullname;
		this.fetchRoles();

		this.eventHub.$on('showSnackBar', val =>{
			this.icon = val.icon;
			this.snackBarColor = val.color;
			this.message = val.message;
			this.sbar = true;
		});
	},
	components : {

	},
	computed: {
		passwordConfirmationRule() {
			return () => (this.form.newpass === this.form.confirmpass) || 'Password must match'
		},
	},
	data : () => ({
		connect : '',
		appnav : false,
		globalLoading : false,
		name : '',
		snackBarColor: '',
		sbar: '',
		message: '',
		icon: '',
		roleList : [],
		changePassword : false,
		errorPass : false,
		errmessage : '',

		form : {
			oldpass: '',
			newpass: '',
			confirmpass: ''
		},
	}),

	methods : {
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
			return ret.toUpperCase();
		},
		changePassModal : function(){
			if(this.$refs.vForm){
				this.$refs.vForm.reset();
			}
			this.changePassword = true;
		},
		submitPassword : function(){
			let _this = this,
			formData = new FormData();
			_this.errorPass = false;
			if(this.$refs.vForm.validate()){
				formData.append('userid', _this.user.id);
				formData.append('oldpass', _this.form.oldpass);
				formData.append('newpass', _this.form.newpass);
				axios.create({
					headers : {
						'Authorization' : `Bearer ${this.token}`
					}
				})
				.post(this.apiUrl + '/users/changepass',formData)
				.then(function(res){
					if(res.data.status){
						_this.eventHub.$emit('showSnackBar',{icon:'check',color:'success',message:res.data.message});
						_this.changePassword = false;
					}else{
						_this.errmessage = res.data.message;
					}
				});
			}
		}
	}
};	
</script>