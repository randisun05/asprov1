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
                            <h3 class="text-center mt-4 text-black">Update Data Jabatan</h3>
                            <div class="col-md-2 col-12 ms-4">
                                <Link href="/user/profile/data-jabatan/edit"
                                    class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                    class="fa fa-plus-circle"></i>
                                Update Data</Link>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" href="/user/profile/data-jabatan">Data Jabatan</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Second group of columns -->
                        <div class="py-4" id="jabatan">
                            <div class="row ms-2 mb-3">
                                <div class="col-md-6 col-sm-6">
                                    <span> Jenis Pegawai </span>
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
                            <!-- <div class="row ms-2 mb-3">
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
                            </div> -->
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center py-5">
                        <button type="submit" class="button btnprimary" style="width: 300px;">Simpan</button>
                        <a href="/user/profile/data-jabatan" class="button btnsecondary" style="width: 300px;">Batal</a>
                    </div>
                </div>
            </div>
        </section>
    </form>
</template>

<script>
//import layout
import LayoutUser from "../../../Layouts/User.vue";

//import Head from Inertia
import { Head, Link } from "@inertiajs/vue3";

//import reactive
import { reactive } from "vue";

//import sweet alert2
import Swal from "sweetalert2";

//import inertia adapter
import { router } from "@inertiajs/vue3";

export default {
    data() {
        return {
            searchInstansi: '',
            showDropdown: false
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
        }
    },

    //layout
    layout: LayoutUser,

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
        instansis: Array
    },

    //define composition API
    setup(props) {
        //define form state
        const form = reactive({
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
            // wyear: props.data.wyear,
            // wmonth: props.data.wmonth,
        });

        //submit method
        const submit = () => {
            //send data to server
            router.post(
                "/user/profile/data-jabatan/edit",
                {
                    //data
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
                    // wyear: form.wyear,
                    // wmonth: form.wmonth,

                },
                {
                    onSuccess: () => {
                        //show success alert
                        Swal.fire({
                            title: "Success!",
                            text: "Data Anggota Berhasil Diupdate.",
                            icon: "success",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                    },
                }
            );
        };

        //return form state and submit method
        return {
            form,
            submit
        };
    },
};
</script>
<style></style>
