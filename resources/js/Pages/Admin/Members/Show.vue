<template>

    <Head>
        <title>Profile Anggota</title>
    </Head>

    <section id="profile" class="container padding">
        <div class="container-fluid px-5">

            <div class="row d-flex justify-content-center">
                <!-- First group of columns -->
                <div class="col-sm-12 card shadow">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row py-4">
                                <div class="col-md-2 col-12 mb-2 ms-4">
                                    <Link href="/admin/members/" class="btn btn-md btn-primary border-0 shadow w-100"
                                        type="button"><i class="fa fa-arrow-left"></i>
                                    Kembali</Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <h3 class="text-center">
                            Profile Anggota
                        </h3>
                    </div>

                    <div class="container py-4">
                        <div class="row">
                            <div class="col-lg-2 col-md-2">
                                <div class="text-center">
                                    <img v-if="form.image" :src="getImageUrl(form.image)" alt=""
                                        class="img-fluid rounded-circle" title="Ubah foto profil"
                                        style="width: 100%; height: 200px;" />
                                    <img v-else src="/assets/images/team-grey-1.jpg" alt=""
                                        class="img-fluid rounded-circle" title="Ubah foto profil"
                                        style="width: 100%; height: 200px;" />
                                </div>
                            </div>

                            <div class="col-lg-2 ms-5">
                                <div class="mt-3">
                                    <span> NIP </span>
                                </div>

                                <div class="mt-4">
                                    <span> Nama </span>
                                </div>

                                <div class="mt-4">
                                    <span> Instansi </span>
                                </div>
                                <div class="mt-4">
                                    <span> Gelar Depan </span>
                                </div>
                                <div class="mt-4">
                                    <span> Gelar Belakang </span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mt-2">
                                    <input type="text" class="form-control" v-model="form.nip" disabled
                                        maxlength="16" />
                                </div>

                                <div class="mt-2">
                                    <input type="text" class="form-control" v-model="form.name" disabled />
                                </div>

                                <div class="mt-2">
                                    <input type="text" class="form-control" v-model="form.agency" disabled />
                                </div>

                                <div class="mt-2">
                                    <input type="text" class="form-control" v-model="form.fname" disabled />
                                </div>

                                <div class="mt-2">
                                    <input type="text" class="form-control" v-model="form.lname" disabled />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Our Team ends-->
                </div>
                <div class="col-sm-12 card shadow mt-4">
                    <div class="py-4">
                        <span> Data Pribadi </span>
                    </div>

                    <div class="row ms-2 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <span class="text-black"> Tempat Lahir </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.place" disabled />
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-black"> Tanggal Lahir </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.dob" disabled />
                            </div>
                        </div>
                    </div>

                    <div class="row ms-2 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <span class="text-black">
                                Jenis Dokumen Identitas
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.docid" disabled />
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-black">
                                Nomor Dokumen Identitas
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.nodocid" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="row ms-2 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <span class="text-black"> Kontak </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.contact" disabled />
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-black"> Email </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.email" disabled />
                            </div>
                        </div>
                    </div>
                    <div class="row ms-2 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <span class="text-black"> Jenis Kelamin </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.gender" disabled />
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-black"> Agama </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.religion" disabled />
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
                            <span class="text-black"> Tingkat Pendidikan </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.leveledu" disabled
                                    maxlength="16" />
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span class="text-black">
                                Pendidikan Terakhir
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.lastedu" disabled />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Second group of columns -->
                <div class="col-sm-12 card shadow py-4 mt-4" id="jabatan">
                    <div class="row ms-2 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <span>
                                Jenis Pegawai PNS/NON PNS
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.type" disabled maxlength="16">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span>
                                Status
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.status" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row ms-2 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <span>
                                Instansi
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.agency" disabled>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span>
                                Satuan Kerja Induk
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.unit" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row ms-2 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <span>
                                Unit Organisasi
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.subunit" disabled>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span>
                                Lokasi Kerja
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.location" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row ms-2 mb-3">
                        <div class="col-md-3 col-sm-3">
                            <span>
                                Jabatan
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.position" disabled>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <span>
                                Jenjang
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.level" disabled>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span>
                                TMT Jabatan
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.tmtpos" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row ms-2 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <span>
                                Golongan Ruang
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.golru" disabled>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span>
                                TMT Golongan Ruang
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.tmtgolru" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row ms-2 mb-3">
                        <div class="col-md-6 col-sm-6">
                            <span>
                                Masa Kerja (Tahun)
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.wyear" disabled>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <span>
                                Masa Kerja (Bulan)
                            </span>
                            <div class="form-group mt-1">
                                <input type="text" class="form-control" v-model="form.wmonth" disabled>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>

<script>
//import layout
import LayoutAdmin from '../../../Layouts/Admin.vue';

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
            activeTab: "profile", // Set the default active tab
        };
    },

    methods: {
        setActiveTab(tabName) {
            this.activeTab = tabName;
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
            address: props.data.main.address,
            villages: props.data.main.villages,
            district: props.data.main.district,
            regency: props.data.main.regency,
            province: props.data.main.province,
            religion: props.data.main.religion,
            agency: props.data.agency,
            image: props.data.main.image,

            type: props.data.type,
            status: props.data.status,
            agency: props.data.agency,
            unit: props.data.unit,
            subunit: props.data.subunit,
            location: props.data.location,
            position: props.data.position,
            level: props.data.level,
            tmtpos: props.data.tmtpos,
            golru: props.data.golru,
            tmtgolru: props.data.tmtgolru,
            wyear: props.data.wyear,
            wmonth: props.data.wmonth,

        });

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        //return form state and submit method
        return {
            form,
            getImageUrl
        };
    },
};



</script>
<style>
.round-circle {
    width: 300px;
    /* Lebar bingkai tetap */
    height: 200px;
    /* Tinggi bingkai tetap */
    border: 1px solid #03BFCB;
    border-radius: 50%;
    padding: 7px;
    overflow: hidden;
    /* Menyembunyikan bagian gambar yang melewati batas bingkai */
    border: 2px solid black;
    /* Garis tepi bingkai */
    display: flex;
    /* Menggunakan flexbox untuk menengahkannya */
    justify-content: center;
    /* Menengahkan gambar secara horizontal */
    align-items: center;
    /* Menengahkan gambar secara vertikal */
}
</style>
