import VueRouter from 'vue-router';


let routes = [
    {
        name: 'home',
        path: '/',
        component: require('./views/Home.vue')
    },
    {
        name: 'login',
        path: '/login',
        component: require('./views/Login.vue')
    },
    {
        name: 'logout',
        path: '/logout',
        component: require('./components/Logout.vue')
    },
    {
        name: 'map',
        path: '/dashboard',
        component: require('./views/Dashboard.vue'),
        meta: { middlewareAuth: true }
    }
];


const router = new VueRouter({
    routes
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.middlewareAuth)) {                
        if (!auth.check()) {
            next({
                path: '/login',
                query: { redirect: to.fullPath }
            });

            return;
        }
    }

    next();
});

export default router;