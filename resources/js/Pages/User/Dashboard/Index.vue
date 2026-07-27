<template>
    <!-- Ringkasan Anggota -->
    <section id="member-summary" class="padding_m mt-4">
        <div class="mx-5">
            <h3 class="mb-3">Selamat Datang, {{ profile.main.name }}</h3>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="fa fa-calendar-check-o fa-lg" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ summary.eventsJoined }}</h3>
                                <span class="text-muted small">Kegiatan Diikuti</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="fa fa-certificate fa-lg" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ summary.certificates }}</h3>
                                <span class="text-muted small">Sertifikat</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="fa fa-trophy fa-lg" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="mb-0">{{ summary.achievements }}</h3>
                                <span class="text-muted small">Penghargaan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon me-3" :class="verified ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary'">
                                <i class="fa" :class="verified ? 'fa-check-circle' : 'fa-clock-o'" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="d-block fw-bold">{{ verified ? 'Terverifikasi' : 'Menunggu' }}</span>
                                <span class="text-muted small">Status Kartu Anggota</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="member-card" class="padding_m mt-2">
        <div class="ms-5">
            <div class="row">
                <div class="col-12 d-flex flex-wrap">
                    <div class="card shadow top60 flex-fill me-4 mb-4" style="min-width:340px; max-width: 48%;">
                        <div class="text-center">
                            <h4>Informasi Anggota</h4>
                            <h4>Aspro SDM Aparatur</h4>
                            <hr class="mt-0">
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="row py-1 ms-2">
                                    <div class="col-sm-4">
                                        <h5>No.</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <h5>: {{ profile.main.nomember }}</h5>
                                    </div>
                                </div>
                                <div class="row py-1 ms-2">
                                    <div class="col-sm-4">
                                        <h5>NIP</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <h5>: {{ profile.main.nip }}</h5>
                                    </div>
                                </div>
                                <div class="row py-1 ms-2">
                                    <div class="col-sm-4">
                                        <h5>Nama</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <h5>: {{ profile.main.name }}</h5>
                                    </div>
                                </div>
                                <div class="row py-1 ms-2">
                                    <div class="col-sm-4">
                                        <h5>Instansi</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <h5>: {{ profile.agency }}</h5>
                                    </div>
                                </div>
                                <div class="row py-1 ms-2">
                                    <div class="col-sm-4">
                                        <h5>Jabatan</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <h5>: {{ profile.position || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row py-1 ms-2">
                                    <div class="col-sm-4">
                                        <h5>Jenjang</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <h5>: {{ profile.level || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row py-1 ms-2">
                                    <div class="col-sm-4">
                                        <h5>Status</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="badge bg-primary">{{ profile.main.statusmember || 'Anggota Biasa' }}</span>
                                    </div>
                                </div>
                                <div class="row py-1 ms-2">
                                    <div class="col-sm-4">
                                        <h5>Tanggal</h5>
                                    </div>
                                    <div class="col-sm-8">
                                        <h5>: {{ formattedDate }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow top60 flex-fill mb-4" style="min-width:340px; max-width: 48%;">
                        <div class="text-center">
                            <h4>Informasi Keuangan</h4>
                            <h4>Aspro SDM Aparatur</h4>
                            <hr class="mt-0">
                        </div>
                        <div class="row mb-3 px-4">
                            <div class="col-sm-12">
                                <div class="row py-1">
                                    <div class="col-sm-7">
                                        <h5>Saldo Kas</h5>
                                    </div>
                                    <div class="col-sm-5 text-end">
                                        <h5>Rp{{ formatRupiah(finance.saldoAkhir) }}</h5>
                                    </div>
                                </div>
                                <div class="row py-1">
                                    <div class="col-sm-7">
                                        <h5>Pemasukan Bulan Ini</h5>
                                    </div>
                                    <div class="col-sm-5 text-end">
                                        <h5 class="text-success">Rp{{ formatRupiah(finance.pemasukanBulanIni) }}</h5>
                                    </div>
                                </div>
                                <div class="row py-1">
                                    <div class="col-sm-7">
                                        <h5>Pengeluaran Bulan Ini</h5>
                                    </div>
                                    <div class="col-sm-5 text-end">
                                        <h5 class="text-danger">Rp{{ formatRupiah(finance.pengeluaranBulanIni) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center pb-3">
                            <a href="/user/jurnals/show" class="btn btnprimary btn-sm">Lihat Laporan Lengkap</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="dashboard" class="card mx-5 mt-3 shadow">
        <div class="ms-4 mt-4">
            <h3> Kegiatan </h3>
            <hr>
        </div>
        <!-- :autoplay="5000"  -->
        <carousel :items-to-show="1" :center-mode="false" wrap-around="true">
            <slide v-for="(event, index) in events.data" :key="index">
                <div class="news_item shadow row">
                    <div class="col-lg-6" style="display: flex; justify-content: center; align-items: center;">
                        <img class="image" v-if="event.image" :src="getImageUrl(event.image)" alt="Gambar"
                            style="width: 100%;" />
                    </div>
                    <div class="col-lg-6">
                        <div class="news_desc">
                            <h4 class="text-capitalize font-light darkcolor" style="font-weight: bold;"><a href="#.">{{
                                            event.title }}</a></h4>
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
            </slide>

            <template #addons>
                <navigation />
                <pagination />
            </template>
        </carousel>
        <div class="text-center py-3">
            <a href="/user/events" class="btn btnprimary">Lihat Semua Kegiatan</a>
        </div>
    </section>

    <section id="dashboard" class="card mx-5 mt-3 shadow">
        <div class="ms-4 mt-4">
            <h3> Merchandises </h3>
            <hr>
        </div>

        <carousel :items-to-show="1" :center-mode="false" wrap-around="true">
            <slide v-for="(merchan, index) in merchans.data" :key="index" class="px-5">
                <div class="news_item shadow row">
                    <div class="col-lg-6" style="display: flex; justify-content: center; align-items: center;">
                        <img class="image" v-if="merchan.image" :src="getImageUrl(merchan.image)" alt="Gambar"
                            style="max-width: 100%; max-height: 100%; width: auto; height: auto;" />
                    </div>

                    <div class="col-lg-6">
                        <div class="col product-details" style="text-align: left;">
                            <h5 class="product-brand">{{ merchan.title }}</h5>
                            <h6 class="product-name">{{ merchan.subtitle }}</h6>
                            <div style="font-size: 10px;" class="product-color">Warna : {{ merchan.color }}</div>
                            <div class="product-rating">
                                <span class="rating-star">★</span>
                                <span class="rating-star">★</span>
                                <span class="rating-star">★</span>
                                <span class="rating-star">★</span>
                                <span class="rating-star">★</span>
                            </div>
                            <h6 class="product-price mb-2">Rp.{{ merchan.price }}</h6>
                            <h6 class="mb-1">Bagaimana Cara Kamu Beli:</h6>
                            <div v-html="merchan.how" style="font-size: 10px;"></div>
                            <div class="mt-3">
                                <Link :href="`/user/merchans/${merchan.id}`" title="join" class="button btnprimary"
                                    type="button">View</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </slide>
            <template #addons>
                <navigation />
                <pagination />
            </template>
        </carousel>
        <div class="text-center py-3">
            <a href="/user/merchans" class="btn btnprimary">Lihat Semua Merchandise</a>
        </div>
    </section>

    <section id="dashboard" class="card mx-5 mt-3 shadow">
        <div class="ms-4 mt-4">
            <h3> Berita </h3>
            <hr>
        </div>

        <carousel :items-to-show="1" :center-mode="false" wrap-around="true">
            <slide v-for="(post, index) in posts.data" :key="index">
                <div class="news_item shadow row">
                    <div class="col-lg-6" style="display: flex; justify-content: center; align-items: center;">
                        <img class="image" v-if="post.image" :src="getImageUrl(post.image)" alt="Gambar"
                            style="width: 100%;" />
                    </div>
                    <div class="col-lg-6">
                        <div class="news_desc">
                            <h3 class="text-capitalize font-light darkcolor"><a href="#">{{ post.title }}</a>
                            </h3>
                            <ul class="meta-tags top20 bottom20">
                                <li><a href="#."><i class="fa fa-user"></i>{{ post.member.name }}</a></li>
                                <li><a href="#."><i class="fa fa-calendar"></i>{{ post.publish_at }}</a></li>
                            </ul>
                            <p class="bottom35">{{ post.excerpt }}</p>
                            <Link :href="`/user/posts/list/${post.slug}`" title="join" class="button btnprimary"
                                type="button">View
                            </Link>
                        </div>
                    </div>
                </div>
            </slide>
            <template #addons>
                <navigation />
                <pagination />
            </template>
        </carousel>
        <div class="text-center py-3">
            <a href="/user/posts/list" class="btn btnprimary">Lihat Semua Post</a>
        </div>
    </section>

</template>

<script>
//import layout Admin
import LayoutUser from '../../../Layouts/User.vue';

//import Heade from Inertia
import {
    Head, Link
} from '@inertiajs/inertia-vue3';

import 'vue3-carousel/dist/carousel.css'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'

//import ref from vue
import {
    ref, reactive, computed
} from 'vue';

//import inertia adapter
import { Inertia } from '@inertiajs/inertia';

//import sweet alert2
import Swal from 'sweetalert2';

export default {

    //layout
    layout: LayoutUser,

    //register components
    components: {
        Head, Carousel, Slide, Pagination, Navigation, Link
    },

    //props
    props: {
        profile: Object,
        events: Object,
        merchans: Object,
        posts: Object,
        formattedDate: Object,
        user: Object,
        summary: Object,
        finance: Object,
    },

    //inisialisasi composition API
    setup(props) {

        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        //define method search
        const handleSearch = () => {
            Inertia.get('/events', {

                //send params "q" with value from state "search"
                q: search.value,
            });
        }

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        const verified = computed(() => !!props.user?.qr_link);

        const formatRupiah = (value) => {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        }

        //return
        return {
            getImageUrl,
            verified,
            formatRupiah,
        }
    }


}

</script>

<style>
.img-fluid {
    max-width: 100%;
    height: auto;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
