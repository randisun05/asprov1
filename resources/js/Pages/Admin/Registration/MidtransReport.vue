<template>

    <Head>
        <title>Laporan Transaksi Midtrans</title>
    </Head>
    <div class="container-fluid padding px-5">

        <div class="row">
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="text-muted">Saldo Masuk (Settlement)</div>
                        <div class="fs-4 fw-bold text-success">{{ formatCurrency(summary.balance) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="text-muted">Transaksi Sukses</div>
                        <div class="fs-4 fw-bold">{{ summary.settled_count }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="text-muted">Menunggu Pembayaran</div>
                        <div class="fs-4 fw-bold text-warning">{{ summary.pending_count }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card border-0 shadow h-100">
                    <div class="card-body">
                        <div class="text-muted">Gagal/Kedaluwarsa</div>
                        <div class="fs-4 fw-bold text-danger">{{ summary.failed_count }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2 col-12 mb-2">
                        <a :href="`/admin/midtrans-report/export`" class="btn btn-md btn-primary border-0 shadow w-100"
                            type="button">
                            <i class="fa fa-file-excel" aria-hidden="true"></i>
                            Export</a>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search"
                                    placeholder="cari nama, nip, instansi, order id...">
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
                                        <th class="border-0">Anggota</th>
                                        <th class="border-0">Order ID</th>
                                        <th class="border-0">Nominal</th>
                                        <th class="border-0">Metode</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end">Waktu Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(trx, index) in transactions.data" :key="trx.id">
                                        <td class="fw-bold text-center">{{ ++index + (transactions.current_page - 1) *
                                            transactions.per_page }}</td>
                                        <td>
                                            <div>{{ trx.registration?.name ?? '-' }}</div>
                                            <small class="text-muted">{{ trx.registration?.nip }} - {{ trx.registration?.agency }}</small>
                                        </td>
                                        <td>{{ trx.order_id }}</td>
                                        <td class="text-end">{{ formatCurrency(trx.gross_amount) }}</td>
                                        <td>{{ trx.payment_type ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge" :class="statusBadgeClass(trx.transaction_status)">
                                                {{ statusLabel(trx.transaction_status) }}
                                            </span>
                                        </td>
                                        <td>{{ trx.transaction_time ? new Date(trx.transaction_time).toLocaleString('id-ID') : '-' }}</td>
                                    </tr>
                                    <tr v-if="transactions.data.length === 0">
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi Midtrans.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="transactions.links" align="end" />
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import Pagination from '../../../Components/Pagination.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    layout: LayoutAdmin,
    components: {
        Head,
        Pagination,
    },
    props: {
        errors: Object,
        transactions: Object,
        summary: Object,
    },
    setup() {
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        const handleSearch = () => {
            router.get('/admin/midtrans-report', {
                q: search.value,
            });
        };

        const formatCurrency = (value) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
            }).format(value || 0);
        };

        const statusLabel = (status) => {
            const labels = {
                settlement: 'Berhasil',
                capture: 'Berhasil',
                pending: 'Menunggu',
                deny: 'Ditolak',
                cancel: 'Dibatalkan',
                expire: 'Kedaluwarsa',
            };
            return labels[status] ?? status;
        };

        const statusBadgeClass = (status) => {
            if (['settlement', 'capture'].includes(status)) return 'bg-success';
            if (status === 'pending') return 'bg-warning text-dark';
            if (['deny', 'cancel', 'expire'].includes(status)) return 'bg-danger';
            return 'bg-secondary';
        };

        return {
            search,
            handleSearch,
            formatCurrency,
            statusLabel,
            statusBadgeClass,
        };
    },
};
</script>
