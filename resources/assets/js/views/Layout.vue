<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <nav>
                <ul class="list-inline">
                    <li>
                        <router-link :to="{ name: 'home' }">Home</router-link>
                    </li>
                    <li v-if="authenticated && user" >
                        <router-link :to="{ name: 'map' }">Map</router-link>
                    </li>
                    <li v-if="authenticated && user" class="pull-right">
                        <router-link :to="{ name: 'logout' }">Logout</router-link>
                    </li>
                    <li v-if="!authenticated" class="pull-right">
                        <router-link :to="{ name: 'login' }">Login</router-link>
                    </li>
                    <li v-if="!authenticated" class="pull-right">
                        <router-link :to="{ name: 'register' }">Register</router-link>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="panel-body">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            authenticated: auth.check(),
            user: auth.user
        };
    },

    mounted() {
        Event.$on('userLoggedIn', () => {
            this.authenticated = true;
            this.user = auth.user;
        });
        Event.$on('userLoggedOut', () => {
            this.authenticated = false;
            this.user = null;
        });
    },
}
</script>
