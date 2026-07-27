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

    <style scoped>
    /* The member navbar (.userbg in Components/User/Navbar.vue) is
       position: fixed, so it's out of document flow and overlays whatever
       renders right after it. Give the content area the same top clearance
       already used elsewhere in the app for this fixed-navbar pattern
       (see .padding_top / .padding in the site stylesheet). Scoped so it
       doesn't leak onto Admin.vue, which reuses the same .wrapper/.content
       class names but already positions its navbar differently. */
    .wrapper {
        padding-top: 7.5rem;
    }
    </style>
