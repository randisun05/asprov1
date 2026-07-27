<template>
    <div>
        <Head>
            <title>Setting</title>
        </Head>
        <form @submit.prevent="submit" class="getin_form border-form padding" id="data">
            <div class="container-fluid mb-5 mt-5">
                <div class="row mt-1">
                    <div class="col-md-12">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <div class="col-lg-6">
                                    <div class="mt-2">
                                        <span> Password Lama </span>
                                        <div class="input-group">
                                            <input v-if="passwordFieldType.oldpassword === 'password'" type="password" class="form-control" v-model="form.oldpassword" />
                                            <input v-else type="text" class="form-control" v-model="form.oldpassword" />
                                            <button type="button" class="btn btn-outline-secondary" @click="togglePassword('oldpassword')">
                                                <i v-if="passwordFieldType.oldpassword === 'password'" class="fa fa-eye"></i>
                                                <i v-else class="fa fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        <div v-if="errors.oldpassword" class="alert-danger mt-1 rounded">
                                            {{ errors.oldpassword }}
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <span> Password Baru </span>
                                        <div class="input-group">
                                            <input v-if="passwordFieldType.password === 'password'" type="password" class="form-control" v-model="form.password" />
                                            <input v-else type="text" class="form-control" v-model="form.password" />
                                            <button type="button" class="btn btn-outline-secondary" @click="togglePassword('password')">
                                                <i v-if="passwordFieldType.password === 'password'" class="fa fa-eye"></i>
                                                <i v-else class="fa fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        <div v-if="errors.password" class="alert-danger mt-1 rounded">
                                            {{ errors.password }}
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <span> Ketik Ulang Password Baru </span>
                                        <div class="input-group">
                                            <input v-if="passwordFieldType.password_confirmation === 'password'" type="password" class="form-control" v-model="form.password_confirmation" />
                                            <input v-else type="text" class="form-control" v-model="form.password_confirmation" />
                                            <button type="button" class="btn btn-outline-secondary" @click="togglePassword('password_confirmation')">
                                                <i v-if="passwordFieldType.password_confirmation === 'password'" class="fa fa-eye"></i>
                                                <i v-else class="fa fa-eye-slash"></i>
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
import LayoutUser from '../../../Layouts/User.vue';

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
    layout: LayoutUser,

    //register component
    components: {
        Head,
        Link,
    },

    //props
    props: {
        user: Object,
        errors: Object,
    },

    //inisialisasi composition API
    setup(props) {
        const form = reactive({
            oldpassword: '',
            password: '',
            password_confirmation: '',
        });

        // Object to hold the current type of password input fields
        const passwordFieldType = reactive({
            password: 'password',
            oldpassword: 'password',
            password_confirmation: 'password'
        });

        // Function to toggle the password input type between 'password' and 'text'
        const togglePassword = (fieldType) => {
            if (fieldType in passwordFieldType) {
                passwordFieldType[fieldType] = passwordFieldType[fieldType] === 'password' ? 'text' : 'password';
            }
        };

        //submit method
        const submit = () => {

            router.put(
                "/user/setting/update",
                {
                    //data
                    oldpassword: form.oldpassword,
                    password: form.password,
                    password_confirmation: form.password_confirmation,

                },
                {
                    onSuccess: () => {
                        //show success alert
                        Swal.fire({
                            title: "Success!",
                            text: "Password Anggota Berhasil Diupdate.",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                    },
                }
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
