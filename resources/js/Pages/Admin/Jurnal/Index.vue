            <template>
                <Head>
                    <title>Pencatatan</title>
                </Head>
                <div class="px-5 shadow padding">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <button class="btn btn-primary" @click="openAddModal"><i
                                class="fa fa-plus-circle"></i>Tambah</button>
                                <a v-if="$page.props.auth.user.role === 'bendahara' || $page.props.auth.user.role === 'administrator'" href="/admin/jurnals/export" class="btn btn-success ms-2"><i class="fa fa-excel"></i> Report</a>
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
                                                    <th class="border-0">Keterangan</th>
                                                    <th class="border-0">Tanggal</th>
                                                    <th class="border-0">Bukti</th>
                                                    <th class="border-0">Pemasukan</th>
                                                    <th class="border-0">Pengeluaran</th>
                                                    <th class="border-0">Saldo</th>
                                                    <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(jurnal, index) in jurnals.data" :key="index">
                                                    <td class="fw-bold text-center">{{ ++index + (jurnals.current_page - 1) * jurnals.per_page }}</td>
                                                    <td>{{ jurnal.title }}</td>
                                                    <td>{{ new Date(jurnal.date).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: '2-digit' }) }}</td>
                                                    <td>
                                                        <a :href="`/storage/${jurnal.bukti}`" target="_blank">Lihat Bukti</a>
                                                    </td>
                                                    <td>{{ jurnal.type === 'debit' ? formatCurrency(jurnal.nominal) : 0 }}</td>
                                                    <td :class="{ 'text-danger': jurnal.type === 'kredit' }">{{ jurnal.type === 'kredit' ? formatCurrency(jurnal.nominal) : 0 }}</td>
                                                    <td>{{ formatCurrency(jurnal.saldo) }}</td>
                                                           <td class="text-center" v-if="$page.props.auth.user.role === 'bendahara' || $page.props.auth.user.role === 'administrator'">
                                                        <button @click.prevent="openEditModal(jurnal)" class="btn btn-sm btn-primary border-0 me-2">
                                                            <i class="fa fa-pencil" title="edit"></i>
                                                        </button>
                                                        <button @click.prevent="destroy(jurnal.id)" class="btn btn-sm btn-danger border-0 me-2">
                                                            <i class="fa fa-trash" title="hapus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <Pagination :links="jurnals.links" align="end" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add/Edit Modal -->
                <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="formModalLabel">{{ isEdit ? 'Edit Pencatatan' : 'Tambah Pencatatan' }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="isEdit ? update() : submit()">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" v-model="form.title">
                                    </div>
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" id="tanggal" v-model="form.date">
                                    </div>
                                    <div class="mb-3">
                                        <label for="bukti" class="form-label">Bukti</label>
                                        <input type="file" class="form-control" id="bukti" @change="handleFileUpload">
                                    </div>
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Jenis</label>
                                        <div>
                                            <input type="radio" id="pemasukan" value="debit" v-model="form.type">
                                            <label for="pemasukan">Pemasukan</label>
                                        </div>
                                        <div>
                                            <input type="radio" id="pengeluaran" value="kredit" v-model="form.type">
                                            <label for="pengeluaran">Pengeluaran</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="nominal" class="form-label">Nominal</label>
                                        <input type="text" class="form-control" id="nominal" v-model="form.nominal">
                                    </div>
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <input type="text" class="form-control" id="keterangan" v-model="form.keterangan">
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ isEdit ? 'Update' : 'Simpan' }}</button>
                                </form>
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
            import Swal from 'sweetalert2';

            export default {
                layout: LayoutAdmin,
                components: {
                    Head,
                    Pagination
                },
                props: {
                    errors: Object,
                    jurnals: Object,
                },
                setup() {
                    const form = ref({
                        title: '',
                        date: '',
                        bukti: null,
                        nominal: '',
                        type: '',
                        keterangan: ''
                    });

                    const isEdit = ref(false);

                    const handleFileUpload = (event) => {
                        form.value.bukti = event.target.files[0];
                    };

                    const updateImage = (event) => {
                        form.media = event.target.files[0];
                    };


                    const openAddModal = () => {
                        isEdit.value = false;
                        form.value = {
                            title: '',
                            date: '',
                            bukti: null,
                            nominal: '',
                            type: '',
                            keterangan: ''
                        };
                        new bootstrap.Modal(document.getElementById('formModal')).show();
                    };

                    const openEditModal = (jurnal) => {
                        isEdit.value = true;
                        form.value = {
                            id: jurnal.id,
                            title: jurnal.title,
                            date: jurnal.date,
                            bukti: null,
                            nominal: jurnal.nominal,
                            type: jurnal.type,
                            keterangan: jurnal.keterangan
                        };
                        new bootstrap.Modal(document.getElementById('formModal')).show();
                    };

                    const submit = () => {
                        const formData = new FormData();
                        formData.append('title', form.value.title);
                        formData.append('date', form.value.date || form.value.date);
                        formData.append('bukti', form.value.bukti);
                        formData.append('type', form.value.type);
                        formData.append('nominal', form.value.nominal);
                        formData.append('keterangan', form.value.keterangan);
                        router.post('/admin/jurnals/', formData, {
                            onError: (errors) => {
                                console.error('Validation errors:', errors);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Gagal menyimpan data. Periksa kembali input Anda.',
                                    icon: 'error',
                                    timer: 3000,
                                    showConfirmButton: false,
                                });
                            },
                            onSuccess: () => {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Data berhasil disimpan.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                const modal = bootstrap.Modal.getInstance(document.getElementById('formModal'));
                                if (modal) modal.hide();
                                form.value = {
                                    title: '',
                                    date: '',
                                    bukti: null,
                                    nominal: '',
                                    type: '',
                                    keterangan: ''
                                };
                            }
                        });
                    };

                    const update = () => {
    if (!form.value.id) {
        console.error('ID is missing in form data.');
        Swal.fire({
            title: 'Error!',
            text: 'ID tidak ditemukan. Tidak dapat memperbarui data.',
            icon: 'error',
            timer: 3000,
            showConfirmButton: false,
        });
        return;
    }

    const formData = new FormData();
    formData.append('title', form.value.title);
    formData.append('date', form.value.date);
    formData.append('type', form.value.type);
    formData.append('nominal', form.value.nominal);
    formData.append('keterangan', form.value.keterangan);

    // Hanya append bukti jika ada file baru
    if (form.value.bukti) {
        formData.append('bukti', form.value.bukti);
    }

    formData.append('_method', 'PUT'); // karena FormData tidak mendukung PUT secara langsung

    router.post(`/admin/jurnals/${form.value.id}`, formData, {
        forceFormData: true, // Pastikan Inertia mengirim sebagai multipart/form-data
        onError: (errors) => {
            console.error('Validation errors:', errors);
            Swal.fire({
                title: 'Error!',
                text: 'Gagal memperbarui data. Periksa kembali input Anda.',
                icon: 'error',
                timer: 3000,
                showConfirmButton: false,
            });
        },
        onSuccess: () => {
            Swal.fire({
                title: 'Success!',
                text: 'Data berhasil diperbarui.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
            const modal = bootstrap.Modal.getInstance(document.getElementById('formModal'));
            if (modal) modal.hide();
            form.value = {
                title: '',
                date: '',
                bukti: null,
                nominal: '',
                type: '',
                keterangan: ''
            };
        }
    });
};


                    const destroy = (id) => {
                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: "Anda tidak akan dapat mengembalikan ini!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                router.delete(`/admin/jurnals/${id}`);
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Pencatatan Berhasil Dihapus!.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            }
                        });
                    };

                     const formatCurrency = (value) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
            }).format(value);
        };


                    return {
                        form,
                        isEdit,
                        handleFileUpload,
                        openAddModal,
                        openEditModal,
                        submit,
                        update,
                        destroy,
                        formatCurrency,
                    };
                }
            };
            </script>
