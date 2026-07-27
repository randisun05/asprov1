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
                        <h2 class="whitecolor font-light bottom30">{{ title }}</h2>
                        <ul class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="/events">Events</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--page Header ends-->


    <!-- Our Blogs -->
    <section id="our-blog">
    <div class="container padding">
        <div class="card shadow mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="news_item text-center mt-5 px-2">
                        <div class="cbp-item web print graphic">
                            <img class="image" style="width: 100%;" v-if="event.image" :src="getImageUrl(event.image)" alt="Gambar" />
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="news_item">
                        <div class="news_desc">
                            <h3 class="text-capitalize font-light darkcolor">{{ event.title }}</h3>
                            <ul class="meta-tags top20 bottom20">
                                <h5 class="darkcolor">Tanggal Pelaksanaan : {{ event.date }}</h5>
                                <h5 class="darkcolor">Kuota Peserta : {{ event.participant }}</h5>
                                <h5 class="darkcolor">Penutupan Pendaftaran : {{ event.enddate }}</h5>
                            </ul>

                            <div v-html="event.body" style="text-align:justify;text-justify: "></div>
                            <div class="text-center">
                                <div class="button-group gap-3">
                                    <a v-if="event.status === 'active' && event.category !== 'Tryout'" href="/user/events/" class="button btnprimary">
                                       Join
                                    </a>
                                    <a v-if="event.status === 'active' && event.category !== 'Tryout'" :href="`/events/${event.slug}/absen`" class="button btnsecondary">
                                        <i class="fas fa-clipboard-check"></i> Absen
                                    </a>
                                    <a v-if="event.category !== 'Tryout'"  :href="`/events/${event.slug}/certificate`" class="button btnprimary">
                                       Cari Sertifikat
                                    </a> <a v-if="event.category === 'Tryout' && event.status !== 'closed'"  :href="`/user/tryouts`" class="button btnprimary">
                                       Join
                                    </a>
                                     <a v-if="event.category === 'Tryout'"  :href="`/events/${event.slug}/dashboard`" class="button btnprimary">
                                       Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



    <!--Our Blogs Ends-->

</template>

<script>
//import layout
import LayoutWebsite from '../../../../Layouts/Website.vue';

//import Heade and Link from Inertia
import {
    Head,
    Link
} from '@inertiajs/vue3';

//import ref from vue
import {
    ref
} from 'vue';

//import inertia adapter
import { router } from '@inertiajs/vue3';

export default {

    //layout
    layout: LayoutWebsite,

    //register component
    components: {
        Head,
        Link,
    },

    //props
    props: {
        title: Object,
        errors: Object,
        event: Object,
    },

    //inisialisasi composition API
    setup() {


        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        //return
        return {
            getImageUrl,
        }
    }

}

</script>
