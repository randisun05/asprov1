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
                        <h3 class="text-center mt-4 text-black">Data Jabatan</h3>
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
                                <Link class="nav-link" href="/user/profile">Profile</Link>
                            </li>
                            <li class="nav-item">
                                <Link class="nav-link active   " href="/user/profile/data-jabatan">Data Jabatan</Link>
                            </li>
                            <!-- <li class="nav-item">
                                                                <a class="nav-link" href="/user/profile/data-lainnya">Data Lainnya</a>
                                                            </li> -->
                        </ul>
                    </div>

                    <!-- Second group of columns -->
                    <div class="py-4" id="jabatan">
                        <div class="row ms-2 mb-3">
                            <div class="col-md-6 col-sm-6">
                                <span>
                                    Jenis Pegawai
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
                        <!-- <div class="row ms-2 mb-3">
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
                        </div> -->
                    </div>
                </div>

            </div>
        </div>
    </section>


</template>

<script>
//import layout
import LayoutUser from '../../../Layouts/User.vue';

//import Head from Inertia
import {
    Head,
    Link
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
            activeTab: 'profile', // Set the default active tab

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
        Link
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

        //return form state and submit method
        return {
            form,
        };

    }

}

</script>
<style>
.text-black {
    color: black;
}
</style>
