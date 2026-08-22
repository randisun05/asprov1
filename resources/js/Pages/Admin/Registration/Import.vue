<template>
    <Head>
        <title>Administrator</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row mt-1">
            <div class="col-md-12">
                <Link href="/admin/registration" class="btn btn-md btn-primary border-0 shadow mb-3" type="button"><i class="fa fa-arrow-left" aria-hidden="true"> </i>
                  Kembali</Link>
                  <a href="/assets/excel/register.xlsx" target="_blank"
                    class="btn btn-md btn-success border-0 shadow mb-3 text-white ms-2" type="button"><i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    Contoh Format</a>

                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h3 class="darkcolor bottom35 text-center">Import Registrasi Anggota</h3>
               <form @submit.prevent="submit" class="getin_form border-form" id="login">
                  <div class="row">
                     <div class="col-md-6 col-sm-6">
                        <span class="ms-4">
                                 Upload File
                        </span>
                        <div class="form-group bottom35 mt-1">
                            <div class="input-group">
                                <input type="file" class="form-control" @input="form.file = $event.target.files[0]">
                            </div>
                            <div v-if="errors.file" class="alert alert-danger mt-2">
                                    {{ errors.file }}
                                </div>
                                <div v-if="errors[0]" class="alert alert-danger mt-2">
                                    {{ errors[0] }}
                                </div>
                        </div>
                     </div>

                     <div class="row d-flex justify-content-center">
                        <button type="submit" class="button btnprimary" style="width: 300px;">Upload</button>
                        <button type="reset" class="button btnsecondary ms-3" style="width: 300px;">Reset</button>
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

    //import Head from Inertia
    import {
        Head, Link
    } from '@inertiajs/vue3';

    //import reactive
    import {
        reactive
    } from 'vue';

    //import inertia adapter
    import { router } from '@inertiajs/vue3';

    export default {

        //layout
        layout: LayoutAdmin,

        //register component
        components: {
            Head,
            Link
        },

        //props
        props: {
            errors: Object,
            session: Object
        },

        //define composition API
        setup() {

            //define form state
            const form = reactive({
                file: '',
            });

            //submit method
            const submit = () => {

                //send data to server
                router.post('/admin/registration/import', {
                    //data
                    file: form.file,
                });

            }

            //return form state and submit method
            return {
                form,
                submit,

            };

        }

    }

</script>

<style>

</style>
