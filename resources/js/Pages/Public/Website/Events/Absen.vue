<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <section class="page-header parallaxie padding_top center-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-titles text-center">
                        <h2 class="whitecolor font-light bottom30">{{ title }}</h2>
                        <ul class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="/events">Events</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Absensi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="our-blog">
        <div class="container padding">
            <div class="card shadow mb-4">
                <div class="row">
                    <div class="col-md-5">
                        <div class="news_item text-center mt-5 px-3">
                            <div class="cbp-item web print graphic">
                                <img class="image shadow rounded" style="width: 100%;" v-if="event.image" :src="getImageUrl(event.image)" alt="Gambar Event" />
                            </div>
                            <div class="news_desc text-left top20">
                                <h4 class="text-capitalize font-light darkcolor">{{ event.title }}</h4>
                                <p class="top10"><i class="fa fa-calendar text-primary"></i> {{ event.date }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="news_item padding_left padding_right">
                            <div class="news_desc mt-5 px-2">
                                <h3 class="text-capitalize font-light darkcolor bottom20">Form Absensi</h3>

                                <div v-if="event.absen === 'N'" class="alert alert-warning border-0">
                                    <i class="fa fa-exclamation-triangle"></i> Absensi untuk event ini ditutup atau belum dibuka.
                                </div>

                                <form v-else @submit.prevent="submitAbsen">
                                    <div class="form-group mb-3">
                                        <label class="darkcolor">NIP / Nomor Identitas (Angka Saja)</label>
                                        <input
                                            type="text"
                                            v-model="form.nip"
                                            class="form-control"
                                            placeholder="Masukkan NIP Anda"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"

                                        >
                                        <div v-if="errors.nip" class="alert alert-danger mt-2">{{ errors.nip }}</div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="darkcolor">Nama Lengkap (Sesuai Sertifikat)</label>
                                        <input type="text" v-model="form.name" class="form-control" :class="{ 'is-invalid': errors.name }" placeholder="Masukkan Nama Tanpa Gelar">
                                        <div v-if="errors.name" class="alert alert-danger mt-2">{{ errors.name }}</div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="darkcolor">Instansi / Unit Kerja</label>
                                        <input type="text" v-model="form.agency" class="form-control" :class="{ 'is-invalid': errors.agency }" placeholder="Contoh: Instansi Pusat / Daerah">
                                        <div v-if="errors.agency" class="alert alert-danger mt-2">{{ errors.agency }}</div>
                                    </div>

                                    <div v-if="errors.message" class="alert alert-danger border-0 small">
                                        {{ errors.message }}
                                    </div>

                                    <div class="text-center top30">
                                        <button type="submit" class="button btnprimary shadow w-100" :disabled="form.processing">
                                            <i class="fa fa-check-circle"></i> {{ form.processing ? 'Memproses...' : 'Kirim Absensi' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import LayoutWebsite from '../../../../Layouts/Website.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { watch } from 'vue'; // Tambahkan watch

export default {
    layout: LayoutWebsite,
    components: {
        Head,
        Link,
    },
    props: {
        title: String,
        errors: Object,
        event: {
            type: Object,
            required: true
        },
        auth: Object // Pastikan data auth di-share via middleware
    },
    setup(props) {
        // Inisialisasi form dengan data dari auth member (jika ada)
        const form = useForm({
            nip: props.auth.user?.nip || '',
            name: props.auth.user?.name || '',
            agency: props.auth.user?.agency || '',
        });

        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        };

        const handleNipInput = (event) => {
            form.nip = event.target.value.replace(/[^0-9]/g, '');
        };

        // Tampilkan Alert jika ada pesan error "sudah absen" dari backend
        watch(() => props.errors.message, (newVal) => {
            if (newVal) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: newVal,
                    icon: 'warning',
                    confirmButtonColor: '#ffc107',
                });
            }
        });

        const submitAbsen = () => {
            Swal.fire({
                title: 'Konfirmasi Absensi',
                text: "Pastikan Nama dan NIP sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.post(`/events/${props.event.id}/absen`, {
                        onSuccess: () => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Absensi Anda telah tercatat.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        },
                    });
                }
            });
        };

        return {
            getImageUrl,
            form,
            submitAbsen,
            handleNipInput
        };
    }
}
</script>

<style scoped>
/* Menyesuaikan input agar senada dengan template Aspro */
.form-control {
    border: 1px solid #e1e1e1;
    padding: 12px;
    border-radius: 5px;
}
.btnprimary {
    border: none;
    cursor: pointer;
}
</style>
