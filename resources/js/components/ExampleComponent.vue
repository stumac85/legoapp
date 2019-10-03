<template>
    <div class="jumbotron">
        <h1 class="display-4">{{ jumbodata.title }}</h1>
        <p class="lead">{{ jumbodata.lead }}</p>
        <hr class="my-4">
        <p>{{ jumbodata.detail }}</p>
        <a class="btn btn-primary btn-lg" :href=jumbodata.thelink role="button">{{ jumbodata.btn_text }}</a>
    </div>
</template>

<script>
    export default {
        props: {
        jumbodata: null,
        }
    }
</script>
