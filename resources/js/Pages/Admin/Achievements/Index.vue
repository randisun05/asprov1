<template>
    <Head>
        <title>Administrator</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/admin/achievements/create" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
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
                                        <th class="border-0">NIP</th>
                                        <th class="border-0">Nama</th>
                                        <th class="border-0">Instansi</th>
                                        <th class="border-0">Penghargaan</th>
                                        <th class="border-0">Tanggal</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                    </tr>
                                </thead>
                                <div class="mt-2"></div>
                                <tbody>
                                    <tr v-for="(data, index) in datas.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (datas.current_page - 1) * datas.per_page }}</td>
                                        <td>{{ data.member.nip }}</td>
                                        <td>{{ data.member.name }}</td>
                                        <td>{{ data.member.agency }}</td>
                                        <td>{{ data.title }}</td>
                                        <td>{{ data.date}}</td>
                                        <td class="text-center">
                                            <button v-if="data.status === '1'" @click="changeStatus(data.id)" class="badge bg-success border-0">Aktif</button>
                                            <button v-else @click="changeStatus(data.id)" class="badge bg-warning border-0">Nonaktif</button>
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary border-0 shadow me-2" type="button" data-fancybox="" :href="showImage(data.image)"><i class="fa fa-eye" title="view"></i></a>
                                            <a class="btn btn-sm btn-success border-0 shadow me-2" data-fancybox="" :href="showDoc(data.document)"><i class="fa fa-file-pdf-o"></i></a>
                                            <Link :href="`/admin/achievements/${data.id}/edit`" title="edit" class="btn btn-sm btn-warning border-0 shadow me-2"><i class="fa fa-pencil fa-lg" aria-hidden="true"></i> </Link>
                                            <button @click="destroy(data.id)" title="tolak" class="btn btn-sm btn-danger border-0 shadow me-2"><i class="fa fa-times-circle fa-lg" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                         <Pagination :links="datas.links" align="end" />
                    </div>
                </div>
            </div>
        </div>
    </div>




</template>

<script>
    //import layout
    import LayoutAdmin from '../../../Layouts/Admin.vue';

    //import component pagination
    import Pagination from '../../../Components/Pagination.vue';

    //import Heade and Link from Inertia
    import {
        Head,
        Link
    } from '@inertiajs/vue3';

    //import ref from vue
    import {
        ref, reactive
    } from 'vue';

    //import inertia adapter
    import { router } from '@inertiajs/vue3';

    //import sweet alert2
    import Swal from 'sweetalert2';


    export default {
        //layout
        layout: LayoutAdmin,

        //register component
        components: {
            Head,
            Link,
            Pagination
        },

        //props
        props: {
            errors: Object,
            datas: Object,

        },


        //inisialisasi composition API
        setup() {

            //define state search
            const search = ref('' || (new URL(document.location)).searchParams.get('q'));

            //define method search
            const handleSearch = () => {
                router.get('/admin/achievements', {

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

                            router.delete(`/admin/achievements/${id}`);

                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Event Berhasil Dihapus!.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    })
            }

            //define method destroy
            const changeStatus = (id) => {
                Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Anda akan mengganti status achievement!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, change it!'
                    })
                    .then((result) => {
                        if (result.isConfirmed) {

                            router.post(`/admin/achievements/${id}/change`);

                            Swal.fire({
                                title: 'Changed!',
                                text: 'Status Achievement Berhasil Dirubah!.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    })
            }

            const showImage = (imageName) => {
                            return `/storage/${imageName}`;
                        };

                        const showDoc = (documentName) => {
                            return `/storage/${documentName}`;
                        };


            //return
            return {
                search,
                handleSearch,
                destroy,
                changeStatus,
                showImage,
                showDoc


        }
    }
}

</script>

<style>

</style>
