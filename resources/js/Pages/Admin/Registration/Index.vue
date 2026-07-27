<template>

    <Head>
        <title>Administrator</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/admin/registration/create" class="btn btn-md btn-primary border-0 shadow w-100"
                            type="button"><i class="fa fa-plus-circle"></i>
                        Tambah</Link>
                    </div>
                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/admin/registration/import" class="btn btn-md btn-success border-0 shadow w-100"
                            type="button"><i class="fa fa-plus-circle"></i>
                        Import</Link>
                    </div>

                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/admin/registration/group" class="btn btn-md btn-success border-0 shadow w-100"
                            type="button"><i class="fa fa-plus-circle"></i>
                        Kolektif</Link>
                    </div>

                    <div class="col-md-1 col-12 mb-2">
                        <button
                            @click="handleApproveGroup"
                            class="btn btn-sm btn-success border-0 shadow me-2">
                            <i class="fa fa-check-circle fa-lg me-2" aria-hidden="true"
                            title="approve"></i>Approve Group</button>
                    </div>


                    <div class="col-md-1 col-12 mb-2">
                        <a :href="`/admin/registration/paid/export`" target="_blank"
                            class="btn btn-warning btn-md border-0 shadow w-100 text-white"><i
                                class="fa fa-download"></i>
                            Export Paid</a>
                    </div>

                    <div class="col-md-1 col-12 mb-2">
                        <a :href="`/admin/registration/data/export`" target="_blank"
                            class="btn btn-success btn-md border-0 shadow w-100 text-white"><i
                                class="fa fa-download"></i>
                            Export Data</a>
                    </div>

                    <div class="col-md-6 col-12 mb-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search"
                                    placeholder="masukkan kata kunci dan enter...">
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
                                        <th class="border-0 rounded-start" style="width:5%">
                                            <input type="checkbox" v-model="form.allSelected" @change="selectAll" v-if="!allApprovedOrRejected" />
                                        </th>
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">NIP</th>
                                        <th class="border-0">Nama</th>
                                        <th class="border-0">Instansi</th>
                                        <th class="border-0">Jenjang Jabatan</th>
                                        <th class="border-0">Email</th>
                                        <th class="border-0" style="width:6%">Dokumen</th>
                                        <th class="border-0" style="width:6%">Status</th>
                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                    </tr>
                                </thead>
                                <div class="mt-2"></div>
                                <tbody>
                                    <tr v-for="(register, index) in registers.data" :key="index">
                                        <td class="text-center">
                                            <!-- <input type="checkbox" v-model="form.registration_ids" :id="register.id" :value="register.id" number :checked="form.allSelected" v-if="!allApprovedOrRejected"/> -->
                                            <input
                                                v-if="register.status !== 'approved' && register.status !== 'rejected'"
                                                type="checkbox" v-model="form.registration_ids" :id="register.id" :value="register.id" number :checked="form.allSelected" />
                                        </td>
                                        <td class="fw-bold text-center">{{ ++index + (registers.current_page - 1) *
                                            registers.per_page }}</td>
                                        <td>{{ register.nip }}</td>
                                        <td>{{ register.name }}</td>
                                        <td>{{ register.agency }}</td>
                                        <td class="text">{{ register.position }} {{ register.level }}</td>
                                        <td>{{ register.email }} <span class="badge bg-secondary">{{
                            register.emailstatus }}</span></td>
                                        <td class="text-center"><a :href="getDocumentUrl(register.document_jab)"
                                                target="_blank" class="badge bg-primary">View</a></td>
                                        <td class="text-center">
                                            <span v-if="register.status === 'approved'" class="badge bg-success">{{
                            register.status }}</span>
                                            <span v-else-if="register.status === 'confirm'"
                                                :href="getImageUrl(register.paid)" target="_blank"
                                                class="badge bg-warning">{{ register.status }}</span>
                                            <span v-else-if="register.status === 'rejected'" class="badge bg-danger">{{
                            register.status }}</span>
                                            <a v-if="register.paid" :href="getImageUrl(register.paid)" target="_blank"
                                                class="badge bg-primary">paid</a>
                                            <span v-else-if="register.status === 'submission'"
                                                class="badge bg-secondary">{{ register.status }}</span>
                                                <span v-if="register.admin"
                                                class="badge bg-primary">{{ register.admin }}</span>
                                        </td>

                                        <td class="text-center">
                                            <Link :href="`/admin/registration/${register.id}`"
                                                class="btn btn-sm btn-primary border-0 shadow me-2" type="button"><i
                                                class="fa fa-eye fa-lg" aria-hidden="true" title="lihat detail"></i>
                                            </Link>
                                            <!-- <button
                                                v-if="register.status !== 'approved' && register.status !== 'rejected'"
                                                @click="handleApprove(register.id)"
                                                class="btn btn-sm btn-success border-0 shadow me-2">
                                                <i class="fa fa-check-circle fa-lg" aria-hidden="true"
                                                    title="approve"></i></button> -->
                                            <button v-if="register.status !== 'approved'" @click="handleConfirm(register.id)" class="btn btn-sm btn-warning border-0 shadow me-2"><i class="fa fa-question-circle fa-lg" aria-hidden="true" title="confirm"></i></button>
                                            <button
                                                v-if="register.status !== 'approved' && register.status !== 'rejected'"
                                                @click="handleReject(register.id)"
                                                class="btn btn-sm btn-danger border-0 shadow me-2"><i
                                                    class="fa fa-times-circle fa-lg" aria-hidden="true"
                                                    title="reject"></i></button>
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
    ref, reactive, toRefs, computed
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
        registers: Object,
    },

    //inisialisasi composition API
    setup(props) {

        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        const { registers } = toRefs(props); // Menggunakan toRefs untuk memastikan reaktivitas

        //define form with reactive
        const form = reactive({
        registration_ids: [],
        allSelected: false,
        });

        //define method "selectAll"
        const selectAll = () => {
            if (form.allSelected && registers.value && Array.isArray(registers.value.data)) {
                form.registration_ids = registers.value.data.map(register => register.id);
            } else {
                form.registration_ids = [];
            }
        };

        const allApprovedOrRejected = computed(() => {
            return registers.value.data.every(register =>
                register.status === 'approved' || register.status === 'rejected'
            );
        });



        //define method search
        const handleSearch = () => {
            router.get('/admin/registration', {

                //send params "q" with value from state "search"
                q: search.value,
            });
        }

        // Method to get the URL of the document
        const getDocumentUrl = (documentName) => {
            return `/storage/${documentName}`;
        }

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        const handleApprove = (id) => {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menyetujui usulan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Approve it!'
            })
                .then((result) => {
                    if (result.isConfirmed) {
                        router.post(`/admin/registration/${id}/approve`);
                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Approved!.',
                            icon: 'success',
                            timer: 5000,
                            showConfirmButton: false,
                        });
                    }
                })
        }

        const handleApproveGroup = () => {
            if (form.registration_ids.length === 0) {
                Swal.fire('Error', 'No registrations selected.', 'error');
                return;
            }
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menyetujui grup usulan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Approve it!'
            })
                .then((result) => {
                    if (result.isConfirmed) {
                        router.post(`/admin/registration/group/approve`, {
                            registration_ids: form.registration_ids
                        });
                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Approved!.',
                            icon: 'success',
                            timer: 5000,
                            showConfirmButton: false,
                        });
                    }
                })
        }

        console.log("Approving these IDs:", form.registration_ids);

        const handleConfirm = (id) => {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan mengkonfirmasi ulang usulan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Confirm it!'
            })
                .then((result) => {
                    if (result.isConfirmed) {
                        router.post(`/admin/registration/${id}/confirm`);
                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Conirmed!.',
                            icon: 'success',
                            timer: 5000,
                            showConfirmButton: false,
                        });
                    }
                })
        }

        const handleReject = (id) => {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menolak usulan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Reject it!'
            })
                .then((result) => {
                    if (result.isConfirmed) {

                        router.post(`/admin/registration/${id}/reject`);

                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Rejected!.',
                            icon: 'success',
                            timer: 5000,
                            showConfirmButton: false,
                        });
                    }
                })
        }

        const paidExport = () => {

            router.get(`/admin/registration/paid/export`);

        }

        //return
        return {
            search,
            handleSearch,
            getDocumentUrl,
            handleReject,
            handleApprove,
            handleConfirm,
            getImageUrl,
            paidExport,
            selectAll,
            form,
            handleApproveGroup,
            allApprovedOrRejected

        }
    }
}

</script>

<style></style>
