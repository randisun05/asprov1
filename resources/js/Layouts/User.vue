<template>
        <FlashMessages />
     <!-- navbar -->
         <Navbar />
        <div class="wrapper">

        <!-- sidebar -->
        <Sidebar />

        <main class="content">



            <!-- content -->
            <slot />

        </main>

        <!-- footer -->
        <Footer />

    </div>


    </template>

    <script>
        //import navbar
        import Navbar from "../Components/User/Navbar.vue";

        //import sidebar
        import Sidebar from '../Components/User/Sidebar.vue';

        //import footer
        import Footer from '../Components/Footer.vue';

        //import global flash message popups
        import FlashMessages from '../Components/FlashMessages.vue';

        export default {

            //register components
            components: {
                Navbar,
                Sidebar,
                Footer,
                FlashMessages
            },
        }

    </script>

    <style>

    </style>
