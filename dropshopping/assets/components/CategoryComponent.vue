<template>
    <div class="col-lg-3">
        <div class="hero__categories">
            <div class="hero__categories__all">
                <i class="fa fa-bars"></i>
                <span>All Categories</span>
            </div>
            <ul>
                <li v-for="(category, index) in categories" :key="index">
                    <a v-bind:href="'/category/search/' + category.id">{{category.name}}</a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                message: "A list of words",
                categories: []
            };
        },
        methods : {
            read()
            {
                return  this.$http.get('/category/list').then((response) => {
                    this.categories = response.data;
                    console.log(response);
                })
            },
        },
        mounted() {
            this.read();
        }
    };
</script>

<style>
    .center {
        text-align: center;
    }
</style>