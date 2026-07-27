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
                                                            <li class="breadcrumb-item active" aria-current="page">{{ title }}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <!--page Header ends-->

                                    <section id="our-team-two" class="padding_m">
                                        <div class="container">
                                            <div class="row">
                                                <!-- Left Column -->
                                                <div class="col-md-6">
                                                    <div class="mt-3">
                                                        <h4>DATA ANGGOTA</h4>
                                                        <table class="table">
                                                            <tbody>
                                                                <tr>
                                                                    <th style="width: 40%;">Nomor Anggota</th>
                                                                    <td>{{ data.nomember }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Nama</th>
                                                                    <td>{{ data.name }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Instansi</th>
                                                                    <td>{{ data.agency }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <!-- Right Column -->
                                                <div class="col-md-6">
                                                    <div v-if="achievments.length > 0" class="mt-3">
                                                        <h4>DATA PENGHARGAAN</h4>
                                                        <table class="table">
                                                            <tbody>
                                                                <tr v-for="(data, index) in achievments" :key="index">
                                                                    <td style="width: 5%;">{{ index + 1 }}.</td>
                                                                    <td><i :class="data.icon" style="color: gold;"></i></td>
                                                                    <td>{{ data.title }}
                                                                        <span v-if="data.document">
                                                                            <a :href="`/storage/${data.document}`"
                                                                                target="_blank" class="badge bg-success">
                                                                                Lihat Sertifikat
                                                                            </a>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div v-if="events.length > 0" class="mt-5">
                                                        <div class="d-flex align-items-center">
                                                            <h4 class="me-3">DATA AKTIFITAS</h4>
                                                            <a :href="`/certificates/search`"
                                                                class="badge bg-warning">
                                                                Cari Sertifikat
                                                            </a>
                                                        </div>
                                                        <table class="table">
                                                            <tbody>
                                                                <tr v-for="(event, index) in events" :key="index">
                                                                    <td style="width: 5%;">{{ index + 1 }}.</td>
                                                                    <td>{{ event.event.title }} sebagai {{ event.title }}
                                                                        <span v-if="event.certificate">
                                                                            <a :href="`/certificates/${event.certificate.id}/view`"
                                                                                target="_blank" class="badge bg-success">
                                                                                Lihat Sertifikat
                                                                            </a>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <!--Testimonials Ends-->
                                    <iframe id="downloadCard" style="display: none;"></iframe>
                                </template>

            <script>
            //import layout
            import LayoutWebsite from '../../../Layouts/Website.vue';


            //import reactive
            import {
                reactive, ref, onUnmounted
            } from 'vue'

            //import Head from Inertia
            import {
                Head,
                Link,
            } from '@inertiajs/vue3';

            //import inertia adapter
            import { router } from '@inertiajs/vue3';

            //import sweet alert2
            import Swal from 'sweetalert2';

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
                    data: Object,
                    achievments: Array,
                    events: Array
                },

                setup() {


                    // Return
                    return {

                    };
                }
            }

            </script>
