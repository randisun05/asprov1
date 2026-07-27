<template>

    <Head>
        <title>{{ title }}</title>
    </Head>

    <!-- Our Blogs -->
    <section id="our-blog">
        <div class="container padding">
            <div class="card shadow mb-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row py-4">
                            <div class="col-md-2 col-12 mb-2 ms-4 mb-4">
                                <Link href="/user/events/" class="btn btn-md btn-primary border-0 shadow w-100"
                                    type="button"><i class="fa fa-arrow-left"></i>
                                Kembali</Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="news_item text-center mt-5 px-2">
                            <div class="cbp-item web print graphic">
                                <img class="image"
                                    style="display: flex; justify-content: center; align-items: center; width: 100%;"
                                    v-if="event.image" :src="getImageUrl(event.image)" alt="Gambar" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="news_item">
                            <div class="news_desc">
                                <h3 class="text-capitalize font-light darkcolor">{{ event.title }}</h3>
                                <ul class="meta-tags top20 bottom20">
                                    <h5 class="darkcolor mb-2">Tanggal Pelaksanaan : {{ event.date }}</h5>
                                    <h5 class="darkcolor mb-2">Kuota Peserta : {{ event.participant }}</h5>
                                    <h5 class="darkcolor mb-2">Penutupan Pendaftaran : {{ event.enddate }}</h5>
                                    <h5 class="darkcolor mb-2">Pelaksanaan : {{ event.place }}</h5>
                                </ul>

                                <div v-html="event.body" style="text-align:justify;text-justify: "></div>

                                <div v-if="event.file === 'Y' && status != 1" class="form-group bottom35 mt-1">
                                    <label>Upload File ( Bentuk File .Pdf Maks 2Mb)</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" @change="updateDocument"
                                                accept=".pdf">
                                        </div>

                                            <div v-if="errors.document" class="alert alert-danger mt-2">
                                                {{ errors.document }}
                                            </div>
                                            <div v-if="errors[0]" class="alert alert-danger mt-2">
                                                {{ errors[0] }}
                                            </div>

                                    </div>
                                <div class="text-center">
                                    <button v-if="event.status === 'active' && status == 0" @click.prevent="join(event.id)"
                                        class="button btnprimary border-0 me-2 mt-4"> Join </button>
                                        <button v-if="event.status === 'active' && status == 1"
                                        class="button btnthrid border-0 me-2 mt-4"> Anda Sudah Terdaftar</button>
                                        <button v-if="event.status === 'active' && status == 1"
                                        class="button btnprimary border-0 me-2 mt-4" @click.prevent="info(event.slug)">Informasi Lebih Lanjut</button>
                                </div>

                                <div class="text-center" v-if="event.absen === 'Y' && detailEvent">
                                    <button v-if="status == 1 && hadir != 1 " @click.prevent="absen(event.id)"
                                        class="button btnprimary border-0 me-2 mt-4">Absen Kehadiran</button>
                                        <button v-if="hadir == '1'"
                                        class="button btnthrid border-0 me-2 mt-4">Telah Melakukan Absensi</button>
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
import LayoutUser from '../../../Layouts/User.vue';

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
    },

    //props
    props: {
        title: Object,
        errors: Object,
        event: Object,
        status: Object,
        detailEvent: Object,
        hadir: Object,
    },

    //inisialisasi composition API
    setup() {

        //define form state
        const form = reactive({
                document: '',
            });

            const join = (id) => {
    Swal.fire({
        title: 'Anda akan mendaftar pada event ini?',
        text: "",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Daftarkan!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(`/user/events/${id}/join`, {
                // Assuming form.document is available in the current scope
                document: form.document,
            }, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Anda sudah berhasil bergabung sebagai peserta',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            });
        }
    });
}


const absen = (id) => {
    Swal.fire({
        title: 'Anda akan melakukan absensi pada event ini?',
        text: "",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Tandai Hadir!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(`/user/events/${id}/absen`, {
                // Assuming form.document is available in the current scope
                document: form.document,
            }, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Anda sudah berhasil melakukan absensi',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            });
        }
    });
}



        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        const info = (id) => {
            return router.get(`/user/events/${id}/info`);

        }


        // Method to update the document file
        const updateDocument = (event) => {
            form.document = event.target.files[0];
        };

        //return
        return {
            getImageUrl,
            join,
            updateDocument,
            form,
            absen,
            info
        }
    }

}

</script>
