<template>

    <Head>
        <title>Buat Kategori</title>
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
                                        <Link href="/admin/posts" class="btn btn-md btn-primary border-0 shadow w-100"
                                            type="button"><i class="fa fa-arrow-left"></i>
                                        Kembali</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-center">Buat Kategori</h3>
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="row py-4 ms-5">
                                <div class="col-md-11">
                                    <span class="text-black">
                                        Judul
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <input type="text" class="form-control" placeholder="Masukan Title"
                                            v-model="form.title">
                                    </div>
                                    <div v-if="errors.title" class="alert alert-danger mt-2">
                                        {{ errors.title }}
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
    },


    //inisialisasi composition API
    setup() {

        //define form state
        const form = reactive({
            title: '',

        });


        //submit method
        const submit = () => {

            //send data to server
            router.post('/admin/category/store', {

                //data
                title: form.title,


            }, {
                onSuccess: () => {
                    //show success alert
                    Swal.fire({
                        title: 'Success!',
                        text: 'Kategori Berhasil Disimpan.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },
            });
        }



        //return
        return {
            form,
            submit,


        }
    }
}

</script>

<style>
.text-black {
    color: black;
}
</style>
