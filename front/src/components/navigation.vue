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
								{{role.toUpperCase()}}
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

					<v-list-group prepend-icon="account_circle" active-class="white--text">
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

						<v-list-item link to="/expense-category" active-class="highlighted green darken-4 white--text">
							<v-list-item-title>Expense Categories</v-list-item-title>
						</v-list-item>
						<v-list-item link to="/expenses" active-class="highlighted green darken-4 white--text">
							<v-list-item-title>Expenses</v-list-item-title>
						</v-list-item>
					</v-list-group>

				</v-list>
			</v-navigation-drawer>
		</template>
		<v-dialog v-model="globalLoading" persistent width="300">
			<v-card color="green darken-4" dark>
				<v-card-text>
					Loading...
					<v-progress-linear
					indeterminate
					color="white"
					class="mb-0"
					hide-overlay
					></v-progress-linear>
				</v-card-text>
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

export default {
	created : function(){
		this.role = VueCookies.get(this.cookieKey).data.role;
		this.user = VueCookies.get(this.cookieKey).data;
		this.name = this.user.fullname;

		this.eventHub.$on('showSnackBar', val =>{
			this.icon = val.icon;
			this.snackBarColor = val.color;
			this.message = val.message;
			this.sbar = true;
		});
	},
	components : {

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
	}),


	methods : {
		
	}
};	
</script>