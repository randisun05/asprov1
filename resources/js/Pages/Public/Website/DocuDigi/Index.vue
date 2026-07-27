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
            </div>
        </div>
    </section>
    <!--page Header ends-->

    <!-- Our Blogs -->
    <section id="our-blog">
        <div class="container padding_m">
            <div class="row mb-4">
            <div v-if="docu" class="document-verified">
                <h1 class="text-center">Dokumen Terverifikasi</h1>
                <table class="mt-3">
                    <tr>
                        <th>Hal: </th>
                        <th>: {{ docu.perihal }}</th>
                    </tr>

                    <tr>
                        <th>Nomor Dokumen </th>
                        <th>: {{ docu.no_surat }}</th>
                    </tr>

                    <tr>
                        <th>Jenis  </th>
                        <th>: {{ docu.jenis }}</th>
                    </tr>

                    <!-- <tr>
                        <th>Tanggal : </th>
                        <th>: {{ new Date(docu.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) }}</th>
                    </tr> -->

                    <tr v-if="docu.nipparaf">
                        <th>Diparaf Oleh</th>
                        <th>: {{ docu.nipparaf }}</th>
                    </tr>
                    <tr v-if="docu.nipparaf">
                        <th></th>
                        <th>&nbsp;&nbsp;{{ paraf.name }}</th>
                    </tr>

                    <tr>
                        <th>Ditandatangi Oleh </th>
                        <th>: {{ docu.nipttd }}</th>

                    </tr>
                    <tr>
                        <th></th>
                        <th>&nbsp;&nbsp;{{ ttd.name }}</th>

                    </tr>
                    <tr>
                        <th>Lihat Dokumen </th>
                        <th>: <a class="btn btn-sm btn-primary border-0 ms-2" data-fancybox="" :href="getDocumentUrl(docu.document)">dokumen </a></th>
                    </tr>

                </table>

            </div>
            <div v-else><span>Dokumen tidak ditemukan, waspada dokumen palsu</span></div>
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
import { partial } from "lodash";

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
        title: String,
        errors: Object,
        docu: Object,
        ttd: String,
        paraf: String,
    },

    //inisialisasi composition API
    setup() {

        // Method to get the URL of the document
        const getDocumentUrl = (DocName) => {
            return `/storage/${DocName}`;
        };


        //return
        return {
            getDocumentUrl,

        };
    },
};
</script>
