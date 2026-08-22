                <template>

                    <Head>
                        <title>Administrator</title>
                    </Head>
                    <div class="container-fluid padding px-5">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                         <Link :href="`/admin/questions/create`" class="btn btn-md btn-primary border-0 shadow me-2" type="button"><i class="fa fa-plus-circle"></i> Tambah</Link>
                                        <Link :href="`/admin/questions/import`" class="btn btn-md btn-success border-0 shadow text-white" type="button"><i class="fa fa-file-excel"></i> Import</Link>
                                    </div>
                                    <div class="col-md-4 col-12 mb-2">
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
                                    <div class="col-md-4 col-12 mb-2">
                                        <select class="form-control border-0 shadow" v-model="categoryId" @change="handleSearch">
                                            <option value="">Semua Kelompok Soal</option>
                                            <option :value="category.id" v-for="(category, index) in categories" :key="index">
                                                {{ category.title }}
                                            </option>
                                        </select>
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
                                                        <th class="border-0">Soal</th>
                                                        <th class="border-0 rounded-end" style="width:15%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <div class="mt-2"></div>
                                                <tbody>
                                                    <tr v-for="(data, index) in datas.data" :key="index">
                                                        <td class="fw-bold text-center">{{ ++index + (datas.current_page - 1) *
                                                            datas.per_page }}</td>
                                                       
                                                        <td>
    <span class="badge bg-secondary mb-1">{{ data.category ? data.category.title : 'Tanpa Kelompok' }}</span>
    <div v-html="data.text"></div>

    <ol type="A" class="ps-3">
        <li v-html="data.a" :class="{ 'text-stabilo': data.answer == '1' }"></li>
        <li v-html="data.b" :class="{ 'text-stabilo': data.answer == '2' }"></li>
        <li v-html="data.c" :class="{ 'text-stabilo': data.answer == '3' }"></li>
        <li v-html="data.d" :class="{ 'text-stabilo': data.answer == '4' }"></li>
        <li v-html="data.e" :class="{ 'text-stabilo': data.answer == '5' }"></li>
    </ol>
</td>
                                                        <td class="text-center">
                                                           <Link :href="`/admin/questions/${data.id}/edit`" class="btn btn-sm btn-info border-0 shadow me-2"
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

                import axios from "axios";


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
                        categories: Array,
                        filters: Object,

                    },




                    //inisialisasi composition API
                    setup(props) {


                        //define state search
                        const search = ref(props.filters?.q || '');
                        const categoryId = ref(props.filters?.category_id || '');

                        //define method search
                        const handleSearch = () => {
                            router.get('/admin/questions', {

                                //send params "q" with value from state "search"
                                q: search.value,
                                category_id: categoryId.value,
                            });
                        }

                        //define method destroy
                        const destroy = (id) => {
                            Swal.fire({
                                title: 'Yakin ingin menghapus soal ini?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, hapus',
                                cancelButtonText: 'Batal',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    router.delete(`/admin/questions/${id}`);
                                }
                            });
                        }


                        //return
                        return {
                            search,
                            categoryId,
                            handleSearch,
                            destroy,


                        }
                    }
                }

                </script>

                <style scoped>
                ol li {
    white-space: nowrap;
}
    .text-stabilo {
    background-color: #90EE90; /* hijau stabilo */
    font-weight: bold;
    display: inline;
}
                </style>
