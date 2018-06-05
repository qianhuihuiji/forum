<template>
    <button class="btn" :class="active ? 'btn-primary' : 'btn-default'" @click="subscribe">Subscribe</button>
</template>

<script>
    export default {
        data() {
          return {
              active: false
          }
        },
        methods:{
            subscribe(){
                axios.post(location.pathname + '/subscriptions');

                flash('Subscribed');
            }
        }
    }
</script>

