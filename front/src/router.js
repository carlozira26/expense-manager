import Vue from 'vue'
import Router from 'vue-router'
import mixins from './mixins';
import VueCookies from 'vue-cookies';
import LoginModule from './components/login.vue';

import NavigationModule from './components/navigation.vue';
import DashboardModule from './components/dashboard.vue';
import UsersModule from './components/users.vue';
import RolesModule from './components/roles.vue';

import LogoutModule from './components/logout.vue';


Vue.use(Router)

const router =  new Router({
    routes: [
        {
            path: '/',
            name: 'root',
            redirect : '/dashboard',
            component: NavigationModule,
            children : [
                {
                    path : '/dashboard',
                    name : 'dashboard',
                    component: DashboardModule,
                },
                {
                    path : '/roles',
                    name : 'roles',
                    component: RolesModule,
                },
                {
                    path : '/users',
                    name : 'users',
                    component: UsersModule,
                },
                {
                    path : '/logout',
                    component : LogoutModule
                },
            ]
        },
        
        {
            path: '/login',
            name: 'login',
            component: LoginModule
        },
    ]
})

router.beforeEach((to, from, next) => {
    let cookie = VueCookies.get( mixins.data().cookieKey );
    if(cookie == null && to.name != 'login'){
        router.push('/login');
    }else if(cookie != null && to.name == 'login'){
        router.push('/')
    }else{
        next();
    }
});

export default router;
