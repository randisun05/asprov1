        <template>

            <Head>
                <title>Hubungi Aspro</title>
            </Head>
            <div class="container-fluid padding px-5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-1 col-12 mb-2">
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
                                                <th class="border-0">Pengirim</th>
                                                <th class="border-0">Title</th>
                                                <th class="border-0">Detail</th>
                                                <th class="border-0">Status</th>
                                                <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <div class="mt-2"></div>
                                        <tbody>

                                            <tr v-for="(archive, index) in archives.data" :key="index"
                                                :class="{ 'bg-success': archive.status == 1, 'bg-opacity-150': archive.status == 1 }">
                                                <td class="fw-bold text-center">{{ ++index + (archives.current_page - 1) *
                                                    archives.per_page }}</td>
                                                <td>
                                                    <div>{{ archive.archive.nip }}</div>
                                                    <div>{{ archive.archive.name }}</div>
                                                    <div>{{ archive.archive.agency }}</div>
                                                </td>
                                                <td>{{ archive.archive.title }}</td>
                                                <td>{{ archive.archive.detail ? (archive.archive.detail.length > 100 ?
                                                    archive.archive.detail.substring(0, 100) + '...' : archive.archive.detail) :
                                                    '' }}</td>
                                                <td>
                                                    {{
                                                        archive.status == 1 ? 'Belum ditindak lanjuti' :
                                                            archive.status == 2 ? 'Dalam proses' :
                                                                archive.status == 3 ? 'Selesai' :
                                                                    archive.status == 4 ? 'Telah didisposisi ke ' + archive.user_names :
                                                                        'Tidak ditindak lanjuti'
                                                    }}

                                                </td>
                                                <td class="text-center">
                                                    <Link :href="`/admin/archives/inbox/${archive.id}/show`"
                                                        class="btn btn-sm btn-primary border-0 shadow me-2" type="button"
                                                        title="action"><i class="fa fa-pencil"></i></Link>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <Pagination :links="archives.links" align="end" />
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
            Link
        } from '@inertiajs/vue3';

        //import ref from vue
        import {
            ref, reactive
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
                Pagination
            },

            //props
            props: {
                errors: Object,
                archives: Object,

            },



            //inisialisasi composition API
            setup() {


                //define state search
                const search = ref('' || (new URL(document.location)).searchParams.get('q'));

                //define method search
                const handleSearch = () => {
                    router.get('/admin/archives', {

                        //send params "q" with value from state "search"
                        q: search.value,
                    });
                }


                //return
                return {
                    search,
                    handleSearch,


                }
            }
        }

        </script>

        <style></style>
