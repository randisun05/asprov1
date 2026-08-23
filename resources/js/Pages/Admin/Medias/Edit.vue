<template>
    <Head>
        <title>Edit Media</title>
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
                                            <Link href="/admin/medias" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                                class="fa fa-arrow-left"></i>
                                            Kembali</Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-center">Edit Media</h3>
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                                <div class="row py-4 ms-5">
                                            <div class="col-md-11">
                                                <span class="text-black">
                                                Judul
                                                </span>
                                                <div class="form-group mt-1 mb-4">
                                                    <input type="text" class="form-control" placeholder="Masukan Title" v-model="form.title">
                                                </div>
                                                <div v-if="errors.title" class="alert alert-danger mt-2">
                                                    {{ errors.title }}
                                                </div>
                                            </div>

                                            <div class="col-md-11">
                                                <span class="text-black">
                                                Kategori
                                                </span>
                                                <div class="form-group mt-1 mb-4">
                                                    <select class="form-select" v-model="form.event_id">
                                                        <option v-for="(event, index) in events" :key="index" :value="event.id">{{ event.title }}</option>
                                                    </select>
                                                </div>
                                                <div v-if="errors.event_id" class="alert alert-danger mt-2">
                                                    {{ errors.event_id }}
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-5">
                                                    <span class="text-black">
                                                    Media
                                                    </span>
                                                    <div class="form-group mt-1">
                                                        <input type="file" class="form-control" placeholder="Masukan Gambar/Foto" @change="updateImage" accept=".jpg, .jpeg, .png, .svg, .gif">
                                                    </div>
                                                    <div v-if="errors.image" class="alert alert-danger mt-2">
                                                        {{ errors.image }}
                                                    </div>
                                                    <div v-if="errors[0]" class="alert alert-danger mt-2">
                                                        {{ errors[0] }}
                                                    </div>
                                                </div>
                                            </div>
                                </div>
                                        <div class="row d-flex justify-content-center">
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-md btn-primary border-0 shadow me-2">Simpan</button>
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
            events: Array,
            media: Object,
        },


        //inisialisasi composition API
        setup(props) {

        //define form state
        const form = reactive({
                title: props.media.title,
                event_id: props.media.event_id,
                media: '',
            });


            //submit method
            const submit = () => {

                //send data to server
                router.post(`/admin/medias/${props.media.id}`, {

                    //data
                    title: form.title,
                    event_id: form.event_id,
                    media: form.media,

                });
            }

            const updateImage = (event) => {
            form.media = event.target.files[0];
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

<style>
.text-black{
    color: black;
}
</style>
