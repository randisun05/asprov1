<template>

    <Head>
        <title>{{ title }}</title>
    </Head>

    <!--page Header-->
    <section class="page-header parallaxie padding_top center-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-titles text-center">
                        <h2 class="whitecolor font-light bottom30">
                            {{ title }}
                        </h2>
                        <ul class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item">
                                <a href="/">Beranda</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ title }}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-3"></div>

                <div class="col-sm-6 mb-4">
                    <div class="text-center">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search"
                                    placeholder="masukkan kata kunci dan enter..." />
                                <span class="input-group-text border-0 shadow">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--page Header ends-->

    <!-- Our Blogs -->
    <section id="our-blog">
        <div class="container padding_m">
            <div class="row mb-4">
                <div v-for="(event, index) in events.data" :key="index" class="col-md-4 mt-5">
                    <div class="news_item shadow">
                        <img class="image" style="width: 100%;" v-if="event.image" :src="getImageUrl(event.image)" alt="Gambar" />
                        <div class="news_desc">
                            <h4 class="text-capitalize font-light darkcolor"
                                style="font-weight: bold; text-align: center">
                                {{ event.title }}
                            </h4>
                            <div class="mt-2 text-center">
                                <span v-if="event.status === 'active'" class="badge bg-success">Open</span>
                                <span v-else-if="event.status === 'closed'" class="badge bg-danger">Closed</span>
                            </div>
                            <ul class="top20 bottom20 ">
                                <li>
                                    <a href="#."> <i class="fa fa-calendar me-2" title="Tanggal pelaksanaa"></i>{{
                             event.date }}</a>

                             <a class="ms-4" href="#.">
                                        <i class="fa fa-user-o me-2" title="Jumlah peserta"></i>
                                        {{ event.participant }}</a>
                                </li>
                                <li>

                                </li>

                                <p>Pendaftaran ditutup pada {{ event.enddate }}</p>

                            </ul>

                            <div class="text-center">
                                <Link v-if="event.status == 'active'" :href="`/events/${event.slug}`" title="join"
                                    class="button btnprimary" type="button">
                                Join
                                </Link>
                                <Link v-else :href="`/events/${event.slug}`" title="join"
                                    class="button btnprimary" type="button">
                                View
                                </Link>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <!-- Center align pagination -->
                    <Pagination :links="events.links" align="center" />
                </div>
            </div>
        </div>
    </section>
    <!--Our Blogs Ends-->
</template>

<script>
//import layout
import LayoutWebsite from "../../../../Layouts/Website.vue";

//import component pagination
import Pagination from "../../../../Components/Pagination.vue";

//import Heade and Link from Inertia
import { Head, Link } from "@inertiajs/vue3";

//import ref from vue
import { ref } from "vue";

//import inertia adapter
import { router } from "@inertiajs/vue3";

export default {
    //layout
    layout: LayoutWebsite,

    //register component
    components: {
        Head,
        Link,
        Pagination,
    },

    //props
    props: {
        title: Object,
        errors: Object,
        events: Object,
    },

    //inisialisasi composition API
    setup() {
        //define state search
        const search = ref(
            "" || new URL(document.location).searchParams.get("q")
        );

        //define method search
        const handleSearch = () => {
            router.get("/events", {
                //send params "q" with value from state "search"
                q: search.value,
            });
        };

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        };


        //return
        return {
            search,
            handleSearch,
            getImageUrl,
        };
    },
};
</script>
