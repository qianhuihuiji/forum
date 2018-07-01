<script>
    import Replies from '../components/Replies';
    import SubscribeButton from '../components/SubscribeButton';

    export default {
        props: ['thread'],

        components: { Replies,SubscribeButton},

        data() {
            return {
                repliesCount:this.thread.replies_count,
                locked:this.thread.locked
            };
        },

        methods: {
            lock() {
                this.locked = true;

                axios.post('/locked-threads/' + this.thread.slug);
            }
        }
    }
</script>
