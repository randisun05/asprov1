<template>

    <Head>
        <title>Import Soal</title>
    </Head>
    <div class="container padding px-5 text-black">
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12"></div>
                            <div class="row py-6">
                                <div class="col-md-2 col-12 mb-2">
                                    <Link href="/admin/questions"
                                        class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                        class="fa fa-arrow-left"></i>
                                    Kembali</Link>
                                </div>
                                <div class="col-md-2 col-12 mb-2">
                                    <a href="/assets/excel/import.xlsx" target="_blank"
                                    class="btn btn-md btn-success border-0 shadow mb-3 text-white" type="button"><i
                                        class="fa fa-file me-2"></i> Format Import</a>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-center">Import Soal</h3>
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="row py-4 ms-5">
                                <div class="col-md-5">
                                    <span class="text-black">
                                        Kelompok Soal
                                    </span>
                                    <select class="form-select" v-model="form.question_category_id">
                                        <option value="" disabled>Pilih Kelompok Soal</option>
                                        <option :value="category.id" v-for="(category, index) in $page.props.categories" :key="index">
                                            {{ category.title }}
                                        </option>
                                    </select>
                                <div v-if="errors.question_category_id" class="alert alert-danger mt-2">
                                    {{ errors.question_category_id }}
                                </div>
                            </div>

                                <div class="col-md-5">
                                    <span class="text-black">
                                        Upload File Excel
                                    </span>
                                   <input type="file" class="form-control" @input="form.file = $event.target.files[0]">
                                <div v-if="errors.file" class="alert alert-danger mt-2">
                                    {{ errors.file }}
                                </div>
                                <div v-if="errors[0]" class="alert alert-danger mt-2">
                                    {{ errors[0] }}
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

//import ref from vue
import {
    reactive,
} from 'vue';

//import inertia adapter
import { router } from '@inertiajs/vue3';

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
        categories: Array,
    },


    //inisialisasi composition API
    setup() {

        //define form state
        const form = reactive({
            file: null,
            question_category_id: '',
        });


        //submit method
        const submit = () => {

            //send data to server
            router.post('/admin/questions/' + form.question_category_id + '/import', {

                //data
                file: form.file,
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
