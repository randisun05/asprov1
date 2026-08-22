<template>

    <Head>
        <title>Kelompok Soal</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-12 mb-2">
                        <Link :href="`/admin/question-categories/create`" class="btn btn-md btn-primary border-0 shadow me-2" type="button"><i class="fa fa-plus-circle"></i> Tambah</Link>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search"
                                    placeholder="masukkan kata kunci dan enter...">
                                <span class="input-group-text border-0 shadow">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0 text-center">
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">Kelompok Soal</th>
                                        <th class="border-0" style="width:15%">Jumlah Soal</th>
                                        <th class="border-0 rounded-end" style="width:15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, index) in datas.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (datas.current_page - 1) *
                                            datas.per_page }}</td>
                                        <td>{{ data.title }}</td>
                                        <td class="text-center">{{ data.questions_count }}</td>
                                        <td class="text-center">
                                            <Link :href="`/admin/question-categories/${data.id}/edit`" class="btn btn-sm btn-info border-0 shadow me-2"
                                                type="button"><i class="fa fa-pencil-alt"></i></Link>
                                            <button @click.prevent="destroy(data.id)" class="btn btn-sm btn-danger border-0"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="datas.links" align="end" />
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
//import layout
import LayoutAdmin from '../../../Layouts/Admin.vue';
//import component pagination
import Pagination from '../../../Components/Pagination.vue';

//import Heade and Link from Inertia
import {
    Head,
    Link,
} from '@inertiajs/vue3';

//import ref from vue
import {
    ref
} from 'vue';

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
        Pagination,
    },

    //props
    props: {
        errors: Object,
        datas: Object,
    },

    //inisialisasi composition API
    setup(props) {

        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        //define method search
        const handleSearch = () => {
            router.get('/admin/question-categories', {
                q: search.value,
            });
        }

        //define method destroy
        const destroy = (id) => {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Soal di dalam kelompok ini tidak akan ikut terhapus, tetapi jadi tanpa kelompok.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    router.delete(`/admin/question-categories/${id}`);
                }
            });
        }

        //return
        return {
            search,
            handleSearch,
            destroy,
        }
    }
}

</script>
