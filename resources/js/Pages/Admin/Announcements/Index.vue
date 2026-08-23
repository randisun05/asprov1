<template>
    <Head>
        <title>Pengumuman</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2 col-12 mb-2">
                        <Link href="/admin/announcements/create" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
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
                                        <th class="border-0">Judul</th>
                                        <th class="border-0">Isi</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(announcement, index) in announcements.data" :key="announcement.id">
                                        <td class="fw-bold text-center">{{ ++index + (announcements.current_page - 1) * announcements.per_page }}</td>
                                        <td>{{ announcement.title }}</td>
                                        <td>{{ announcement.body.length > 100 ? announcement.body.slice(0, 100) + '...' : announcement.body }}</td>
                                        <td class="text-center">
                                            <button @click="changeStatus(announcement.id)" class="badge border-0" :class="announcement.is_active ? 'bg-success' : 'bg-warning'">
                                                {{ announcement.is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <Link :href="`/admin/announcements/${announcement.id}/edit`" class="btn btn-sm btn-warning border-0 shadow me-2" type="button" title="edit"><i class="fa fa-pencil"></i></Link>
                                            <button @click.prevent="destroy(announcement.id)" class="btn btn-sm btn-danger border-0 me-2"><i class="fa fa-trash" title="hapus"></i></button>
                                        </td>
                                    </tr>
                                    <tr v-if="announcements.data.length === 0">
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada pengumuman.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="announcements.links" align="end" />
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
        announcements: Object,
    },
    setup() {
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        const handleSearch = () => {
            router.get('/admin/announcements', { q: search.value });
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
                    router.delete(`/admin/announcements/${id}`);
                }
            });
        };

        const changeStatus = (id) => {
            router.post(`/admin/announcements/${id}/status`);
        };

        return { search, handleSearch, destroy, changeStatus };
    },
};
</script>
