<template>
    <Head>
        <title>Import Sertifikat</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <form @submit.prevent="submitImport">
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <label class="fw-bold">Kategori</label>
                                    <select class="form-control" :class="{ 'is-invalid': errors.category }" v-model="form.category">
                                        <option disabled value="">Pilih Kategori</option>
                                        <option value="Kombel">Komunitas Belajar - Peserta</option>
                                        <option value="Kombel-Panitia">Komunitas Belajar - Panitia</option>
                                        <option value="Kombel-Narasumber">Komunitas Belajar - Narasumber</option>
                                        <option value="Kombel-Moderator">Komunitas Belajar - Moderator</option>
                                        <option value="sayembara">Sayembara</option>
                                        <option value="lain">Lainnya</option>
                                    </select>
                                    <div v-if="errors.category" class="invalid-feedback">{{ errors.category }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-bold">Tanggal</label>
                                    <input type="date" class="form-control" :class="{ 'is-invalid': errors.date }" v-model="form.date">
                                    <div v-if="errors.date" class="invalid-feedback">{{ errors.date }}</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bold">Template</label>
                                    <select class="form-select" :class="{ 'is-invalid': errors.template }" v-model="form.template">
                                        <option value="" disabled>Pilih salah satu opsi</option>
                                        <option v-for="(template, index) in templates" :key="index" :value="template.id">{{ template.title }}</option>
                                    </select>
                                    <div v-if="errors.template" class="invalid-feedback">{{ errors.template }}</div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-8">
                                    <label class="fw-bold">File Excel</label>
                                    <input type="file" @change="form.file = $event.target.files[0]" class="form-control" :class="{ 'is-invalid': errors.file }">
                                    <div v-if="errors.file" class="invalid-feedback">{{ errors.file }}</div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">Proses</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="errors.import_failed" class="row mt-4">
            <div class="col-md-12">
                <div class="alert alert-danger shadow border-0">
                    <h6 class="fw-bold"><i class="fa fa-exclamation-triangle"></i> Beberapa data gagal diimpor:</h6>
                    <ul class="mb-0 mt-2">
                        <li v-for="(msg, index) in errors.import_failed" :key="index">
                            {{ msg }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

export default {
    layout: LayoutAdmin,
    components: { Head, Link },
    props: {
        event: Object,
        templates: Array,
        errors: Object, // Di sini data error dari controller diterima
    },
    setup(props) {
        const form = useForm({
            file: null,
            category: '',
            date: '',
            template: '',
        });

        const submitImport = () => {
            form.post(`/admin/events/${props.event.id}/certificates-import`, {
                forceFormData: true,
                onSuccess: () => {
                    Swal.fire('Berhasil', 'Proses selesai', 'success');
                    form.reset();
                },
                onError: () => {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengimpor', 'error');
                },
            });
        };

        return { form, submitImport };
    }
}
</script>
