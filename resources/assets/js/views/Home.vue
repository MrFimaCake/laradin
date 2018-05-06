<template>
    <div>
        <div v-if="readyToRender">
            <h1 v-if="renderStart">Congratulations!</h1>
            Click on "Map" item of top menu to 
            <span v-if="renderStart">start</span><span v-if="!renderStart">continue</span> creating shapes!
        </div>
    </div>
</template>

<script>
export default {
    data() {
        let hasHistoryParam = typeof this.$route.params.cleanHistory !== 'undefined',
            showHello = hasHistoryParam && this.$route.params.cleanHistory === true;
        return {
            readyToRender: hasHistoryParam,
            renderStart: showHello
        };
    },
    mounted() {
        if (!this.readyToRender) {
            this.loadShapeCount();
        }
    },
    methods: {
        loadShapeCount() {
            this.readyToRender = false;
            axios.get('/api/shapes/count')
                .then((response) => {
                    this.renderStart = response.data.count === 0;
                    this.readyToRender = true;
                    
                })
                .catch(({response}) => {
                    if (response.status === 401) {
                        this.$router.push({
                            name:'login'
                        });
                    } else {
                        this.renderStart = true;
                        this.readyToRender = true;
                    }
                });
        }
    }
}
</script>