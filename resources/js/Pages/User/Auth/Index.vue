<template>
    <div>
        <Head>
            <title>Reset Password</title>
        </Head>

        <!--page Header-->
            <section class="page-header parallaxie padding_top center-block">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-titles text-center">
                        <h2 class="whitecolor font-light bottom30"></h2>
                        <ul class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><h3>Reset Password</h3></li>
                            <li class="breadcrumb-item active" aria-current="page"></li>
                        </ul>
                        </div>
                    </div>
                </div>
            </div>
            </section>
            <!--page Header ends-->

            <div v-if="errors.message" class="alert alert-danger mt-2">
                    {{ errors.message }}
            </div>


        <form @submit.prevent="submit" class="getin_form border-form" id="data">
            <div class="container-fluid mb-5 mt-5">
                <div class="row mt-1">
                    <div class="col-md-12">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <div class="col-lg-6">
                                    <div v-if="$page.props.session.error" class="alert alert-danger mt-2">
                                    {{ $page.props.session.error }}
                                    </div>

                                    <div v-if="$page.props.session.success" class="alert alert-success mt-2">
                                    {{ $page.props.session.success }}
                                    </div>
                                    <div class="mt-4">
                                        <span> Password Baru </span>
                                        <div class="input-group">
                                            <input :type="passwordFieldType.password" class="form-control" v-model="form.password" />
                                            <button type="button" class="btn btn-outline-secondary" @click="togglePassword('password')">
                                                <i :class="{'fa fa-eye': passwordFieldType.password === 'text', 'fa fa-eye-slash': passwordFieldType.password === 'password'}"></i>
                                            </button>
                                        </div>
                                        <div v-if="errors.password" class="alert-danger mt-1 rounded">
                                            {{ errors.password }}
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <span> Ketik Ulang Password Baru </span>
                                        <div class="input-group">
                                            <input :type="passwordFieldType.password_confirmation" class="form-control" v-model="form.password_confirmation" />
                                            <button type="button" class="btn btn-outline-secondary" @click="togglePassword('password_confirmation')">
                                                <i :class="{'fa fa-eye': passwordFieldType.password_confirmation === 'text', 'fa fa-eye-slash': passwordFieldType.password_confirmation === 'password'}"></i>
                                            </button>
                                        </div>
                                        <div v-if="errors.password_confirmation" class="alert-danger mt-1 rounded">
                                            {{ errors.password_confirmation }}
                                        </div>
                                    </div>
                                    <div v-if="form.password !== form.password_confirmation" class="alert-danger mt-1 rounded">
                                        Password Baru dan Konfirmasi Password harus sama.
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-center py-5">
                                    <button type="submit" class="button btnprimary" style="width: 300px;">Simpan</button>
                                    <Link href="/user/profile" class="button btnsecondary" style="width: 300px;">Batal</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
//import layout
import LayoutWebsite from '../../../Layouts/Website.vue';

//import Head and Link from Inertia
import { Head, Link } from '@inertiajs/vue3';

//import ref from vue
import { reactive } from 'vue';

//import inertia adapter
import { router } from '@inertiajs/vue3';

//import sweet alert2
import Swal from 'sweetalert2';

export default {

    //layout
    layout: LayoutWebsite,

    //register component
    components: {
        Head,
        Link,
    },

    //props
    props: {
        member: Object,
        errors: Object,
        session: Object
    },

    //inisialisasi composition API
    setup(props) {
        const form = reactive({
            password: '',
            password_confirmation: '',
        });

        // Object to hold the current type of password input fields
        const passwordFieldType = reactive({
            password: 'password',
            password_confirmation: 'password'
        });

        // Function to toggle the password input type between 'password' and 'text'
        const togglePassword = (fieldType) => {
            passwordFieldType[fieldType] = passwordFieldType[fieldType] === 'password' ? 'text' : 'password';
        };

        //submit method
        const submit = () => {

            router.put(
                (`/user/forget-password/${props.member['code-password']}/reset`),
                {
                    //data
                    password: form.password,
                    password_confirmation: form.password_confirmation,

                },
            );
        };

        //return
        return {
            form,
            submit,
            passwordFieldType,
            togglePassword,
        }
    }
}
</script>

<style></style>
