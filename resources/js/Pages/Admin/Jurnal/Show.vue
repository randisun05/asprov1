           <template>
    <Head>
        <title>Laporan Keuangan</title>
    </Head>
    <div class="px-5 shadow padding">
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0 text-center">
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">Bulan</th>
                                        <th class="border-0">Pemasukan</th>
                                        <th class="border-0">Pengeluaran</th>
                                        <th class="border-0">Saldo</th>
                                        <th class="border-0 rounded-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="(jurnal, index) in jurnals" :key="index">
                                        <!-- Baris utama -->
                                        <tr class="text-center">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ jurnal.month }}</td>
                                            <td @click="toggle(index)" class="text-success" style="cursor: pointer;">
                                                {{ formatCurrency(jurnal.total_pemasukan) }}
                                            </td>
                                            <td @click="toggle(index)" class="text-danger" style="cursor: pointer;">
                                                {{ formatCurrency(jurnal.total_pengeluaran) }}
                                            </td>
                                            <td>{{ formatCurrency(jurnal.saldo_akhir) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-secondary" @click="toggle(index)">
                                                    {{ expandedIndex === index ? 'Tutup' : 'Detail' }}
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Baris detail -->
                                        <tr v-if="expandedIndex === index">
                                            <td colspan="6">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:5%">#</th>
                                                            <th>Keterangan</th>
                                                            <th>Tanggal</th>
                                                            <th>Jenis</th>
                                                            <th>Nominal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(item, i) in jurnal.items" :key="i">
                                                            <td>{{ i + 1 }}</td>
                                                            <td>{{ item.title }}</td>
                                                            <td>{{ new Date(item.date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: '2-digit' }) }}</td>
                                                            <td :class="item.type === 'kredit' ? 'text-danger' : 'text-success'">
                                                                {{ item.type === 'debit' ? 'Pemasukan' : item.type === 'kredit' ? 'Pengeluaran' : item.type }}
                                                            </td>
                                                            <td :class="item.type === 'debit' ? 'text-success' : item.type === 'kredit' ? 'text-danger' : ''">
                                                                {{ formatCurrency(item.nominal) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


            <script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

export default {
    layout: LayoutAdmin,
    components: {
        Head,
    },
    props: {
        errors: Object,
        jurnals: Array,
    },
    setup() {
        const expandedIndex = ref(null);

        const toggle = (index) => {
            expandedIndex.value = expandedIndex.value === index ? null : index;
        };

        const formatCurrency = (value) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
            }).format(value);
        };

        return {
            expandedIndex,
            toggle,
            formatCurrency,
        };
    }
};
</script>
