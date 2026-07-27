<template>

    <Head>
        <title>Hubungi Aspro SDMA</title>
    </Head>

    <!--page Header-->
    <section class="page-header parallaxie padding_top center-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-titles text-center">
                        <h2 class="whitecolor font-light bottom30"></h2>
                        <ul class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item">
                                <h3> Hubungi Aspro SDMA</h3>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--page Header ends-->

    <section id="registration" class="mt-4">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-12 col-md-12 col-sm-10">
                    <div class="mt-4">
                        <form @submit.prevent="submit" class="getin_form border-form" id="post"
                            enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <span class="ms-4">
                                        NIP
                                    </span>
                                    <div class="form-group bottom35 mt-1">
                                        <input type="text" class="form-control" v-model="form.nip"
                                            placeholder="Masukan NIP" maxlength="18"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                        <div v-if="errors.nip" class="alert alert-danger mt-2">
                                            {{ errors.nip }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <span class="ms-4">
                                        Nama Lengkap
                                    </span>
                                    <div class="form-group bottom35 mt-1">
                                        <input type="text" class="form-control" v-model="form.name"
                                            placeholder="Masukan Nama Lengkap Tanpa Gelar">
                                        <div v-if="errors.name" class="alert alert-danger mt-2">
                                            {{ errors.name }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <span class="ms-4">
                                        Email
                                    </span>
                                    <div class="form-group bottom35 mt-1">
                                        <input type="email" class="form-control" v-model="form.email"
                                            placeholder="Masukan Alamat Email Aktif">
                                        <div v-if="errors.email" class="alert alert-danger mt-2">
                                            {{ errors.email }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <span class="ms-4">
                                        Nomor Kontak
                                    </span>
                                    <div class="form-group bottom35 mt-1">
                                        <input type="text" class="form-control" v-model="form.contact"
                                            placeholder="Masukan Nomor Kontak Aktif" maxlength="13"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                        <div v-if="errors.contact" class="alert alert-danger mt-2">
                                            {{ errors.contact }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <label for="agency" class="ms-4">Instansi</label>
                                    <div class="form-group bottom35 mt-1">
                                        <div class="position-relative" ref="dropdownWrapper">
                                            <div class="form-group mt-1">
                                                <input type="text" class="form-control" placeholder="Pilih Instansi"
                                                    v-model="form.agency" @click="toggleSearch" readonly>
                                            </div>
                                            <div v-if="showDropdown" class="dropdown-menu position-absolute w-100">
                                                <input type="text" class="form-control mb-2" placeholder="Cari Instansi"
                                                    v-model="searchInstansi">
                                                <div class="dropdown-item-list" v-if="filteredInstansis.length > 0">
                                                    <button v-for="(instansi, index) in filteredInstansis" :key="index"
                                                        class="dropdown-item" @click="selectInstansi(instansi)">
                                                        {{ instansi.title }}
                                                    </button>
                                                </div>
                                                <template v-else>
                                                    <div class="dropdown-item disabled">Instansi tidak ditemukan</div>
                                                </template>

                                            </div>
                                        </div>
                                        <div v-if="errors.agency" class="alert alert-danger mt-2">
                                            {{ errors.agency }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3">
                                    <span class="ms-4">
                                        Jabatan
                                    </span>
                                    <div class="form-group bottom35 mt-1">
                                        <select type="form-select" class="form-control" v-model="form.position">
                                            <option value="" disabled selected>Pilih Jabatan</option>
                                            <option value="Jabatan Struktural">Jabatan Struktural K/L</option>
                                            <option value="Analis SDM Aparatur">Analis SDM Aparatur</option>
                                            <option value="Pranata SDM Aparatur">Pranata SDM Aparatur</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                        <div v-if="errors.position" class="alert alert-danger mt-2">
                                            {{ errors.position }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3">
                                    <span class="ms-4">
                                        Kategori
                                    </span>
                                    <div class="form-group bottom35 mt-1">
                                        <select type="form-select" class="form-control" v-model="form.category">
                                                <option value="" disabled selected>Pilih Kategori</option>
                                                <option value="Konsultasi">Konsultasi</option>
                                                <option value="Laporan">Laporan</option>
                                                <option value="Pengaduan">Pengaduan</option>
                                                <option value="Kerjasama">Kerjasama</option>
                                                <option value="Kerjasama">Lainnya</option>
                                        </select>
                                        <div v-if="errors.category" class="alert alert-danger mt-2">
                                            {{ errors.category }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <span class="ms-4">
                                        Dokumen (Jika Ada / Maks 2Mb)
                                    </span>
                                    <div class="form-group bottom35 mt-1">
                                        <div class="input-group">
                                            <input type="file" class="form-control" @change="updateDocument"
                                        >

                                        </div>
                                        <div v-if="errors.document" class="alert alert-danger mt-2">
                                            {{ errors.document }}
                                        </div>
                                        <div v-if="errors[0]" class="alert alert-danger mt-2">
                                            {{ errors[0] }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-3 mt-2">
                                    <div class="form-group bottom35 mt-1">
                                        <div class="form-group mb-4 ms-4">
                                            <div class="input-group">
                                                <span class="text px-5" id="generated-captcha">{{ code.value }}</span>
                                                <button class="btn btn btn-outline-primary no-border ms-2"
                                                    style="border-color: white;" @click.prevent="refreshCaptcha"><i
                                                        class="fa fa-undo"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3 mt-4">
                                    <div class="form-group mb-4 ms-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control me-3" id="captcha"
                                                v-model="form.captcha" placeholder="Enter Captcha" size="6">
                                        </div>
                                        <div v-if="errors.captcha" class="alert alert-danger mt-2">
                                            {{ errors.captcha }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <span class="ms-4">
                                        Topik Pertanyaan
                                    </span>
                                    <div class="form-group bottom35 mt-1">
                                        <input type="text" class="form-control" v-model="form.title"
                                            placeholder="Masukan Judul">
                                        <div v-if="errors.title" class="alert alert-danger mt-2">
                                            {{ errors.title }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <span class="ms-4">
                                       Detail Pertanyaan
                                    </span>
                                    <div class="form-group bottom35 mt-1">
                                        <textarea type="text" class="form-control" v-model="form.detail"
                                            placeholder="Masukan Detail"></textarea>
                                        <div v-if="errors.detail" class="alert alert-danger mt-2">
                                            {{ errors.detail }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <button type="submit" class="button btnprimary"
                                        style="width: 300px;">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


</template>

<script>

//import layout
import LayoutWebsite from '../../../Layouts/Website.vue';

//import Head from Inertia
import {
    Head
} from '@inertiajs/vue3';

//import reactive
import {
    reactive
} from 'vue';

//import sweet alert2
import Swal from 'sweetalert2';

//import inertia adapter
import { router } from '@inertiajs/vue3';

export default {

    data() {
        return {
            searchInstansi: '',
            showDropdown: false,
            form: {
                term: false // Default value of the checkbox
            }
        };
    },

    // computed property to filter instansis based on search input
    computed: {
        filteredInstansis() {
            return this.instansis.filter(instansi =>
                instansi.title.toLowerCase().includes(this.searchInstansi.toLowerCase())
            );
        }
    },

    methods: {
        // method to toggle dropdown visibility
        toggleSearch() {
            this.showDropdown = !this.showDropdown;
            if (this.showDropdown) {
                // Menambahkan event listener ke elemen body
                document.body.addEventListener('click', this.closeDropdownOutside);
            } else {
                // Menghapus event listener dari elemen body
                document.body.removeEventListener('click', this.closeDropdownOutside);
            }
        },

        // method to close dropdown when clicked outside
        closeDropdownOutside(event) {
            if (!this.$refs.dropdownWrapper.contains(event.target)) {
                this.showDropdown = false;
                document.body.removeEventListener('click', this.closeDropdownOutside);
            }
        },

        // method to select an instansi from dropdown
        selectInstansi(instansi) {
            this.form.agency = instansi.title;
            this.searchInstansi = ''; // reset search input after selection
            this.showDropdown = false; // hide dropdown after selection
        },
    },

    //layout
    layout: LayoutWebsite,

    //register component
    components: {
        Head,

    },

    //props
    props: {
        errors: Object,
        session: Object,
        instansis: Array
    },

    //define composition API
    setup() {

        //define form state
        const form = reactive({
            nip: '',
            name: '',
            email: '',
            contact: '',
            agency: '',
            position: '',
            title: '',
            document: '',
            captcha: '',
            detail: '',
            category: ''
        });

        //submit method
        const submit = () => {

            //send data to server
            router.post('/hubungi-aspro/store', {

                //data
                nip: form.nip,
                name: form.name,
                email: form.email,
                contact: form.contact,
                agency: form.agency,
                position: form.position,
                title: form.title,
                document: form.document,
                code: code.value,
                captcha: form.captcha,
                detail: form.detail,
                category: form.category
            }, {
                onSuccess: () => {
                    //show success alert
                    Swal.fire({
                        title: 'Success!',
                        text: 'Permintaan Berhasil Dikirim, Silakan Cek Email Anda.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    });
                },
            });

        }


        // Method to update the document file
        const updateDocument = (event) => {
            form.document = event.target.files[0];
        };

        //define captcha state
        const code = reactive({
            value: generateCaptcha(), // Generate captcha when the component initializes
        });

        // Function to generate random captcha
        function generateCaptcha() {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            const charactersLength = characters.length;
            for (let i = 0; i < 6; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }

        const refreshCaptcha = () => {
            code.value = generateCaptcha();
        }

        //return form state and submit method
        return {
            form,
            submit,
            updateDocument,
            refreshCaptcha,
            code,

        };

    }

}

</script>

<style scoped></style>
