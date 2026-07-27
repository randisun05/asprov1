<template>

    <Head>
        <title>Kegiatan</title>
    </Head>
    <div class="px-5 shadow padding">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 col-12 mb-2 py-3">
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
                    <h3>Daftar Kegiatan</h3>
                </div>
                <div v-if="!events.data.length" class="text-center py-5 text-muted">
                    <i class="fa fa-calendar-o fa-3x mb-3 d-block" aria-hidden="true"></i>
                    Belum ada kegiatan yang tersedia saat ini.
                </div>
                <div class="row mb-4">
                    <div v-for="(event, index) in events.data" :key="index" class="col-md-4 mt-5">
                        <div class="news_item shadow">
                                <img class="image" v-if="event.image" :src="getImageUrl(event.image)" alt="Gambar" style="display: flex; justify-content: center; align-items: center; width: 100%;" />
                            <div class="news_desc">
                                <h4 class="text-capitalize font-light darkcolor" style="font-weight: bold;"><a
                                        href="#.">{{ event.title }}</a></h4>
                                <div class="mt-2 text-center">
                                    <span v-if="event.status === 'active'" class="badge bg-success">Open</span>
                                    <span v-else-if="event.status === 'closed'" class="badge bg-danger">Closed</span>
                                </div>
                                <ul class="top20 bottom20">
                                    <li>
                                        <a href="#."><i class="fa fa-calendar me-2" title="Tanggal pelaksanaa"></i>{{
                            event.date }}
                                            <a href="#." class="ms-4"> <i class="fa fa-user-o me-2"
                                                    title="Jumlah peserta"></i>
                                                {{ event.participant }}</a></a>
                                    </li>
                                    <p>Pendaftaran ditutup pada {{ event.enddate }}</p>

                                </ul>
                                <div class="text-center">
                                    <Link v-if="event.status == 'active'" :href="`/user/events/${event.slug}`" title="join"
                                        class="button btnprimary" type="button">
                                    Join
                                    </Link>
                                    <Link v-else :href="`/user/events/${event.slug}`" title="join"
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
        events: Object,

    },



    //inisialisasi composition API
    setup() {

        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        //define method search
        const handleSearch = () => {
            router.get('/user/events', {

                //send params "q" with value from state "search"
                q: search.value,
            });
        }

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        //return
        return {
            search,
            handleSearch,
            getImageUrl,

        }
    }
}

</script>


