<template>

    <Head>
        <title>Notifikasi WhatsApp</title>
    </Head>
    <div class="container-fluid padding px-5">

        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h5 class="mb-3">Pengaturan WhatsApp</h5>
                        <p class="text-muted">
                            Kirim notifikasi (approval/penolakan/konfirmasi registrasi, permintaan pembayaran,
                            lupa password, sertifikat kegiatan) juga lewat WhatsApp menggunakan Fonnte.
                        </p>
                        <form @submit.prevent="submitSettings" class="row align-items-end">
                            <div class="col-md-3 col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="waEnabled"
                                        v-model="form.enabled">
                                    <label class="form-check-label" for="waEnabled">
                                        {{ form.enabled ? 'Aktif' : 'Nonaktif' }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <span class="text-black">
                                    Token Device Fonnte
                                    <span v-if="settings.has_token" class="badge bg-success ms-1">Sudah diatur</span>
                                    <span v-else class="badge bg-warning ms-1">Belum diatur</span>
                                </span>
                                <div class="form-group mt-1">
                                    <input type="text" class="form-control" v-model="form.api_token"
                                        :placeholder="settings.has_token ? 'Kosongkan jika tidak ingin mengganti token' : 'Masukkan token device Fonnte'">
                                </div>
                                <div v-if="errors.api_token" class="alert alert-danger mt-2 py-1 px-2">{{ errors.api_token }}</div>
                            </div>
                            <div class="col-md-3 col-12 mb-3">
                                <button type="submit" class="btn btn-md btn-primary border-0 shadow w-100">
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="text-muted">Total WhatsApp</div>
                        <div class="fs-4 fw-bold">{{ summary.total }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="text-muted">Berhasil Terkirim</div>
                        <div class="fs-4 fw-bold text-success">{{ summary.sent }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="text-muted">Menunggu (Antrean)</div>
                        <div class="fs-4 fw-bold text-warning">{{ summary.pending }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="text-muted">Gagal</div>
                        <div class="fs-4 fw-bold text-danger">{{ summary.failed }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3 col-12 mb-2">
                        <select class="form-select border-0 shadow" v-model="status" @change="handleSearch">
                            <option value="">Semua Status</option>
                            <option value="sent">Berhasil</option>
                            <option value="pending">Menunggu</option>
                            <option value="failed">Gagal</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search"
                                    placeholder="cari nomor tujuan atau jenis pesan...">
                                <span class="input-group-text border-0 shadow">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0 text-center">
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">Tujuan</th>
                                        <th class="border-0">Jenis Pesan</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Keterangan</th>
                                        <th class="border-0 rounded-end">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(log, index) in whatsappLogs.data" :key="log.id">
                                        <td class="fw-bold text-center">{{ ++index + (whatsappLogs.current_page - 1) * whatsappLogs.per_page }}</td>
                                        <td>{{ log.to_phone }}</td>
                                        <td>{{ typeLabel(log.type) }}</td>
                                        <td class="text-center">
                                            <span class="badge" :class="statusBadgeClass(log.status)">
                                                {{ statusLabel(log.status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="log.status === 'failed'" class="text-danger small">{{ log.error }}</span>
                                            <span v-else>-</span>
                                        </td>
                                        <td>{{ new Date(log.created_at).toLocaleString('id-ID') }}</td>
                                    </tr>
                                    <tr v-if="whatsappLogs.data.length === 0">
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada catatan WhatsApp.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="whatsappLogs.links" align="end" />
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import Pagination from '../../../Components/Pagination.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

export default {
    layout: LayoutAdmin,
    components: {
        Head,
        Pagination,
    },
    props: {
        errors: Object,
        settings: Object,
        whatsappLogs: Object,
        summary: Object,
    },
    setup(props) {
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));
        const status = ref('' || (new URL(document.location)).searchParams.get('status') || '');

        const handleSearch = () => {
            router.get('/admin/whatsapp', {
                q: search.value,
                status: status.value,
            });
        };

        const form = useForm({
            enabled: props.settings.enabled,
            api_token: '',
        });

        const submitSettings = () => {
            form.put('/admin/whatsapp/settings', {
                preserveScroll: true,
                onSuccess: () => { form.api_token = ''; },
            });
        };

        const typeLabel = (type) => {
            const labels = {
                registration_approved: 'Approval Registrasi',
                registration_rejected: 'Penolakan Registrasi',
                registration_payment_request: 'Permintaan Pembayaran',
                registration_confirm: 'Konfirmasi Registrasi',
                forgot_password: 'Lupa Password',
                certificate: 'Sertifikat Kegiatan',
            };
            return labels[type] ?? type;
        };

        const statusLabel = (status) => {
            const labels = {
                sent: 'Berhasil',
                pending: 'Menunggu',
                failed: 'Gagal',
            };
            return labels[status] ?? status;
        };

        const statusBadgeClass = (status) => {
            if (status === 'sent') return 'bg-success';
            if (status === 'pending') return 'bg-warning text-dark';
            if (status === 'failed') return 'bg-danger';
            return 'bg-secondary';
        };

        return {
            search,
            status,
            handleSearch,
            form,
            submitSettings,
            typeLabel,
            statusLabel,
            statusBadgeClass,
        };
    },
};
</script>
