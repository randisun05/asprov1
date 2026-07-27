<template>

<div class="wrapper">

    <FlashMessages />

    <!-- sidebar -->
    <Sidebar />

    <main class="content">

        <!-- navbar -->
        <Navbar />

        <!-- content -->
        <div  class="page-wrapper" :key="$page.url">
            <slot />
        </div>

    </main>

</div>

</template>

<script>
    //import navbar
    import Navbar from "../Components/Navbar.vue";

    //import sidebar
    import Sidebar from '../Components/Sidebar.vue';

    //import global flash message popups
    import FlashMessages from '../Components/FlashMessages.vue';

    export default {

        //register components
        components: {
            Navbar,
            Sidebar,
            FlashMessages
        },
    }

</script>

<style>

</style>
