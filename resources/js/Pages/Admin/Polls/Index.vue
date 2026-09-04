<template>
    <Head>
        <title>Polling</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2 col-12 mb-2">
                        <Link href="/admin/polls/create" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                            class="fa fa-plus-circle"></i>
                         Tambah</Link>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search" placeholder="masukkan kata kunci dan enter...">
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
                                        <th class="border-0">Pertanyaan</th>
                                        <th class="border-0">Opsi</th>
                                        <th class="border-0">Total Suara</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(poll, index) in polls.data" :key="poll.id">
                                        <td class="fw-bold text-center">{{ ++index + (polls.current_page - 1) * polls.per_page }}</td>
                                        <td>{{ poll.question }}</td>
                                        <td class="text-center">{{ poll.options_count }}</td>
                                        <td class="text-center">{{ poll.votes_count }}</td>
                                        <td class="text-center">
                                            <button @click="changeStatus(poll.id)" class="badge border-0" :class="poll.is_open ? 'bg-success' : 'bg-warning'">
                                                {{ poll.is_open ? 'Terbuka' : 'Ditutup' }}
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <Link :href="`/admin/polls/${poll.id}/edit`" class="btn btn-sm btn-warning border-0 shadow me-2" type="button" title="edit"><i class="fa fa-pencil"></i></Link>
                                            <button @click.prevent="destroy(poll.id)" class="btn btn-sm btn-danger border-0 me-2"><i class="fa fa-trash" title="hapus"></i></button>
                                        </td>
                                    </tr>
                                    <tr v-if="polls.data.length === 0">
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada polling.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="polls.links" align="end" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import Pagination from '../../../Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

export default {
    layout: LayoutAdmin,
    components: { Head, Link, Pagination },
    props: {
        errors: Object,
        polls: Object,
    },
    setup() {
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        const handleSearch = () => {
            router.get('/admin/polls', { q: search.value });
        };

        const destroy = (id) => {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda tidak akan dapat mengembalikan ini!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    router.delete(`/admin/polls/${id}`);
                }
            });
        };

        const changeStatus = (id) => {
            router.post(`/admin/polls/${id}/status`);
        };

        return { search, handleSearch, destroy, changeStatus };
    },
};
</script>
