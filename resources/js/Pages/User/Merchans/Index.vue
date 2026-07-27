<template>

    <Head>
        <title>Merchandise</title>
    </Head>
    <div class="px-4 shadow mb-5 mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1 col-12 mb-2">
                    </div>
                    <div class="col-md-6 col-12 mb-2 ms-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search"
                                    placeholder="masukkan kata kunci dan enter...">
                                <span class="input-group-text border-0 shadow">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- Our Blogs -->
        <section id="our-blog">
            <div class="padding_m card">
                <div class="row text-center">
                    <h3>Daftar Merchandise</h3>
                </div>
                <div v-if="!merchans.data.length" class="text-center py-5 text-muted">
                    <i class="fa fa-shopping-bag fa-3x mb-3 d-block" aria-hidden="true"></i>
                    Belum ada merchandise yang tersedia saat ini.
                </div>
                <div class="row mb-4">
                    <div v-for="(merchan, index) in merchans.data" :key="index" class="col-md-4 mt-5">
                        <div class="news_item shadow">
                                <img class="image" v-if="merchan.image" :src="getImageUrl(merchan.image)" alt="Gambar" style="width: 100%; display: flex; justify-content: center; align-items: center;"/>
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
                                    <a :href="cleanUrl(merchan.how)" title="join"
                                            class="button btnprimary" type="button" target="_blank">
                                        Beli
                                    </a>
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
    </div>
</template>

<script>
//import layout
import LayoutUser from '../../../Layouts/User.vue';

//import component pagination
import Pagination from '../../../Components/Pagination.vue';

//import Heade and Link from Inertia
import {
    Head,
    Link
} from '@inertiajs/vue3';

//import ref from vue
import {
    ref, reactive
} from 'vue';

//import inertia adapter
import { router } from '@inertiajs/vue3';

//import sweet alert2
import Swal from 'sweetalert2';

export default {
    //layout
    layout: LayoutUser,

    //register component
    components: {
        Head,
        Link,
        Pagination
    },

    //props
    props: {
        merchans: Object,
    },



    //inisialisasi composition API
    setup() {

        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        //define method search
        const handleSearch = () => {
            router.get('/user/merchans', {

                //send params "q" with value from state "search"
                q: search.value,
            });
        }

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

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
        }
    }
}

</script>


