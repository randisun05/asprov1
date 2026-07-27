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
            <div v-if="data" class="document-verified">
                <h1 class="text-center">Dokumen Terverifikasi</h1>
                <table class="mt-3">
                    <tr>
                        <th>Nomor Sertifikat </th>
                        <th>: {{ data.no_certificate }}</th>
                    </tr>

                    <tr>
                        <th>Nama Kegiatan </th>
                        <th>: {{ data.event.title }}</th>
                    </tr>

                    <tr>
                        <th>Atas Nama  </th>
                        <th>: {{ data.name }}</th>
                    </tr>

                    <tr>
                        <!-- <th>Lihat </th>
                        <th></th> -->
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
        data: Object,

    },

    //inisialisasi composition API
    setup() {


        const downloadCard = async (data) => {
                try {
                    // Select the iframe
                    const iframe = document.getElementById('downloadFrame');

                    // URL to download the certificate
                    const downloadUrl = `/certificates/${data.link}/view`;

                    // Set the iframe source to the download URL
                    iframe.src = downloadUrl;

                    // Add an event listener to detect when the iframe is loaded
                    iframe.onload = () => {
                        console.log('Certificate download initiated.');
                    }
                } catch (error) {
                    console.error("Error downloading certificate:", error);
                    Swal.fire('Error', 'Failed to download certificate.', 'error');
                }
            };

        //return
        return {
            downloadCard
        };
    },
};
</script>
