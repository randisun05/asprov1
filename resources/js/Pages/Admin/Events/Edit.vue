<template>

    <Head>
        <title>Administrator</title>
    </Head>
    <div class="container padding px-5">
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row py-4">
                                    <div class="col-md-2 col-12 mb-2">
                                        <Link href="/admin/events" class="btn btn-md btn-primary border-0 shadow w-100"
                                            type="button"><i class="fa fa-arrow-left"></i>
                                        Kembali</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-center">Update Event</h3>
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="row py-4 ms-5">
                                <div class="col-md-11">
                                    <span class="text-black">
                                        Judul
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <input type="text" class="form-control" placeholder="Masukan Nama kegiatan"
                                            v-model="form.title">
                                    </div>
                                    <div v-if="errors.title" class="alert alert-danger mt-2">
                                        {{ errors.title }}
                                    </div>
                                </div>

                                <div class="col-md-11">
                                    <span class="text-black">
                                        Deskripsi
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <Editor api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                            v-model="form.body" :init="{
                                                menubar: false,
                                                plugins: 'lists link image emoticons',
                                                toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons'
                                            }" />
                                    </div>
                                    <div v-if="errors.body" class="alert alert-danger mt-2">
                                        {{ errors.bodu }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <span class="text-black">
                                        Tanggal Pelaksanaan
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <input type="date" class="form-control"
                                            placeholder="Masukan Judul Cerita/Artikel/Berita" v-model="form.date">
                                    </div>
                                    <div v-if="errors.date" class="alert alert-danger mt-2">
                                        {{ errors.date }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <span class="text-black">
                                        Tutup Pendaftaran
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <input type="date" class="form-control"
                                            placeholder="Masukan Judul Cerita/Artikel/Berita" v-model="form.enddate">
                                    </div>
                                    <div v-if="errors.enddate" class="alert alert-danger mt-2">
                                        {{ errors.enddate }}
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <span class="text-black">
                                        Kapasitas Peserta
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <input type="number" class="form-control"
                                            placeholder="Masukan Kapasitas Peserta" v-model="form.participant">
                                    </div>
                                    <div v-if="errors.participant" class="alert alert-danger mt-2">
                                        {{ errors.participant }}
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <span class="text-black">
                                        Upload File
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <select type="form-select" class="form-control" id="file" v-model="form.file">
                                            <option value="N" selected>N</option>
                                            <option value="Y">Y</option>
                                        </select>
                                    </div>
                                    <div v-if="errors.participant" class="alert alert-danger mt-2">
                                        {{ errors.participant }}
                                    </div>
                                </div>

                                <div class="col-md-11">
                                    <span class="text-black">
                                        Tempat Pelaksanaan
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <input type="text" class="form-control"
                                            placeholder="Masukan Tempat Pelaksanaan Kegiatan" v-model="form.place">
                                    </div>
                                    <div v-if="errors.place" class="alert alert-danger mt-2">
                                        {{ errors.place }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <span class="text-black">
                                        Template Sertifikat
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <select class="form-select" v-model="form.template">
                                            <option value="" disabled selected>Pilih salah satu opsi</option>
                                            <option v-for="(template, index) in templates" :key="index"
                                                :value="template.id">{{ template.title }}</option>
                                        </select>
                                    </div>
                                    <div v-if="errors.template" class="alert alert-danger mt-2">
                                        {{ errors.template }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <span class="text-black">
                                        Link
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <input type="text" class="form-control"
                                            placeholder="Masukan Tempat Pelaksanaan Kegiatan" v-model="form.link">
                                    </div>
                                    <div v-if="errors.link" class="alert alert-danger mt-2">
                                        {{ errors.link }}
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <span class="text-black">Kategori</span>
                                    <div class="form-group mt-1 mb-4">
                                        <select class="form-control" v-model="form.category">
                                            <option disabled selected>Pilih Kategori</option>
                                            <option value="Kombel">Komunitas Belajar - Peserta</option>
                                            <option value="Kombel-Panitia">Komunitas Belajar - Panitia</option>
                                            <option value="Kombel-Narasumber">Komunitas Belajar - Narasumber</option>
                                            <option value="Kombel-Moderator">Komunitas Belajar - Moderator</option>
                                            <option value="Sayembara">Sayembara</option>
                                            <option value="Tryout">Tryout</option>
                                            <option value="lain">Lainnya</option>
                                        </select>
                                    </div>
                                    <div v-if="errors.category" class="alert alert-danger mt-2">
                                        {{ errors.category }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="text-black">
                                           Dimulai Pada
                                        </span>
                                        <div class="form-group mt-1">
                                            <input type="datetime-local" class="form-control" v-model="form.start_at">
                                        </div>
                                        <div v-if="errors.start_at" class="alert alert-danger mt-2">
                                            {{ errors.start_at }}
                                        </div>

                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-black">
                                            Berakhir Pada
                                        </span>
                                        <div class="form-group mt-1">
                                            <input type="datetime-local" class="form-control" v-model="form.end_at">
                                        </div>

                                        <div v-if="errors.end_at" class="alert alert-danger mt-2">
                                            {{ errors.end_at }}
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-5">
                                        <span class="text-black">
                                            Gambar/Foto
                                        </span>
                                        <div class="form-group mt-1">
                                            <input type="file" class="form-control" placeholder="Masukan Gambar/Foto"
                                                @change="updateImage" accept=".jpg, .jpeg, .png, .svg, .gif">
                                        </div>
                                        <div v-if="errors.image" class="alert alert-danger mt-2">
                                            {{ errors.image }}
                                        </div>
                                        <div v-if="errors[0]" class="alert alert-danger mt-2">
                                            {{ errors[0] }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-black">
                                            Durasi Kegiatan (Menit)
                                        </span>
                                        <div class="form-group mt-1">
                                            <input type="number" class="form-control"
                                                placeholder="Masukan Durasi Kegiatan" v-model="form.duration">
                                        </div>
                                        <div v-if="errors.duration" class="alert alert-danger mt-2">
                                            {{ errors.duration }}
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="text-black">
                                            Biaya Poin untuk Ikut Kegiatan
                                        </span>
                                        <div class="form-group mt-1">
                                            <input type="number" min="0" class="form-control"
                                                placeholder="0 = gratis, tidak pakai poin" v-model="form.point_cost">
                                        </div>
                                        <div v-if="errors.point_cost" class="alert alert-danger mt-2">
                                            {{ errors.point_cost }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-2">
                                    <button type="submit"
                                        class="btn btn-md btn-primary border-0 shadow me-2">Simpan</button>
                                    <button type="reset" class="btn btn-md btn-warning border-0 shadow">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
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

//import tinyMCE
import Editor from '@tinymce/tinymce-vue';

//import ref from vue
import {
    ref, reactive,
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
        Editor,
    },

    //props
    props: {
        errors: Object,
        event: Object,
        pointCost: [Number, String],
        templates: Array
    },


    //inisialisasi composition API
    setup(props) {

        //define form state
        const form = reactive({
            title: props.event.title,
            date: props.event.date,
            enddate: props.event.enddate,
            body: props.event.body,
            place: props.event.place,
            link: props.event.link,
            participant: props.event.participant,
            image: props.event.image,
            file: props.event.file,
            category: props.event.category,
            template: props.event.template,
            duration: props.event.duration,
            start_at: props.event.start_at,
            end_at: props.event.end_at,
            point_cost: props.pointCost,
        });


        //submit method
        const submit = () => {

            //send data to server
            router.post(`/admin/events/${props.event.id}`, {
                //data
                title: form.title,
                date: form.date,
                enddate: form.enddate,
                body: form.body,
                participant: form.participant,
                image: form.image,
                place: form.place,
                link: form.link,
                file: form.file,
                category: form.category,
                template: form.template,
                duration: form.duration,
                start_at: form.start_at,
                end_at: form.end_at,
                point_cost: form.point_cost,
            }, {
                onSuccess: () => {
                    //show success alert
                    Swal.fire({
                        title: 'Success!',
                        text: 'Event Anda Berhasil Disimpan.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },
            });
        }

        const updateImage = (event) => {
            form.image = event.target.files[0];
        };

        //return
        return {
            form,
            submit,
            updateImage
        }
    }
}

</script>

<style></style>
