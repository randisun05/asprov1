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
                <div v-for="(merchan, index) in merchans.data" :key="index" class="col-md-4 mt-5">
                    <div class="news_item shadow">
                                <img class="image" v-if="merchan.image" :src="getImageUrl(merchan.image)" alt="Gambar" style="width: 100%;"/>
                            <div class="news_desc">
                                <h4 class="text-capitalize font-light darkcolor" style="font-weight: bold;"><a
                                        href="#.">{{ merchan.title }}</a></h4>
                                        <div class="col product-details">
                                    <h4 class="product-name">{{ merchan.subtitle }}</h4>
                                    <p class="product-price">Rp.{{ merchan.price }}</p>
                                    <p class="product-color">Warna : {{ merchan.color }}</p>
                                    <div v-html="merchan.how"></div>

                                </div>
                                <div class="text-center mt-4">
                                    <div class="text-center mt-4">
                                        <a :href="cleanUrl(merchan.how)" title="join"
                                            class="button btnprimary" type="button" target="_blank">
                                        Beli
                                    </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="text-center">
                    <!-- Center align pagination -->
                    <Pagination :links="merchans.links" align="center" />
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
        merchans: Object,
    },

    //inisialisasi composition API
    setup() {
        //define state search
        const search = ref(
            "" || new URL(document.location).searchParams.get("q")
        );

        //define method search
        const handleSearch = () => {
            router.get("/merchans", {
                //send params "q" with value from state "search"
                q: search.value,
            });
        };

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        };
        // Method to clean HTML from URLs
        const cleanUrl = (url) => {
            const div = document.createElement('div');
            div.innerHTML = url;
            return div.textContent || div.innerText || "";
        };


        //return
        return {
            search,
            handleSearch,
            getImageUrl,
            cleanUrl,
        };
    },
};
</script>
