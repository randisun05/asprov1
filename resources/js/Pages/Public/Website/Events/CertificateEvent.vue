<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <section class="page-header parallaxie padding_top center-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h2 class="whitecolor font-light bottom30">Cek Sertifikat</h2>
                    <ul class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                        <li class="breadcrumb-item active">Sertifikat</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="certificate-search">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 mb-5 shadow-sm">
                        <div class="card-body">
                            <h3 class="darkcolor bottom20 text-center">Cari Sertifikat Anda</h3>
                            <form @submit.prevent="handleSearch">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        v-model="search"
                                        class="form-control"
                                        placeholder="Masukkan NIP / NIK Anda..."
                                        @input="search = search.replace(/[^0-9]/g, '')"
                                    >
                                    <button class="button btnprimary" type="submit">
                                        <i class="fa fa-search"></i> Cari
                                    </button>
                                </div>
                                <div v-if="errors.message" class="text-danger mt-2 small">
                                    * {{ errors.message }}
                                </div>
                            </form>
                        </div>
                    </div>

                    <div v-if="currentCertificate" class="card border-0 mb-5 bg-light shadow-sm">
                        <div class="card-body text-center py-5">
                            <h4 class="darkcolor bottom10">Sertifikat Ditemukan!</h4>
                            <p class="bottom20 text-muted">Event: {{ event.title }}</p>
                            <div class="d-flex justify-content-center">
                                <a :href="`/certificates/${currentCertificate.id}/view`" target="_blank" class="btn btn-success shadow px-4">
                                    <i class="fa fa-download"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="querySearch && !currentCertificate" class="alert alert-warning border-0 shadow-sm mb-5 text-center py-4">
                        <i class="fa fa-info-circle fa-lg mb-2 d-block"></i>
                        Sertifikat tidak ditemukan / Anda tidak terdaftar pada kegiatan <br><strong>{{ event.title }}</strong>.
                    </div>

                    <div v-if="allCertificates.length > 0" class="mt-4">
                        <h4 class="darkcolor bottom20"><i class="fa fa-history"></i> Riwayat Sertifikat Anda</h4>
                        <div class="table-responsive shadow rounded">
                            <table class="table table-striped mb-0 bg-white">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Nama Event</th>
                                        <th>Nomor Sertifikat</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="cert in allCertificates" :key="cert.id">
                                        <td>{{ cert.event?.title || 'Event Aspro' }}</td>
                                        <td class="small">{{ cert.no_certificate }}</td>
                                        <td class="text-center">
                                            <a :href="`/certificates/${cert.id}/view`" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-external-link"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr v-if="querySearch && allCertificates.length === 0">
                                        <td colspan="3" class="text-center text-muted py-5">
                                            <i class="fa fa-folder-open-o d-block mb-2 fa-2x"></i>
                                            Tidak ada riwayat sertifikat ditemukan untuk NIP ini.
                                        </td>
                                    </tr>

                                    <tr v-if="!querySearch">
                                        <td colspan="3" class="text-center text-muted py-4">
                                            Silahkan masukkan NIP untuk melihat riwayat sertifikat.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import LayoutWebsite from '../../../../Layouts/Website.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    layout: LayoutWebsite,
    props: {
        title: String,
        event: Object,
        errors: Object,
        currentCertificate: Object,
        allCertificates: Array,
        querySearch: String
    },
    setup(props) {
        const search = ref(props.querySearch || '');

        const handleSearch = () => {
            if (!search.value) return;

            router.get(`/events/${props.event.slug}/certificate`, {
                q: search.value
            }, {
                preserveState: true,
                replace: true
            });
        };

        return {
            search,
            handleSearch
        };
    }
}
</script>

<style scoped>
.btnprimary { border: none; padding: 10px 25px; }
.form-control { border-radius: 5px 0 0 5px; height: 50px; border: 1px solid #ddd; }
.input-group .button { border-radius: 0 5px 5px 0; }
.table th { border-top: none; }
</style>
