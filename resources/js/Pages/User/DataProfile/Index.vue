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
                        <h3 class="text-center mt-4 text-black">
                            Profile Anggota
                        </h3>
                        <div class="col-md-2 col-12 ms-4">
                            <Link href="/user/profile/edit" class="btn btn-md btn-primary border-0 shadow w-100"
                                type="button"><i class="fa fa-plus-circle"></i> Update
                            Data</Link>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <Link href="/user/profile/" class="nav-link active" type="button">
                                Profile</Link>
                            </li>

                            <li class="nav-item">
                                <Link class="nav-link" href="/user/profile/data-jabatan">Data Jabatan</Link>
                            </li>

                        </ul>
                    </div>

                    <!-- Our Team-->

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

                    <!-- <div class="row ms-2 mb-3">
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
                    </div> -->
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
            </div>
        </div>
    </section>
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
            activeTab: "profile", // Set the default active tab
        };
    },

    methods: {
        setActiveTab(tabName) {
            this.activeTab = tabName;
        },
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
