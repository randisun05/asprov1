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


                    <section id="our-testimonial" class="padding_m">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 text-center">
                                    <div class="heading-title bottom30">
                                        <span>
                                            <h4>Masukan NIP</h4>
                                        </span>
                                        <div class="col-md-6 col-12 mb-2 mx-auto">
                                            <form @submit.prevent="handleSearch">
                                                <div class="input-group">
                                                    <input type="text" class="form-control border-0 shadow" v-model="search"
                                                        placeholder="masukkan kata kunci dan enter...">
                                                    <button type="submit" class="input-group-text border-0 shadow" style="cursor: pointer;">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                      </div>
                                </div>
                            </div>

                            <div v-if="datas.length > 0" class="card border-0 shadow">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                            <thead class="thead-dark">
                                                <tr class="border-0">
                                                    <th class="border-0 rounded-start" style="width:5%">No.</th>
                                                    <th class="border-0">NIP</th>
                                                    <th class="border-0">Nama</th>
                                                    <th class="border-0">Kegiatan</th>
                                                    <th class="border-0">Action</th>
                                                </tr>
                                            </thead>
                                            <div class="mt-2"></div>
                                            <tbody>
                                                <tr v-for="(data, index) in datas" :key="data.id">
                                                    <td class="fw-bold text-center">
                                                        {{ index + 1 }}
                                                    </td>
                                                    <td>{{ data.nip }}</td>
                                                    <td>{{ data.name }}</td>
                                                    <td>{{ data.body }}</td>
                                                    <td> <span>
                                                            <a :href="`/certificates/${data.id}/view`" target="_blank"
                                                                class="badge bg-success">
                                                                Lihat Sertifikat
                                                            </a>
                                                        </span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>


                    </section>
                    <!--Testimonials Ends-->


                </template>

            <script>
            //import layout
            import LayoutWebsite from '../../../../Layouts/Website.vue';

            //import ref
            import {
                ref
            } from 'vue'

            //import Head from Inertia
            import {
                Head
            } from '@inertiajs/vue3';

            //import inertia adapter
            import { router } from '@inertiajs/vue3';

            export default {

                //layout
                layout: LayoutWebsite,

                //register component
                components: {
                    Head,

                },

                //props
                props: {
                    title: {
                        type: String,
                        required: true,
                    },
                    datas: {
                        type: Array,
                        default: () => [],
                    }

                },

                //inisialisasi composition API
                setup() {

                    //define state search
                    const search = ref('' || (new URL(document.location)).searchParams.get('q'));

                    //define method search
                    const handleSearch = () => {
                        router.get('/certificates/filter', {

                            //send params "q" with value from state "search"
                            q: search.value,
                        });
                    }

                    //return
                    return {
                        search,
                        handleSearch,
                    }
                }



            }

            </script>
