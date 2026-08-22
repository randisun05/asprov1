<template>

    <Head>
        <title>Administrator</title>
    </Head>

    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <Link href="/admin/registration" class="btn btn-md btn-primary border-0 shadow mb-3" type="button"><i
                    class="fa fa-arrow-left" aria-hidden="true"></i>
                Kembali</Link>
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h5><i class="fa fa-bookmark"></i> Data Pengusul</h5>
                        <div v-if="errors && Object.keys(errors).length > 0" class="alert alert-danger mt-2">
                            {{ errors }}
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <span>
                                    NIP
                                </span>
                                <div class="form-group bottom35 mt-1">
                                    <input type="text" class="form-control" v-model="form.nip" :disabled="!canEdit">
                                    <div v-if="errors.nip" class="alert alert-danger mt-2">{{ errors.nip }}</div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <span>
                                    Nama
                                </span>
                                <div class="form-group bottom35 mt-1">
                                    <input type="text" class="form-control" v-model="form.name" :disabled="!canEdit">
                                    <div v-if="errors.name" class="alert alert-danger mt-2">{{ errors.name }}</div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <span>
                                    Email
                                </span>
                                <div class="form-group bottom35 mt-1">
                                    <input type="email" class="form-control" v-model="form.email" :disabled="!canEdit">
                                    <div v-if="errors.email" class="alert alert-danger mt-2">{{ errors.email }}</div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <span>
                                    Nomor Kontak
                                </span>
                                <div class="form-group bottom35 mt-1">
                                    <input type="text" class="form-control" v-model="form.contact" :disabled="!canEdit">
                                    <div v-if="errors.contact" class="alert alert-danger mt-2">{{ errors.contact }}</div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <span>
                                    Instansi
                                </span>
                                <div class="form-group bottom35 mt-1">
                                    <input type="text" class="form-control" v-model="form.agency" :disabled="!canEdit">
                                    <div v-if="errors.agency" class="alert alert-danger mt-2">{{ errors.agency }}</div>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <span>
                                    Info
                                </span>
                                <div class="form-group bottom35 mt-1">
                                    <input type="text" class="form-control" v-model="form.info"
                                        :disabled="register.status === 'approved' || register.status === 'rejected'">
                                </div>

                            </div>

                            <div class="col-md-3 col-sm-3">
                                <span>
                                    Jabatan
                                </span>
                                <div class="form-group bottom35 mt-1">
                                    <select class="form-control" v-model="form.position" :disabled="!canEdit">
                                        <option value="Analis SDM Aparatur">Analis SDM Aparatur</option>
                                        <option value="Pranata SDM Aparatur">Pranata SDM Aparatur</option>
                                    </select>
                                    <div v-if="errors.position" class="alert alert-danger mt-2">{{ errors.position }}</div>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-3">
                                <span>
                                    Jenjang
                                </span>
                                <div class="form-group bottom35">
                                    <div class="form-group bottom35 mt-1">
                                        <select class="form-control" v-model="form.level" :disabled="!canEdit">
                                            <template v-if="form.position === 'Analis SDM Aparatur'">
                                                <option value="Ahli Pertama">Ahli Pertama</option>
                                                <option value="Ahli Muda">Ahli Muda</option>
                                                <option value="Ahli Madya">Ahli Madya</option>
                                                <option value="Ahli Utama">Ahli Utama</option>
                                            </template>
                                            <template v-else-if="form.position === 'Pranata SDM Aparatur'">
                                                <option value="Terampil">Terampil</option>
                                                <option value="Mahir">Mahir</option>
                                                <option value="Penyelia">Penyelia</option>
                                            </template>
                                            <template v-else>
                                                <option :value="form.level">{{ form.level }}</option>
                                            </template>
                                        </select>
                                        <div v-if="errors.level" class="alert alert-danger mt-2">{{ errors.level }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12" v-if="canEdit">
                                <button @click.prevent="handleUpdate(register.id)"
                                    class="btn btn-sm btn-primary border-0 shadow mb-3"><i
                                        class="fa fa-save me-1" aria-hidden="true"></i>Simpan Perubahan Data</button>
                            </div>

                            <div class="col-md-6 col-sm-6 mt-4">
                                <a :href="getDocumentUrl(form.document_jab)" target="_blank"
                                    class="badge bg-primary fs-5 border-0 shadow me-4 fs-5" type="button">
                                    View SK Jabatan
                                </a>
                                <span v-if="register.status === 'approved'" class="badge bg-success fs-5">Status {{
                                    register.status }}</span>
                                <span v-else-if="register.status === 'confirm'" class="badge bg-warning fs-5">Status {{
                                    register.status }}</span>
                                <span v-else-if="register.status === 'rejected'" class="badge bg-danger fs-5">Status {{
                                    register.status }}</span>
                                <span v-else-if="register.status === 'submission'"
                                    class="badge bg-secondary fs-5">Status {{ register.status }}</span>
                                <a v-if="register.paid" :href="getImageUrl(register.paid)" target="_blank"
                                    class="badge bg-primary fs-5 ms-4">Paid</a>
                                <span class="badge bg-warning fs-5 ms-4">{{ register.from }}</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <button v-if="register.status !== 'rejected' && register.status !== 'approved'"
                                @click="handleApprove(register.id)"
                                class="btn btn-sm btn-success border-0 shadow me-2"><i
                                    class="fa fa-check-circle fa-lg me-1" aria-hidden="true"></i>Selesai</button>
                            <button v-if="register.status !== 'approved'" @click.prevent="showModalEmail = true"
                                class="btn btn-sm btn-warning border-0 shadow me-2"><i
                                    class="fa fa-question-circle fa-lg me-1" aria-hidden="true"></i>Perbaikan</button>
                            <button v-if="register.status !== 'approved' && register.status !== 'rejected'"
                                @click="handleReject(register.id)" class="btn btn-sm btn-danger border-0 shadow me-2"><i
                                    class="fa fa-times-circle fa-lg me-1" aria-hidden="true"></i>Tolak</button>
                            <button v-if="register.status !== 'approved' && register.status !== 'rejected'"
                                @click="sendEmail(register.id)" class="btn btn-sm btn-primary border-0 shadow me-2"><i
                                    class="fa fa-envelope fa-lg me-1" aria-hidden="true"></i>Permintaan
                                Pembayaran</button>
                            <button v-if="register.status === 'approved'" @click="handleemailApprove(register.id)"
                                class="btn btn-sm btn-success border-0 shadow me-2"><i
                                    class="fa fa-check-circle fa-lg me-1" aria-hidden="true"></i>Kirim Ulang Email
                                Selesai</button>
                        </div>

                        <button v-if="register.status !== 'rejected' && register.status !== 'approved' && $page.props.auth.user.role == 'administrator'"
                            @click="handleApproveLB(register.id)"
                            class="btn btn-sm btn-info border-0 shadow me-2 text-white">
                            <i class="fa fa-university fa-lg me-1" aria-hidden="true"></i>Selesai (LB)
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="showModalEmail" class="modal fade" :class="{ 'show': showModalEmail }" tabindex="-1" aria-hidden="true"
        style="display:block;" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title">Mengirim Link Perbaikan Ke Email:</h5>
                </div>
                <div class="modal-body">
                    <input type="email" class="form-control" v-model="form.sendemail">
                </div>
                <div class="modal-footer">
                    <button @click.prevent=handleConfirm(register.id) type="button"
                        class="btn btn-primary">Kirim</button>
                    <button @click.prevent="showModalEmail = false" type="button"
                        class="btn btn-secondary">Tutup</button>
                </div>
            </div>
        </div>
    </div>



</template>

<script>
//import layout
import LayoutAdmin from '../../../Layouts/Admin.vue';


//import Heade and Link from Inertia
import {
    Head,
    Link
} from '@inertiajs/vue3';

//import reactive from vue
import { reactive, ref, computed } from 'vue';

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
    },

    //props
    props: {
        errors: Object,
        register: Object,


    },




    //inisialisasi composition API
    setup(props) {



        //define form with reactive
        const form = reactive({
            nip: props.register.nip,
            name: props.register.name,
            email: props.register.email,
            contact: props.register.contact,
            agency: props.register.agency,
            position: props.register.position,
            level: props.register.level,
            status: props.register.status,
            paid: props.register.paid,
            document_jab: props.register.document_jab,
            info: props.register.info,
            sendemail: props.register.email,
        });

        const canEdit = computed(() => props.register.status !== 'approved' && props.register.status !== 'rejected');

        const handleUpdate = (id) => {
            router.put(`/admin/registration/${id}`, {
                nip: form.nip,
                name: form.name,
                email: form.email,
                contact: form.contact,
                agency: form.agency,
                position: form.position,
                level: form.level,
            });
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

                        router.post(`/admin/registration/${id}/approve`,
                            {
                                'info': form.info
                            });
                    }
                })
        }

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

                        router.post(`/admin/registration/${id}/confirm`, {
                            'info': form.info,
                            'email': form.sendemail
                        });
                    }
                })
            showModalEmail.value = false;
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
                    }
                })
        }

        const sendEmail = (id) => {
            Swal.fire({
                title: 'Pastikan data sudah sesuai.!',
                text: "Anda akan mengirim email konfirmasi?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Send it!'
            })
                .then((result) => {
                    if (result.isConfirmed) {

                        router.post(`/admin/registration/${id}/email`);
                    }
                })
        }

        const handleemailApprove = (id) => {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan kirim ulang email!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Send it!'
            })
                .then((result) => {
                    if (result.isConfirmed) {

                        router.post(`/admin/registration/${id}/email-approve`);
                    }
                })
        }

        const handleApproveLB = (id) => {
    Swal.fire({
        title: 'Konfirmasi Anggota LB',
        text: "Masukkan Jabatan Khusus untuk Anggota Luar Biasa:",
        input: 'text', // Menambahkan input teks sesuai permintaan Anda sebelumnya
        inputPlaceholder: 'Contoh: Dosen / Praktisi / Lainnya',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#0dcaf0',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Approve sebagai LB',
        inputValidator: (value) => {
            if (!value) {
                return 'Jabatan harus diisi!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(`/admin/registration/${id}/approve-lb`, {
                'info': form.info,
                'position': result.value // Mengirim jabatan inputan admin ke controller
            });
        }
    })
}

        const getDocumentUrl = (documentName) => {
            return `/storage/${documentName}`;
        }

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        const showModalEmail = ref(false);

        //return
        return {
            form,
            canEdit,
            handleUpdate,
            getDocumentUrl,
            handleApprove,
            handleReject,
            handleConfirm,
            getImageUrl,
            sendEmail,
            handleemailApprove,
            showModalEmail,
            handleApproveLB
        }

    }



}


</script>

<style></style>
