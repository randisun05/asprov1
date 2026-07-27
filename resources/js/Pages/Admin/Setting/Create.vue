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
                                <h3 class="mb-4"> Buat Akun Admin</h3>
                                <div class="col-lg-6">
                                    <div class="mt-2">
                                        <span> NIP </span>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="nip" id="nip"
                                                v-model="form.nip" />
                                        </div>
                                        <div v-if="errors.nip" class="alert-danger mt-1 rounded">
                                            {{ errors.nip }}
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <span> Nama </span>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="name" id="name"
                                                v-model="form.name" />
                                        </div>
                                        <div v-if="errors.name" class="alert-danger mt-1 rounded">
                                            {{ errors.name }}
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <span> Email </span>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="email" id="email"
                                                v-model="form.email" />
                                        </div>
                                        <div v-if="errors.email" class="alert-danger mt-1 rounded">
                                            {{ errors.email }}
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <span> Role </span>
                                        <div class="input-group">
                                                <select type="form-select" class="form-control" v-model="form.role">
                                                    <option value="" disabled>Pilih Role</option>
                                                    <option value="administrator">Administrator</option>
                                                    <option value="humas">Humas</option>
                                                    <option value="keanggotaan">Keanggotaan</option>
                                                    <option value="pendanaan">Pendanaan</option>
                                                    <option value="hukum">Hukum</option>
                                                    <option value="kapasitas">Kapasitas Insani</option>
                                                    <option value="sekretariat">Sekretariat</option>
                                                </select>
                                                <div v-if="errors.role" class="alert alert-danger mt-2">
                                                    {{ errors.role }}
                                                </div>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <span> Posisi </span>
                                        <div class="input-group">
                                                <select type="form-select" class="form-control" v-model="form.position">
                                                    <option value="" disabled>Pilih Posisi</option>
                                                    <option value="kabid">Ketua Bidang</option>
                                                    <option value="sekretaris">Sekretaris</option>
                                                    <option value="bendahara">Bendahara</option>
                                                    <option value="anggota">Anggota</option>
                                                </select>
                                                <div v-if="errors.position" class="alert alert-danger mt-2">
                                                    {{ errors.position }}
                                                </div>
                                        </div>
                                    </div>


                                    <div class="mt-4">
                                        <span> Password Baru </span>
                                        <div class="input-group">
                                            <input v-if="passwordFieldType.password === 'password'" type="password"
                                                class="form-control" v-model="form.password" />
                                            <input v-else type="text" class="form-control" v-model="form.password" />
                                            <button type="button" class="btn btn-outline-secondary"
                                                @click="togglePassword('password')">
                                                <i v-if="passwordFieldType.password === 'password'"
                                                    class="fa fa-eye"></i>
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
                                            <input v-if="passwordFieldType.password_confirmation === 'password'"
                                                type="password" class="form-control"
                                                v-model="form.password_confirmation" />
                                            <input v-else type="text" class="form-control"
                                                v-model="form.password_confirmation" />
                                            <button type="button" class="btn btn-outline-secondary"
                                                @click="togglePassword('password_confirmation')">
                                                <i v-if="passwordFieldType.password_confirmation === 'password'"
                                                    class="fa fa-eye"></i>
                                                <i v-else class="fa fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        <div v-if="errors.password_confirmation" class="alert-danger mt-1 rounded">
                                            {{ errors.password_confirmation }}
                                        </div>
                                    </div>
                                    <div v-if="form.password !== form.password_confirmation"
                                        class="alert-danger mt-1 rounded">
                                        Password Baru dan Konfirmasi Password harus sama.
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-center py-5">
                                    <button type="submit" class="button btnprimary"
                                        style="width: 300px;">Simpan</button>
                                    <Link href="/user/profile" class="button btnsecondary" style="width: 300px;">Batal
                                    </Link>
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
import LayoutAdmin from '../../../Layouts/Admin.vue';

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
    layout: LayoutAdmin,

    //register component
    components: {
        Head,
        Link,
    },

    //props
    props: {

        errors: Object,
    },

    //inisialisasi composition API
    setup(props) {
        const form = reactive({
            nip: '',
            name: '',
            email: '',
            role: '',
            password: '',
            password_confirmation: '',
            position: '',
        });

        // Object to hold the current type of password input fields
        const passwordFieldType = reactive({
            password: 'password',
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

            router.post(
                `/admin/setting/`,
                {
                    //data
                    nip: form.nip,
                    name: form.name,
                    email: form.email,
                    role: form.role,
                    password: form.password,
                    password_confirmation: form.password_confirmation,
                    position: form.position,
                },
                {
                    onSuccess: () => {
                        //show success alert
                        Swal.fire({
                            title: "Success!",
                            text: "Admin Berhasil Disimpan.",
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
