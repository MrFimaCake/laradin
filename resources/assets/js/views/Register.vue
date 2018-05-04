<template>
    <div>
        <div class="alert alert-danger" v-if="error && !success">
            <p>There was an error, unable to complete registration.</p>
            <ul>
                <li v-if="errorModel.name">{{ errorModel.name }}</li>
                <li v-if="errorModel.email">{{ errorModel.email }}</li>
                <li v-if="errorModel.password">{{ errorModel.password }}</li>
            </ul>
        </div>
        <div class="alert alert-success" v-if="success">
            <p>Registration completed. You can now <router-link :to="{name:'login'}">sign in.</router-link></p>
        </div>
        <form autocomplete="off" @submit.prevent="register" v-if="!success" method="post">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" class="form-control" v-model="name" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" class="form-control" placeholder="user@example.com" v-model="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" class="form-control" v-model="password" required>
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
</template>
<script> 
    export default {
        data(){
            return {
                name: '',
                email: '',
                password: '',
                error: false,
                success: false,
                errorModel: {
                    name: null,
                    email: null,
                    password: null,
                }
            };
        },
        methods: {
            register(){
                let data = {
                    name: this.name,
                    email: this.email,
                    password: this.password
                };
                axios.post('/api/register', data)
                    .then(({data}) => {
                        this.$router.push('/login');
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