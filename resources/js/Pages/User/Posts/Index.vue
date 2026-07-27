<template>
    <Head>
        <title>Berbagi Cerita</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/user/posts/create" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
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
                                        <th class="border-0">Kategori</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                    </tr>
                                </thead>
                                <div class="mt-2"></div>
                                <tbody>
                                    <tr v-for="(post, index) in posts.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (posts.current_page - 1) * posts.per_page }}</td>
                                        <td>{{ post.title }}</td>
                                        <td>{{ post.category.title }}</td>
                                        <td><span v-if="post.status === 'approved'" class="badge bg-success" title="disetujui untuk di publish">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'private'" class="badge bg-warning" title="hanya untuk konsumsi pribadi">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'perlu ada perbaikan'" class="badge bg-warning" title="silakan dicek kembali">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'rejected'" class="badge bg-danger" title="ditolak untuk publish">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'submission'" class="badge bg-secondary" title="sedang diajukan, fitur edit nonaktif">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'return'" class="badge bg-danger" title="ditolak untuk publish">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'limited'" class="badge bg-danger" title="ditolak untuk publish">{{ post.status }}</span></td>
                                        <td class="text-center">
                                            <Link v-if="post.status !== 'submission' && post.status !== 'rejected' && post.status !== 'approved'&& post.status !== 'limited'" :href="`/user/posts/${encodeURIComponent(post.slug)}/edit`" class="btn btn-sm btn-warning border-0 shadow me-2" type="button" title="edit"><i class="fa fa-pencil"></i></Link>
                                            <Link :href="`/user/posts/${encodeURIComponent(post.slug)}`" class="btn btn-sm btn-info border-0 shadow me-2" type="button"><i class="fa fa-eye" title="lihat detail"></i></Link>
                                            <Link  v-if="post.status !== 'submission' && post.status !== 'rejected' && post.status !== 'approved' && post.status !== 'limited'" @click.prevent="handleSubmission(post.id)" class="btn btn-sm btn-success border-0 shadow me-2" type="button" title="pengajuan publish"><i class="fa fa-envelope"></i></Link>
                                            <button v-if="post.status !== 'submission' && post.status !== 'rejected' && post.status !== 'approved' && post.status !== 'limited'" @click.prevent="destroy(post.id)" class="btn btn-sm btn-danger border-0 me-2"><i class="fa fa-trash" title="hapus"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="posts.links" align="end" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    //import layout
    import LayoutUser from '../../../Layouts/User.vue';

    //import component pagination
    import Pagination from '../../../Components/Pagination.vue';

    //import Heade and Link from Inertia
    import {
        Head,
        Link
    } from '@inertiajs/vue3';

    //import ref from vue
    import {
        ref
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
            Pagination
        },

        //props
        props: {
            posts :Object,

        },

        //inisialisasi composition API
        setup() {

            //define state search
            const search = ref('' || (new URL(document.location)).searchParams.get('q'));

            //define method search
            const handleSearch = () => {
                router.get('/user/posts', {

                    //send params "q" with value from state "search"
                    q: search.value,
                });
            }

            //define method destroy
            const destroy = (id) => {
                Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Anda tidak akan dapat mengembalikan ini!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    })
                    .then((result) => {
                        if (result.isConfirmed) {

                            router.delete(`/user/posts/${id}`);

                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Peserta Berhasil Dihapus!.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    })
            }

            const handleSubmission = (id) => {
            Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Post akan diajukan untuk publish!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Submit it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        router.get(`/user/posts/${id}/submission`);
                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Returned!.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
            }




            //return
            return {
                search,
                handleSearch,
                destroy,
                handleSubmission

        }
    }
}

</script>

<style>

</style>
