<template>
</template>
<script>
export default {
    mounted() {
        if (auth.check()) {
        
            axios.post('/api/logout', {})
                .then(({data}) => {
                    auth.logout();

                    this.$router.push('/login');
                })
                .catch(({response}) => {                    
                    alert(response.data.message);
                });
        }

        this.$router.push('/login');
    }
}
</script>