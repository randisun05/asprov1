<template>

    <Head>
        <title>Registrasi Keanggotaan Aspro</title>
    </Head>

 <!--page Header-->
 <section class="page-header parallaxie padding_top center-block">
   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <div class="page-titles text-center">
               <h2 class="whitecolor font-light bottom30"></h2>
               <ul class="breadcrumb justify-content-center">
                 <li class="breadcrumb-item"><h3> Registrasi Anggota Kolektif</h3></li>
                 <li class="breadcrumb-item active" aria-current="page"></li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</section>
<!--page Header ends-->

<section id="registration" class="">
   <div class="container">
      <div class="row d-flex justify-content-center">
         <div class="col-lg-12 col-md-12 col-sm-10">
            <div class="logincontainer">
               <form @submit.prevent="submit" class="getin_form border-form" id="login">
                  <div class="row">
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


                     <div class="col-md-6 col-sm-6">
                        <span class="ms-4">
                                 Nama PIC
                        </span>
                        <div class="form-group bottom35 mt-1">
                                <input type="text" class="form-control" v-model="form.name" placeholder="Masukan Nama PIC">
                            <div v-if="errors.name" class="alert alert-danger mt-2">
                                {{ errors.name }}
                            </div>
                        </div>
                     </div>

                     <div class="col-md-6 col-sm-6">
                        <span class="ms-4">
                                 Kontak Aktif PIC
                        </span>
                        <div class="form-group bottom35 mt-1">
                            <input type="text" class="form-control" v-model="form.contact" placeholder="Masukan Kontak Aktif" maxlength="13" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                            <div v-if="errors.contact" class="alert alert-danger mt-2">
                                {{ errors.contact }}
                            </div>
                        </div>
                     </div>

                     <div class="col-md-6 col-sm-6">
                        <span class="ms-4">
                                 Email Aktif PIC
                        </span>
                        <div class="form-group bottom35 mt-1">
                                <input type="email" class="form-control" v-model="form.email" placeholder="Masukan Email Aktif PIC">
                            <div v-if="errors.email" class="alert alert-danger mt-2">
                                {{ errors.email }}
                            </div>
                        </div>
                     </div>

                     <div class="col-md-6 col-sm-6">
                        <span class="ms-4">
                                 Jumlah Data
                        </span>
                        <div class="form-group bottom35 mt-1">
                                <input type="number" class="form-control" v-model="form.total" placeholder="Masukan Jumlah Data" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                            <div v-if="errors.total" class="alert alert-danger mt-2">
                                {{ errors.total }}
                            </div>
                        </div>
                     </div>


                     <div class="col-md-6 col-sm-6">
                        <span class="ms-4">
                                 File    <a href="/assets/excel/register_group.xlsx" class="text-center mt-4" target="_blank"><u>( Unduh Template Excel )</u></a>
                        </span>
                        <div class="form-group bottom35 mt-1">
                            <div class="input-group">
                                <input type="file" class="form-control" @change="updateDocument" accept=".xls, .xlsx,">

                            </div>
                            <div v-if="errors.file" class="alert alert-danger mt-2">
                                    {{ errors.file }}
                                </div>
                                <div v-if="errors[0]" class="alert alert-danger mt-2">
                                    {{ errors[0] }}
                                </div>
                            </div>
                     </div>

                     <div class="col-md-5 col-sm-5 ms-2 me-5">
                        <button type="button" class="btn" @click="openModal">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" v-model="form.term">
                            <label class="form-check-label ms-2" for="agreeTerms">
                                Saya menyetujui peraturan organisasi.
                            </label>
                            </button>
                            <div v-if="errors.term" class="col-md-5 alert alert-danger mt-2">
                                {{ errors.term }}
                            </div>
                        </div>

                     <div class="col-md-3 col-sm-3 ms-5">
                            <div class="form-group bottom35">
                                <div class="form-group mb-4">
                                    <div class="input-group">
                                        <span class="text px-5" id="generated-captcha">{{ code.value }}</span>
                                        <button class="btn btn btn-outline-primary no-border" style="border-color: white;" @click.prevent="refreshCaptcha"><i class="fa fa-undo"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="col-md-3 col-sm-2">
                                <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control me-3" id="captcha" v-model="form.captcha" placeholder="Enter Captcha" size="6">
                                </div>
                                <div v-if="errors.captcha" class="alert alert-danger mt-2">
                                    {{ errors.captcha }}
                                </div>
                            </div>
                     </div>

                     <div class="row d-flex justify-content-center">
                        <button type="submit" class="button btnprimary" style="width: 300px;">Submit</button>
                    </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

 <!-- Modal -->
 <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Peraturan Organisasi</h5>
                </div>
                <div class="modal-body">
                    <p>Kewajiban dan Hak Anggota sesuai dengan Pasal 18 Anggaran Rumah Tangga Aspro SDMA adalah sebagai
                        berikut:</p>
                    <strong>Kewajiban:</strong>
                    <ul class="mb-2">
                        <p class="mb-0">1. Menjaga dan menjunjung tinggi martabat serta kehormatan Aspro SDMA;</p>
                        <p class="mb-0">2. Mematuhi Anggaran Dasar, Anggaran Rumah Tangga, serta peraturan keputusan
                            organisasi;
                        </p>
                        <p class="mb-0">3. Mematuhi kode etik dan kode perilaku Aspro SDMA;</p>
                        <p class="mb-0">4. Berpartisipasi aktif dalam kegiatan Aspro SDMA; dan</p>
                        <p class="mb-0">5. Membayar iuran anggota bagi Anggota Biasa dan Luar Biasa.</p>
                    </ul>
                    <strong>Hak:</strong>

                    <ul class="mb-2">
                        <p class="mb-0">1. Memperoleh layanan organisasi;</p>
                        <p class="mb-0">2.Memperoleh hak keterbukaan informasi terkait operasional organisasi;</p>
                        <p class="mb-0">3. Memilih dan dipilih sebagai pengurus; dan</p>
                        <p class="mb-0">4. Memberikan masukan dan saran kepada pengurus.</p>
                    </ul>

                    <strong class="mt-2">DISCLAIMER</strong>
                    <p style="text-align: justify;">Seluruh informasi, keterangan dan dokumen formulir pendaftaran
                        keanggotaan Aspro SDMA yang disampaikan melalui website https://asprosdma.id/ Asosiasi Profesi
                        Jabatan Fungsional Sumber Daya Manusia Aparatur (Aspro SDMA) yaitu informasi dan/atau keterangan
                        yang merupakan alat bukti hukum yang sah, karenanya Dokumen Elektronik tersebut bersifat
                        mengikat.
                        Mengacu pada Pasal 5 UU ITE No. 11 Tahun 2008 mengenai Informasi, Dokumen, dan Tanda Tangan
                        Elektronik.</p>
                    <p style="text-align: justify;">Sehubungan dengan Dokumen Elektronik merupakan data yang diberikan
                        oleh
                        pengguna dan dengan ini pengguna memberikan jaminan atas kekinian, keakuratan, dan kelengkapan,
                        atau
                        keandalan informasi yang diberikan.</p>
                    <p style="text-align: justify;">Oleh karenanya, segala hal yang berkaitan dengan diterimanya
                        dan/atau
                        dipergunakannya Dokumen Elektronik tersebut sebagai data yang diberikan oleh pengguna merupakan
                        tanggung jawab pribadi atas segala risiko yang mungkin timbul.</p>
                    <p style="text-align: justify;">Sehubungan dengan risiko dan tanggung jawab pribadi atas Dokumen
                        Elektronik, pengguna dengan ini menyetujui untuk melepaskan segala tanggung jawab dan risiko
                        hukum
                        kepada Aspro SDMA atas diterimanya dan/atau dipergunakannya Dokumen Elektronik tersebut.</p>
                    <p style="text-align: right;"><strong>Bidang Hukum dan Advokasi</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="checkbox" class="btn btn-primary" data-bs-dismiss="modal">Saya Setuju</button>
                </div>
            </div>
        </div>
    </div>



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

        openModal() {
            if (!this.form.term) {
                // Show modal only if the checkbox is not checked
                $('#staticBackdrop').modal('show');
            }
        }
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
                agency: '',
                name: '',
                contact: '',
                email: '',
                total: '',
                file: '',
                captcha: '',
                term: ''
            });


            //submit method
            const submit = () => {

                //send data to server
                router.post('/registration/group', {

                    //data
                    agency: form.agency,
                    name: form.name,
                    contact: form.contact,
                    email: form.email,
                    total: form.total,
                    file: form.file,
                    code: code.value,
                    captcha : form.captcha,
                    term : form.term
                } ,{
                    onSuccess: () => {
                        //show success alert
                        Swal.fire({
                            title: 'Success!',
                            text: 'File Berhasil Dikirim.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    },
                });

            }
            // Method to update the document file
            const updateDocument = (event) => {
                form.file = event.target.files[0];
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
