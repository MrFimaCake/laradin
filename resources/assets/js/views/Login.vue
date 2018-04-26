<template>
    <div>
        <div class="alert alert-danger" v-if="error">
            <p>There was an error, unable to sign in with those credentials.</p>
        </div>
        <form autocomplete="off" @submit.prevent="login" method="post">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" class="form-control" placeholder="user@example.com" v-model="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" class="form-control" v-model="password" required>
            </div>
            <button type="submit" class="btn btn-default">Sign in</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            username: '',
            password: '',
            error: null
        };
    },

    methods: {
        login() {
            let data = {
                username: this.username,
                password: this.password
            };

            
            axios.post('/api/login', data)
                .then(({data}) => {
                    auth.login(data.token, data.user);

                    this.$router.push('/dashboard');
                })  
                .catch(({response}) => {                    
                    alert(response.data.message);
                });
        }
    }
}
</script>