<template>

    <Head>
        <title>Hubungi Aspro</title>
    </Head>

    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <Link href="/admin/archives" class="btn btn-md btn-primary border-0 shadow mb-3" type="button"><i
                    class="fa fa-arrow-left" aria-hidden="true"></i>
                Kembali</Link>
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <h5><i class="fa fa-bookmark"></i> Data Pengirim</h5>
                        <hr>
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th>NIP</th>
                                    <td>{{ archive.nip }}</td>

                                    <th>Jabatan</th>
                                    <td>{{ archive.position }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ archive.name }}</td>
                                    <th>Instansi</th>
                                    <td>{{ archive.agency }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ archive.email }}</td>
                                    <th>Status</th>
                                    <td> {{ archive.status == 1 ? 'Belum ditindak lanjuti' : 'telah didisposisi ke ' + archive.user_names }}</td>
                                </tr>
                                <tr>
                                    <th>Kontak</th>
                                    <td>{{ archive.contact }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="card border-0 shadow mt-2 col-6">
                    <div class="card-body">
                        <h5><i class="fa fa-bookmark"></i> Aksi</h5>

                        <hr>
                        Disposisi ke
                        <div>
                            <table class="table table-borderless" style="width: 50%;">
                                <tbody>
                                    <tr>
                                        <th class="border-0 rounded-start" style="width:5%">
                                            <input type="checkbox" v-model="form.allSelected" @change="selectAll" />
                                        </th>
                                        <th class="border-0 rounded-start">
                                             Check All
                                        </th>
                                    </tr>
                                    <tr v-for="(user, index) in users" :key="index">
                                        <td>
                                            <input type="checkbox" v-model="form.user_id" :id="user.id" :value="user.id" :checked="form.allSelected" />
                                        </td>
                                        <td class="text-left">
                                            <label class="form-check-label" :for="'user-' + user.id">
                                                {{ user.name }}
                                            </label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button @click="submitDisposisi(archive.id)" class="btn btn-primary mt-3">Disposisi</button>
                    </div>
                </div>

                <div class="card border-0 shadow mt-2 col-6">
                    <div class="card-body">
                        <h5><i class="fa fa-bookmark"></i> Histori Disposisi</h5>

                        <hr>
                        <table class="table table-borderless">
                            <thead class="thead-dark">
                                <tr class="border-0 text-center">
                                    <th class="border-0 rounded-start" style="width:5%">No.</th>
                                    <th class="border-0">Disposisi</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in datas" :key="index">
                                    <td class="fw-bold text-center">{{ ++index + (datas.current_page - 1) * datas.per_page }}</td>
                                    <td class="text-center">
                                        {{ data.user_id }}
                                    </td>
                                    <td class="text-center">
                                        {{ data.status == 1 ? 'Belum ditindak lanjuti' : 'telah didisposisi ke ' }}
                                    </td>
                                    <td class="text-center">
                                    {{ new Date(data.updated_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' }) }}        </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
                <div class="card border-0 shadow mt-2">
                    <div class="card-body">
                        <h5><i class="fa fa-bookmark"></i> Tindaklanjut</h5>
                        <hr>
                        <button @click="submitDisposisi(archive.id)" class="btn btn-primary mt-3">Disposisi</button>
                    </div>
                </div>
            </div>
    </div>





</template>

<script>
//import layout
import LayoutAdmin from '../../../Layouts/Admin.vue';


//import Heade and Link from Inertia
import {
    Head,
    Link
} from '@inertiajs/vue3';

//import reactive from vue
import { reactive, ref } from 'vue';

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
        archive: Object,
        users: Array,
        datas: Array,
    },




    //inisialisasi composition API
    setup(props) {



        //define form with reactive
        const form = reactive({
            archive_id: props.archive.id,
            user_id: [],
            allSelected: false,

        });

          //define method "selectAll"
    const selectAll = () => {
    if (form.allSelected) {
        form.user_id = props.users.map(user => user.id);
    } else {
        form.user_id = [];
    }
}

        const submitDisposisi = (id) => {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan disposisi arsip ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Send it!'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    router.post(`/admin/archives/disposition/${id}`, { user_id: form.user_id });
                }
            })
        }

        //return
        return {
            form,
            submitDisposisi,
            selectAll
        }

    }



}


</script>

<style></style>
