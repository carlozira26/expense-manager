<template>
	<v-container>
		<v-card>
			<v-card-title class="display-1 text--primary">My Expenses</v-card-title>
		</v-card>
	</v-container>
</template>
<script>
import VueCookies from 'vue-cookies';
		export default {
			components : {
				
			},
			created: function(){
				this.role = VueCookies.get(this.cookieKey).data.role;
			},
			data : function(){
				return {

				}
			}
	};
</script>