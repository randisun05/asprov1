<template>

    <Head>
        <title>Profile Anggota</title>
    </Head>
    <form @submit.prevent="submit" class="getin_form border-form padding" id="data">
        <section id="profile" class="container mt-4">
            <div class="container-fluid px-5">
                <div class="row d-flex justify-content-center">
                    <!-- First group of columns -->
                    <div class="col-sm-12 card shadow">
                        <div class="row">
                            <h3 class="text-center mt-4 text-black">
                                Update Profile Anggota
                            </h3>
                        </div>



                        <div class="container py-4">
                            <div class="row">
                                <div class="col-lg-2 col-md-2">
                                    <div class="text-center">
                                        <img :src="getImageUrl(form.image)" alt="Foto Profil"
                                            class="img-fluid rounded-circle" title="Ubah foto profil"
                                            style="width: 100%; height: 200px;" />
                                        <input ref="fileInput" type="file" style="display: none" @change="updateImage"
                                            accept=".jpg, .jpeg, .png" />
                                        <button @click="openFileInput" type="button" class="btn btn-secondary mt-2">Ubah
                                            Foto</button>
                                    </div>
                                </div>

                                <div class="col-lg-1">

                                </div>

                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-3 ms-5 py-2">
                                            <span> NIP </span>
                                        </div>

                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" v-model="form.nip" maxlength="18"

                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                                            <div v-if="errors.nip" class="alert-danger mt-1 rounded">
                                                {{ errors.nip }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-lg-3 ms-5 py-2">
                                            <span> Nama </span>
                                        </div>

                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" v-model="form.name" />
                                            <div v-if="errors.name" class="rounded alert-danger mt-1">
                                                {{ errors.name }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-lg-3 ms-5 py-2">
                                            <span> Gelar Depan </span>
                                        </div>

                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" v-model="form.fname"
                                                onkeypress="return event.charCode < 48 || event.charCode > 57" />
                                            <div v-if="errors.fname" class="rounded alert-danger mt-1">
                                                {{ errors.fname }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-lg-3 ms-5 py-2">
                                            <span> Gelar Belakang </span>
                                        </div>

                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" v-model="form.lname"
                                                onkeypress="return event.charCode < 48 || event.charCode > 57" />
                                            <div v-if="errors.lname" class="rounded alert-danger mt-1">
                                                {{ errors.lname }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Data Pribadei-->
                    <div class="col-sm-12 card shadow mt-4">
                        <div class="py-4">
                            <span> Data Pribadi </span>
                        </div>

                        <div class="row ms-2 mb-3">
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black"> Tempat Lahir </span>
                                <div class="form-group mt-1">
                                    <input type="text" class="form-control" v-model="form.place" />
                                </div>
                                <div v-if="errors.place" class="rounded alert-danger mt-2">
                                    {{ errors.place }}
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black"> Tanggal Lahir </span>
                                <div class="form-group mt-1">
                                    <input type="date" class="form-control" v-model="form.dob" />
                                </div>
                                <div v-if="errors.dob" class="rounded alert-danger mt-2">
                                    {{ errors.dob }}
                                </div>
                            </div>
                        </div>

                        <div class="row ms-2 mb-3">
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black">
                                    Jenis Dokumen Identitas
                                </span>
                                <div class="form-group mt-1">
                                    <select class="form-select" v-model="form.docid">
                                        <option value="" disabled selected>Pilih salah satu opsi</option>
                                        <option value="KTP">KTP</option>
                                        <option value="Passport">Passport</option>
                                    </select>
                                </div>
                                <div v-if="errors.docid" class="rounded alert-danger mt-2">
                                    {{ errors.docid }}
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black">
                                    Nomor Dokumen Identitas
                                </span>
                                <div class="form-group mt-1">
                                    <input type="text" class="form-control" v-model="form.nodocid" maxlength="25"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                                </div>
                                <div v-if="errors.nodocid" class="rounded alert-danger mt-2">
                                    {{ errors.nodocid }}
                                </div>
                            </div>
                        </div>
                        <div class="row ms-2 mb-3">
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black"> Kontak </span>
                                <div class="form-group mt-1">
                                    <input type="text" class="form-control" v-model="form.contact"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        maxlength="13" />
                                </div>
                                <div v-if="errors.contact" class="rounded alert-danger mt-2">
                                    {{ errors.contact }}
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black"> Email </span>
                                <div class="form-group mt-1">
                                    <input type="email" class="form-control" v-model="form.email" />
                                </div>
                                <div v-if="errors.email" class="rounded alert-danger mt-2">
                                    {{ errors.email }}
                                </div>
                            </div>
                        </div>
                        <div class="row ms-2 mb-3">
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black"> Jenis Kelamin </span>
                                <div class="form-group mt-1">
                                    <select class="form-select" v-model="form.gender">
                                        <option value="" disabled selected>Pilih salah satu opsi</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div v-if="errors.gender" class="rounded alert-danger mt-2">
                                    {{ errors.gender }}
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black"> Agama </span>
                                <div class="form-group mt-1">
                                    <select class="form-select" v-model="form.religion">
                                        <option value="" disabled selected>Pilih salah satu opsi</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Khatolik">Khatolik</option>
                                        <option value="Budha">Budha</option>
                                        <option value="Hindu">Hindu</option>
                                    </select>
                                </div>
                                <div v-if="errors.religion" class="rounded alert-danger mt-2">
                                    {{ errors.religion }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 card shadow mt-4">
                        <div class="py-4">
                            <span> Data Pendidikan </span>
                        </div>

                        <div class="row ms-2 mb-3">
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black">
                                    Tingkat Pendidikan
                                </span>
                                <div class="form-group mt-1">
                                    <select class="form-select" v-model="form.leveledu">
                                        <option value="" disabled selected>Pilih salah satu opsi</option>
                                        <option value="SMA/SMK">SMA/SMK</option>
                                        <option value="D-3">D-3</option>
                                        <option value="D-4">D-4</option>
                                        <option value="S-1">S-1</option>
                                        <option value="S-2">S-2</option>
                                        <option value="S-3">S-3</option>
                                    </select>

                                </div>
                                <div v-if="errors.leveledu" class="rounded alert-danger mt-2">
                                    {{ errors.leveledu }}
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <span class="text-black">
                                    Pendidikan Terakhir
                                </span>
                                <div class="form-group mt-1">
                                    <input type="text" class="form-control" v-model="form.lastedu" />
                                </div>
                                <div v-if="errors.lastedu" class="rounded alert-danger mt-2">
                                    {{ errors.lastedu }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 card shadow mt-4 py-4" id="jabatan">
                            <div class="row ms-2 mb-3">
                                <div class="col-md-6 col-sm-6">
                                    <span> Jenis Pegawai PNS/NON PNS </span>
                                    <div class="form-group mt-1">
                                        <select type="form-select" class="form-control" v-model="form.type">
                                            <option value="" disabled selected>Pilih salah satu opsi</option>
                                            <option value="PNSP">PNS Pusat</option>
                                            <option value="PPPKP">PPPK Pusat</option>
                                            <option value="PNSD">PNS Daerah</option>
                                            <option value="PPPKD">PPPK Daerah</option>
                                        </select>
                                    </div>
                                    <div v-if="errors.type" class="rounded alert-danger mt-2">
                                        {{ errors.type }}
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <span> Status </span>
                                    <div class="form-group mt-1">
                                        <input type="text" class="form-control" v-model="form.status" readonly>
                                        <!-- <select type="form-select" class="form-control" v-model="form.status">
                                            <option value="" disabled selected>Pilih salah satu opsi</option>
                                            <option value="Aktif">Aktif</option>
                                            <option value="Non-Aktif">Non-Aktif</option>
                                        </select> -->
                                    </div>
                                    <div v-if="errors.status" class="rounded alert-danger mt-2">
                                        {{ errors.status }}
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-2 mb-3">
                                <div class="col-md-6 col-sm-6">
                                    <span> Instansi </span>
                                    <div class="form-group mt-1">
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
                                                    <div class="dropdown-item disabled">Instansi tidak ditemukan
                                                    </div>
                                                </template>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <span> Satuan Kerja Induk </span>
                                    <div class="form-group mt-1">
                                        <input type="text" class="form-control" v-model="form.unit" />
                                    </div>
                                    <div v-if="errors.unit" class="rounded alert-danger mt-2">
                                        {{ errors.unit }}
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-2 mb-3">
                                <div class="col-md-6 col-sm-6">
                                    <span> Unit Organisasi </span>
                                    <div class="form-group mt-1">
                                        <input type="text" class="form-control" v-model="form.subunit" />
                                    </div>
                                    <div v-if="errors.subunit" class="rounded alert-danger mt-2">
                                        {{ errors.subunit }}
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <span> Lokasi Kerja </span>
                                    <div class="form-group mt-1">
                                        <input type="text" class="form-control" v-model="form.location" />
                                    </div>
                                    <div v-if="errors.location" class="rounded alert-danger mt-2">
                                        {{ errors.location }}
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-2 mb-3">
                                <div class="col-md-3 col-sm-3">
                                    <span> Jabatan </span>
                                    <div class="form-group mt-1">
                                        <select type="form-select" class="form-control" v-model="form.position">
                                            <option value="" disabled>Pilih Jabatan</option>
                                            <option value="Analis SDM Aparatur" :selected="form.position === 'Analis SDM Aparatur'">Analis SDM Aparatur</option>
                                            <option value="Pranata SDM Aparatur" :selected="form.position === 'Pranata SDM Aparatur'">Pranata SDM Aparatur</option>
                                        </select>
                                        <div v-if="errors.position" class="alert alert-danger mt-2">
                                            {{ errors.position }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-3">
                                    <span> Jenjang </span>
                                    <div class="form-group mt-1">
                                        <select type="form-select" class="form-control" v-model="form.level">
                                            <option :value="''" disabled selected>Jabatan Harus Dipilih</option>
                                            <template v-if="form.position === 'Analis SDM Aparatur'">
                                                <option value="Ahli Pertama" :selected="form.level === 'Ahli Pertama'">Ahli Pertama</option>
                                                <option value="Ahli Muda" :selected="form.level === 'Ahli Muda'">Ahli Muda</option>
                                                <option value="Ahli Madya" :selected="form.level === 'Ahli Madya'">Ahli Madya</option>
                                                <option value="Ahli Utama" :selected="form.level === 'Ahli Utama'">Ahli Utama</option>
                                            </template>
                                            <template v-else-if="form.position === 'Pranata SDM Aparatur'">
                                                <option value="Terampil" :selected="form.level === 'Terampil'">Terampil</option>
                                                <option value="Mahir" :selected="form.level === 'Mahir'">Mahir</option>
                                                <option value="Penyelia" :selected="form.level === 'Penyelia'">Penyelia</option>
                                            </template>
                                        </select>
                                        <div v-if="errors.level" class="alert alert-danger mt-2">
                                            {{ errors.level }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <span> TMT Jabatan </span>
                                    <div class="form-group mt-1">
                                        <input type="date" class="form-control" v-model="form.tmtpos" />
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-2 mb-3">
                                <div class="col-md-6 col-sm-6">
                                    <span> Golongan Ruang </span>
                                    <div class="form-group mt-1">
                                        <select type="form-select" class="form-control" v-model="form.golru">
                                            <option value="" disabled>Pilih Golru</option>
                                            <option value="II/a">II/a</option>
                                            <option value="II/b">II/b</option>
                                            <option value="II/c">II/c</option>
                                            <option value="II/d">II/d</option>
                                            <option value="III/a">III/a</option>
                                            <option value="III/b">III/b</option>
                                            <option value="III/c">III/c</option>
                                            <option value="III/d">III/d</option>
                                            <option value="IV/a">IV/a</option>
                                            <option value="IV/b">IV/b</option>
                                            <option value="IV/c">IV/c</option>
                                            <option value="IV/d">IV/d</option>
                                            <option value="IV/e">IV/e</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <span> TMT Golongan Ruang </span>
                                    <div class="form-group mt-1">
                                        <input type="date" class="form-control" v-model="form.tmtgolru" />
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-2 mb-3">
                                <div class="col-md-6 col-sm-6">
                                    <span> Masa Kerja (Tahun) </span>
                                    <div class="form-group mt-1">
                                        <input type="number" class="form-control" v-model="form.wyear" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <span> Masa Kerja (Bulan) </span>
                                    <div class="form-group mt-1">
                                        <input type="number" class="form-control" v-model="form.wmonth" />
                                    </div>
                                </div>
                            </div>
                        </div>


                    <div class="row d-flex justify-content-center py-5">
                        <button type="submit" class="button btnprimary" style="width: 300px;">Simpan</button>
                        <a href="/admin/members" class="button btnsecondary" style="width: 300px;">Batal</a>
                    </div>
                </div>
            </div>
        </section>
    </form>
</template>

<script>
//import layout
import LayoutAdmin from '../../../Layouts/Admin.vue';

//import Head from Inertia
import { Head, Link } from "@inertiajs/vue3";

//import reactive
import { reactive, watch, ref, computed } from "vue";

//import inertia adapter
import { router } from "@inertiajs/vue3";

export default {

    data() {
        return {
            searchInstansi: '',
            showDropdown: false,
        };
    },

    // computed property to filter instansis based on search input
    computed: {
        filteredInstansis() {
            return this.instansis.filter(instansi =>
                instansi.title.toLowerCase().includes(this.searchInstansi.toLowerCase())
            );
        },
    },


    methods: {
        // // method to toggle dropdown visibility
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

        // // method to close dropdown when clicked outside
        closeDropdownOutside(event) {
            if (!this.$refs.dropdownWrapper.contains(event.target)) {
                this.showDropdown = false;
                document.body.removeEventListener('click', this.closeDropdownOutside);
            }
        },

        // // method to select an instansi from dropdown
        selectInstansi(instansi) {
            this.form.agency = instansi.title;
            this.searchInstansi = ''; // reset search input after selection
            this.showDropdown = false; // hide dropdown after selection
        },

        openFileInput() {
            this.$refs.fileInput.click();
        },
        updateImage(event) {
            this.form.image = event.target.files[0];
        },
    },

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
        session: Object,
        data: Object,
        instansis: Array,
        provinces: Array,
        cities: Array,
    },

    //define composition API
    setup(props) {


        //define form state
        const form = reactive({
            nip: props.data.main.nip,
            name: props.data.main.name,
            email: props.data.main.email,
            contact: props.data.main.contact,
            fname: props.data.main.fname,
            lname: props.data.main.lname,
            leveledu: props.data.main.leveledu,
            lastedu: props.data.main.lastedu,
            place: props.data.main.place,
            dob: props.data.main.dob,
            docid: props.data.main.docid,
            nodocid: props.data.main.nodocid,
            gender: props.data.main.gender,
            religion: props.data.main.religion,
            agency: props.data.agency,
            image: props.data.main.image,

            level: props.data.level,
            type: props.data.type,
            status: props.data.status,
            agency: props.data.agency,
            unit: props.data.unit,
            subunit: props.data.subunit,
            location: props.data.location,
            position: props.data.position,
            tmtpos: props.data.tmtpos,
            golru: props.data.golru,
            tmtgolru: props.data.tmtgolru,
            wyear: props.data.wyear,
            wmonth: props.data.wmonth,
        });

        //submit method
        const submit = () => {
            //send data to server
            router.put(`/admin/members/${props.data.id}`, {
                //data
                nip: form.nip,
                name: form.name,
                fname: form.fname,
                lname: form.lname,
                leveledu: form.leveledu,
                lastedu: form.lastedu,
                place: form.place,
                dob: form.dob,
                docid: form.docid,
                nodocid: form.nodocid,
                email: form.email,
                contact: form.contact,
                gender: form.gender,
                religion: form.religion,

                level: form.level,
                type: form.type,
                status: form.status,
                agency: form.agency,
                unit: form.unit,
                subunit: form.subunit,
                location: form.location,
                position: form.position,
                tmtpos: form.tmtpos,
                golru: form.golru,
                tmtgolru: form.tmtgolru,
                wyear: form.wyear,
                wmonth: form.wmonth,
            });
        };


        //send data to server


        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return imageName ? `/storage/${imageName}` : "/assets/images/team-grey-1.jpg";
        }

        const updateImage = (event) => {
            // Buat objek FormData
            const formData = new FormData();
            // Tambahkan file gambar yang dipilih ke FormData
            formData.append('image', event.target.files[0]);
            // Kirim permintaan dengan Inertia
            router.post(`/admin/members/${props.data.id}/image`, formData);
        };


        //return form state and submit method
        return {
            form,
            submit,
            getImageUrl,
            updateImage,

        };
    },
};
</script>
<style>
.dropdown-select {
    max-height: 200px;
    /* Tentukan tinggi maksimal dropdown */
    overflow-y: auto;
    /* Aktifkan pengguliran vertikal jika item melebihi tinggi maksimal */
}
</style>
