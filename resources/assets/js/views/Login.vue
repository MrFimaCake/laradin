<template>
    <div>
        <div class="alert alert-danger" v-if="error">
            <p>There was an error, unable to sign in with those credentials</p>
            <ul>
                <li v-if="errorModel.username">{{ errorModel.username }}</li>
                <li v-if="errorModel.password">{{ errorModel.password }}</li>
            </ul>
        </div>
        <div class="alert alert-success" v-if="newUserLogin">
            Successful registration, welcome to "Test app". Please, input login credentials to login. 
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
        let newUserLogin = typeof this.$route.params.newUserLogin === 'boolean' 
            && this.$route.params.newUserLogin;
        return {
            username: '',
            password: '',
            error: null,
            errorModel: {
                username: null,
                password: null
            },
            newUserLogin 
        };
    },

    methods: {
        login() {
            let data = {
                username: this.username,
                password: this.password
            };

            this.error = false;
            this.newUserLogin = false;
            axios.post('/api/login', data)
                .then(({data}) => {
                    let userHasShapesHistory = data.shapes !== undefined 
                        && data.shapes.count == 0;
                    auth.login(data.token, data.user);

                    this.$router.push({
                        name: 'home', 
                        params:{cleanHistory:userHasShapesHistory}
                    });
                })  
                .catch(({response}) => {
                    if (typeof response.data.errors != 'undefined') {
                        let errors = response.data.errors;
                        for (let key in errors) {
                            if (typeof this.errorModel[key] != 'undefined') {
                                this.errorModel[key] = errors[key].shift();
                            }
                        }
                        
                    }
                    this.error = true;
                });
        }
    }
}
</script>