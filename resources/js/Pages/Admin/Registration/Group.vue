<template>
    <Head>
        <title>Administrator</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">

                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/admin/registration" class="btn btn-md btn-primary border-0 shadow mb-3" type="button"><i class="fa fa-arrow-left" aria-hidden="true"> </i>
                    Kembali</Link>
                    </div>
                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/admin/registration/import" class="btn btn-md btn-success border-0 shadow w-100" type="button"><i
                            class="fa fa-plus-circle"></i>
                        Import</Link>
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
                                        <th class="border-0">Instansi</th>
                                        <th class="border-0">Nama PIC</th>
                                        <th class="border-0">Kontak</th>
                                        <th class="border-0">Email</th>
                                        <th class="border-0" style="width:6%">Dokumen</th>
                                        <th class="border-0" style="width:6%">Status</th>
                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                    </tr>
                                </thead>
                                <div class="mt-2"></div>
                                <tbody>
                                    <tr v-for="(register, index) in registers.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (registers.current_page - 1) * registers.per_page }}</td>
                                        <td>{{ register.agency }}</td>
                                        <td>{{ register.name }}</td>
                                        <td>{{ register.contact }}</td>
                                        <td>{{ register.email }}</td>
                                        <td class="text-center"><a :href="getDocumentUrl(register.file)" target="_blank" class="badge bg-primary">Unduh</a></td>
                                        <td class="text-center">
                                            <span v-if="register.status === 'done'" class="badge bg-success">{{ register.status }}</span>
                                            <span v-else-if="register.status === 'confirm'" class="badge bg-warning">{{ register.status }}</span>
                                            <span v-else-if="register.status === 'rejected'" class="badge bg-danger">{{ register.status }}</span>
                                            <span v-else-if="register.status === 'submission'" class="badge bg-secondary">{{ register.status }}</span>
                                        </td>

                                        <td class="text-center">
                                            <button v-if="register.status !== 'done' && register.status !== 'rejected'"  @click="handleDone(register.id)" class="btn btn-sm btn-success border-0 shadow me-2"><i class="fa fa-check-circle fa-lg" aria-hidden="true"></i></button>
                                            <button @click="handleConfirm(register.id)" class="btn btn-sm btn-warning border-0 shadow me-2"><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i></button>
                                            <button v-if="register.status !== 'done' && register.status !== 'rejected'" @click="handleReject(register.id)" class="btn btn-sm btn-danger border-0 shadow me-2"><i class="fa fa-times-circle fa-lg" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="registers.links" align="end" />
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
        ref
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
            registers: Object,
        },

        //inisialisasi composition API
        setup() {

            //define state search
            const search = ref('' || (new URL(document.location)).searchParams.get('q'));

            //define method search
            const handleSearch = () => {
                router.get('/admin/registration/group', {

                    //send params "q" with value from state "search"
                    q: search.value,
                });
            }

             // Method to get the URL of the document
        const getDocumentUrl = (documentName) => {
            return `/storage/${documentName}`;
        }

        const handleDone = (id) => {
            Swal.fire({
                    title: 'Usulan Kolektif Selesai?',
                    text: "Anda telah menyelesaikan usulan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        router.post(`/admin/registration/group/${id}/done`);
                    }
                })
            }

            const handleConfirm = (id) => {
            Swal.fire({
                    title: 'Usulan Kolektif Sedang Dikonfirmasi?',
                    text: "Anda telah mengkonfirmasi usulan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Confirm it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        router.post(`/admin/registration/group/${id}/confirm`);
                    }
                })
            }

            const handleReject = (id) => {
            Swal.fire({
                    title: 'Usulan Kolektif Ditolak?',
                    text: "Anda telah menolak usulan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Reject it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {

                        router.post(`/admin/registration/group/${id}/reject`);
                    }
                })
            }
            //return
            return {
                search,
                handleSearch,
                getDocumentUrl,
                handleReject,
                handleDone,
                handleConfirm,


        }
    }
}

</script>

<style>

</style>
