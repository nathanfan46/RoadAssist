<style scoped>
    .action-link {
        cursor: pointer;
    }

    .m-b-none {
        margin-bottom: 0;
    }

    .services h4 {
        color: white;
    }
</style>
<template>
<div class="form-group services">
    <h4>What do you need?</h4>
    <button type="button" class="btn btn-primary btn-block" @click="goto('roadassist/request/towing')">TOWING</button>
    <button type="button" class="btn btn-primary btn-block" @click="goto('roadassist/request/lockout')">LOCKOUT</button>
    <button type="button" class="btn btn-primary btn-block" @click="goto('roadassist/request/jumpstart')">JUMP START</button>
    <button type="button" class="btn btn-primary btn-block" @click="goto('roadassist/request/fuel')">FUEL</button>
    <button type="button" class="btn btn-primary btn-block" @click="goto('roadassist/request/flattire')">FLAT TIRE</button>
    <button type="button" class="btn btn-primary btn-block" @click="goto('roadassist/request/winch')">WINCH</button>
</div>

</template>

<script>
    export default {

        mounted() {
            console.log('Component mounted.')
        },

        methods: {
            goto(dest) {
                this.$router.push({ path: dest })
            },
        }
        
    }
</script>
