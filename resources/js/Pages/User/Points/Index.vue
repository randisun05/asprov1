<template>
    <Head>
        <title>Riwayat Poin</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row mt-1">
            <div class="col-md-12">
                <h3 class="mb-3">Riwayat Poin</h3>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4 col-sm-6">
                <div class="card border-0 shadow h-100">
                    <div class="card-body text-center">
                        <span class="text-muted small d-block mb-2">Saldo Poin Saat Ini</span>
                        <h2 class="mb-0">{{ points }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="transactions.data.length" class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="table-responsive">
                        <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                            <thead class="thead-dark">
                                <tr class="border-0 text-center">
                                    <th class="border-0">Tanggal</th>
                                    <th class="border-0">Tipe</th>
                                    <th class="border-0">Jumlah</th>
                                    <th class="border-0">Saldo Setelah</th>
                                    <th class="border-0">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="trx in transactions.data" :key="trx.id" class="text-center">
                                    <td>{{ trx.created_at }}</td>
                                    <td>
                                        <span class="badge" :class="trx.type === 'reward' ? 'bg-success' : 'bg-danger'">
                                            {{ trx.type === 'reward' ? 'Reward' : 'Redeem' }}
                                        </span>
                                    </td>
                                    <td :class="trx.type === 'reward' ? 'text-success' : 'text-danger'">
                                        {{ trx.type === 'reward' ? '+' : '-' }}{{ trx.amount }}
                                    </td>
                                    <td>{{ trx.balance_after }}</td>
                                    <td>{{ trx.description ?? (trx.event ? trx.event.title : '-') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fa fa-star fa-3x mb-3 d-block" aria-hidden="true"></i>
                        Belum ada riwayat poin.
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <Pagination :links="transactions.links" align="end" />
        </div>
    </div>
</template>

<script>
//import layout
import LayoutUser from '../../../Layouts/User.vue';

//import component pagination
import Pagination from '../../../Components/Pagination.vue';

//import Head from Inertia
import { Head } from '@inertiajs/vue3';

export default {
    //layout
    layout: LayoutUser,

    //register component
    components: {
        Head,
        Pagination,
    },

    //props
    props: {
        points: [Number, String],
        transactions: Object,
    },
};
</script>
