<template>

    <Head>
        <title>Edit Kelompok Soal</title>
    </Head>
    <div class="container padding px-5 text-black">
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row py-4">
                                    <div class="col-md-2 col-12 mb-2">
                                        <Link href="/admin/question-categories"
                                            class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                            class="fa fa-arrow-left"></i>
                                        Kembali</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-center">Edit Kelompok Soal</h3>
                        <form @submit.prevent="submit">
                            <div class="row py-4 ms-5">
                                <div class="col-md-11">
                                    <span class="text-black">
                                        Nama Kelompok Soal
                                    </span>
                                    <input type="text" class="form-control" v-model="form.title">
                                    <div v-if="errors.title" class="alert alert-danger mt-2">
                                        {{ errors.title }}
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-2">
                                    <button type="submit"
                                        class="btn btn-md btn-primary border-0 shadow me-2">Simpan</button>
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

//import ref from vue
import {
    reactive,
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
    },

    //props
    props: {
        errors: Object,
        data: Object,
    },

    //inisialisasi composition API
    setup(props) {

        //define form state
        const form = reactive({
            title: props.data.title,
        });

        //submit method
        const submit = () => {
            router.put('/admin/question-categories/' + props.data.id, {
                title: form.title,
            }, {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Kelompok Soal Berhasil Diupdate.',
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
