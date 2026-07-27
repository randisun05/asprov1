<template>

    <Head>
        <title>Tambah Penghargaan</title>
    </Head>
    <div class="px-5 shadow padding">
        <div class="row">
            <div class="col-md-12">
                <div class="row py-4">
                    <div class="col-md-2 col-12 mb-2">
                        <Link href="/admin/achievements" class="btn btn-md btn-primary border-0 shadow w-100"
                            type="button"><i class="fa fa-arrow-left"></i>
                        Kembali</Link>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="row">
                    <h3 class="text-center">Tambah Penghargaan</h3>
                    <form @submit.prevent="submit" enctype="multipart/form-data">
                        <div class="row py-4">
                            <div class="col-md-3">
                                <span class="text-black">NIP</span>
                                <div class="form-group mt-1 mb-4">
                                    <input type="text" class="form-control" placeholder="Masukan NIP" v-model="form.nip"
                                        @input="searchNIP">
                                </div>
                                <div v-if="errors.nip" class="alert alert-danger mt-2">
                                    {{ errors.nip }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <span class="text-black">Nama</span>
                                <div class="form-group mt-1 mb-4">
                                    <input type="text" class="form-control" v-model="form.name" disabled>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <span class="text-black">Instansi</span>
                                <div class="form-group mt-1 mb-4">
                                    <input type="text" class="form-control" v-model="form.agency" disabled>
                                </div>

                            </div>
                            <div class="col-md-7">
                                <span class="text-black">Judul Penghargaan</span>
                                <div class="form-group mt-1 mb-4">
                                    <input type="text" class="form-control" placeholder="Masukan Judul Penghargaan"
                                        v-model="form.title">
                                </div>
                                <div v-if="errors.title" class="alert alert-danger mt-2">
                                    {{ errors.title }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <span class="text-black">Kategori</span>
                                <div class="form-group mt-1 mb-4">
                                    <select class="form-control" v-model="form.category">
                                        <option selected disabled>Pilih Kategori</option>
                                        <option value="penghargaan">Penghargaan</option>
                                        <option value="anugrah">Anugrah</option>
                                    </select>
                                </div>
                                <div v-if="errors.category" class="alert alert-danger mt-2">
                                    {{ errors.category }}
                                </div>
                            </div>

                            <div class="col-md-11">
                                <span class="text-black">Deskripsi</span>
                                <div class="form-group mt-1 mb-4">
                                    <input class="form-control" placeholder="Masukan Deskripsi"
                                        v-model="form.description">
                                </div>
                                <div v-if="errors.description" class="alert alert-danger mt-2">
                                    {{ errors.description }}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <span class="text-black">Tanggal</span>
                                <div class="form-group mt-1 mb-4">
                                    <input type="date" class="form-control" v-model="form.date">
                                </div>
                                <div v-if="errors.date" class="alert alert-danger mt-2">
                                    {{ errors.date }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <span class="text-black">Dokumen</span>
                                <div class="form-group mt-1 mb-4">
                                    <input type="file" class="form-control" @change="updateDocument" accept=".pdf">
                                </div>
                                <div v-if="errors.document" class="alert alert-danger mt-2">
                                    {{ errors.document }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <span class="text-black">Foto</span>
                                <div class="form-group mt-1 mb-4">
                                    <input type="file" class="form-control" @change="updateImage" accept="jpg, jpeg, png">
                                </div>
                                <div v-if="errors.image" class="alert alert-danger mt-2">
                                    {{ errors.image }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <span class="text-black">Icon</span>
                                <div class="form-group">
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="iconDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i :class="form.icon"></i> {{ form.icon || 'Pilih Icon' }}
                                                    </button>
                                                    <ul class="dropdown-menu w-100" aria-labelledby="iconDropdown">
                                                        <li v-for="icon in icons" :key="icon" @click="form.icon = icon" class="dropdown-item">
                                                            <i :class="icon"></i> {{ icon }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>                                <div v-if="errors.icon" class="alert alert-danger mt-2">
                                    {{ errors.icon }}
                                </div>
                            </div>

                            <div class="col-md-11 text-center">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
//import layout
import LayoutAdmin from '../../../Layouts/Admin.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
//import Heade and Link from Inertia
import {
    Head,
    Link
} from '@inertiajs/vue3';

export default {
    layout: LayoutAdmin,

    //register component
    components: {
        Head,
        Link,

    },

    props: {
        errors: Object,
        members: Array, // Pass the members array as a prop
        data: Object
    },
    setup(props) {
        const form = ref({
            nip: props.data.member.nip,
            name: props.data.member.name,
            title: props.data.title,
            category: props.data.category,
            description: props.data.description,
            date: props.data.date,
            icon: props.data.icon,
            image: null,
            document: null
        });

        const icons = ref([
            'fa fa-star',
            'fa fa-trophy',
            'fa fa-medal',
            'fa fa-certificate',
            'fa fa-award'
        ]);

        // Method to update the document file
        const updateDocument = (event) => {
            form.document = event.target.files[0];
        };

        const updateImage = (event) => {
            form.image = event.target.files[0];
        };

        const searchNIP = () => {
            const member = props.members.find(member => member.nip === form.value.nip);
            if (member) {
                form.value.name = member.name;
                form.value.agency = member.agency;
            } else {
                form.value.name = '';
                form.value.agency = '';
            }
        };

        //submit method
        const submit = () => {
            //send data to server
            router.post(`/admin/achievements/${props.data.id}`, {
                //data
                nip: form.value.nip,
                title: form.value.title,
                category: form.value.category,
                description: form.value.description,
                date: form.value.date,
                image: form.image,
                document: form.document,
                icon: form.value.icon,


            }, {
                onSuccess: () => {
                    //show success alert
                    Swal.fire({
                        title: 'Success!',
                        text: 'Data Anda Berhasil Disimpan.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },
            });
        };

        return {
            form,
            icons,
            updateDocument,
            updateImage,
            searchNIP,
            submit
        };
    }
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
