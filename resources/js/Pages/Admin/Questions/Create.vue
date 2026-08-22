<template>

    <Head>
        <title>Buat Soal</title>
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
                                        <Link href="/admin/questions"
                                            class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                            class="fa fa-arrow-left"></i>
                                        Kembali</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-center">Buat Soal</h3>
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            


                            <div class="row py-4 ms-5">
                                 <div class="col-md-11">
                                    <span class="text-black">
                                        Kelompok Soal
                                    </span>
                                    <select class="form-control" v-model="form.question_category_id">
                                        <option value="" disabled>Pilih Kelompok Soal</option>
                                        <option :value="category.id" v-for="(category, index) in $page.props.categories" :key="index">
                                            {{ category.title }}
                                        </option>
                                    </select>
                                    <div v-if="errors.question_category_id" class="alert alert-danger mt-2">
                                        {{ errors.question_category_id }}
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <span class="text-black">
                                        Text Soal
                                    </span>
                                    <Editor api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb" v-model="form.text" :init="{
                                        menubar: false,
                                        plugins: 'lists link image emoticons',
                                        toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons'
                                    }" />
                                    <div v-if="errors.text" class="alert alert-danger mt-2">
                                        {{ errors.text }}
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <span class="text-black">
                                        Pilihan A
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <Editor api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                            v-model="form.a" :init="{
                                                menubar: false,
                                                plugins: 'lists link image emoticons',
                                                toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons'
                                            }" />
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <span class="text-black">
                                        Pilihan B
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <Editor api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                            v-model="form.b" :init="{
                                                menubar: false,
                                                plugins: 'lists link image emoticons',
                                                toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons'
                                            }" />
                                    </div>

                                </div>

                                <div class="col-md-5">
                                    <span class="text-black">
                                        Pilihan C
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <Editor api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                            v-model="form.c" :init="{
                                                menubar: false,
                                                plugins: 'lists link image emoticons',
                                                toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons'
                                            }" />
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <span class="text-black">
                                        Pilihan D
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <Editor api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                            v-model="form.d" :init="{
                                                menubar: false,
                                                plugins: 'lists link image emoticons',
                                                toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons'
                                            }" />
                                    </div>

                                </div>

                                <div class="col-md-5">
                                    <span class="text-black">
                                        Pilihan E
                                    </span>
                                    <div class="form-group mt-1 mb-4">
                                        <Editor api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                            v-model="form.e" :init="{
                                                menubar: false,
                                                plugins: 'lists link image emoticons',
                                                toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons'
                                            }" />
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <span class="text-black">
                                        Jawaban Benar
                                    </span>
                                    <select class="form-control" v-model="form.answer">
                                        <option value="1">A</option>
                                        <option value="2">B</option>
                                        <option value="3">C</option>
                                        <option value="4">D</option>
                                        <option value="5">E</option>
                                    </select>
                                    <div v-if="errors.answer" class="alert alert-danger mt-2">
                                        {{ errors.answer }}
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

//import tinyMCE
import Editor from '@tinymce/tinymce-vue';

//import Heade and Link from Inertia
import {
    Head,
    Link
} from '@inertiajs/vue3';

//import ref from vue
import {
    ref, reactive,
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
        Editor,
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
            text: '',
            a: '',
            b: '',
            c: '',
            d: '',
            e: '',
            answer: '',
            question_category_id: '',
        });


        //submit method
        const submit = () => {

            //send data to server
            router.post('/admin/questions/', {

                //data
                text: form.text,
                a: form.a,
                b: form.b,
                c: form.c,
                d: form.d,
                e: form.e,
                answer: form.answer,
                question_category_id: form.question_category_id,
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
