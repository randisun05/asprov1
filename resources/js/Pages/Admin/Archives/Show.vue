                                            <template>

                                                <Head>
                                                    <title>Hubungi Aspro</title>
                                                </Head>

                                                <div class="container-fluid padding px-5">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <Link href="/admin/archives" class="btn btn-md btn-primary border-0 shadow mb-3"
                                                                type="button">
                                                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                                            Kembali</Link>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="card border-0 shadow">
                                                                <div class="card-body">
                                                                    <div class="row">
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
                                                                                    <td>
                                                                                        {{
                                                                                            archive.status == 1 ? 'Belum ditindak lanjuti' :
                                                                                                archive.status == 2 ? 'Dalam proses' :
                                                                                                    archive.status == 3 ? 'Selesai' :
                                                                                                        archive.status == 4 ? 'Telah didisposisi ke ' +
                                                                                                            archive.user_names :
                                                                                                            'Tidak ditindak lanjuti'
                                                                                        }}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Kontak</th>
                                                                                    <td>{{ archive.contact }}</td>

                                                                                    <th>Title</th>
                                                                                    <td>{{ archive.title }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>Detail</th>
                                                                                    <td>{{ archive.detail }}</td>
                                                                                    <th>Document</th>
                                                                                    <td><a class="btn btn-sm btn-primary border-0 me-1"
                                                                                            data-fancybox=""
                                                                                            :href="getDocumentUrl(archive.document)"><i
                                                                                                class="fa fa-eye"></i>View</a></td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <h5><i class="fa fa-bookmark"></i> Disposisi</h5>
                                                                            <hr>
                                                                            Disposisi ke
                                                                            <div>
                                                                                <table v-if="users && users.length > 0" class="table table-borderless" style="width: 50%;">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <th class="border-0 rounded-start"
                                                                                                style="width:5%">
                                                                                                <input type="checkbox"
                                                                                                    v-model="form.allSelected"
                                                                                                    @change="selectAll" />
                                                                                                </th>                                            <th class="border-0 rounded-start">
                                                                                                Check All
                                                                                            </th>
                                                                                        </tr>
                                                                                        <tr v-for="(user, index) in users" :key="index">
                                                                                            <td>
                                                                                                <input type="checkbox"
                                                                                                    v-model="form.user_id" :id="user.id"
                                                                                                    :value="user.id"
                                                                                                    :checked="form.allSelected" />
                                                                                            </td>
                                                                                            <td class="text-left">
                                                                                                <label class="form-check-label"
                                                                                                    :for="'user-' + user.id">
                                                                                                    {{ user.name }}
                                                                                                </label>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>

                                                                                <input type="text" v-model="searchQuery"
                                                                                    placeholder="Cari nama..." class="form-control"
                                                                                    @input="searchRefs" />
                                                                                <div v-if="searchQuery && filteredRefs.length" class="mt-2">
                                                                                    <ul class="list-group">
                                                                                        <li v-for="ref in filteredRefs" :key="ref.id"
                                                                                            class="list-group-item" @click="addUser(ref)">
                                                                                            {{ ref.name }}
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                                <div class="mt-2">
                                                                                    <span v-for="(user, index) in form.user_id" :key="index"
                                                                                        class="badge bg-primary me-2">
                                                                                        {{ getUserById(user).name }}
                                                                                        <span @click="removeUser(user)"
                                                                                            class="ms-1 text-white"
                                                                                            style="cursor:pointer">&times;</span>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                            <button @click="submitDisposisi(archive.id)"
                                                                                class="btn btn-primary mt-3">Disposisi</button>
                                                                        </div>


                                                                        <div class="col-3">
                                                                            <h5><i class="fa fa-bookmark"></i>Isi Disposisi</h5>
                                                                            <hr>
                                                                            <div class="row" style="font-size: 13px;">
                                                                                <div class="col-6">
                                                                                    <div>
                                                                                        <input type="checkbox" id="status1" value="Selesaikan/Tindaklanjuti" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status1">Selesaikan/Tindaklanjuti</label>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox" id="status2" value="Siapkan bahan" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status2">Siapkan bahan</label>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox" id="status3" value="Buat konsep jawaban" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status3">Buat konsep jawaban</label>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox" id="status4" value="Koodinasikan" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status4">Koodinasikan</label>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox" id="status5" value="Pelajari/Kaji" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status5">Pelajari/Kaji</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <div>
                                                                                        <input type="checkbox" id="status6" value="Untuk diketahui" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status6">Untuk diketahui</label>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox" id="status7" value="Wakil/Hadiri" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status7">Wakil/Hadiri</label>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox" id="status8" value="Untuk menjadi perhatian" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status8">Untuk menjadi perhatian</label>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox" id="status9" value="Pantau" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status9">Pantau</label>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox" id="status10" value="Temui Saya" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status10">Temui Saya</label>
                                                                                    </div>
                                                                                    <div>
                                                                                        <input type="checkbox" id="status11" value="File/Simpan" @change="updateIsi($event)">
                                                                                        <label class="form-check-label" for="status11">File/Simpan</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>                                            </div>
                                                                        <div class="col-6">
                                                                            <h5><i class="fa fa-bookmark"></i>Histori Disposisi</h5>
                                                                            <hr>
                                                                            <table class="table table-borderless">
                                                                                <thead class="thead-dark">
                                                                                    <tr class="border-0 text-center">
                                                                                        <th class="border-0 rounded-start" style="width:5%">
                                                                                            No.</th>
                                                                                        <th class="border-0">Disposisi</th>
                                                                                        <th class="border-0">Status</th>
                                                                                        <th class="border-0">Aksi</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr v-for="(data, index) in datas" :key="index">
                                                                                        <td class="fw-bold text-center">{{ ++index }}</td>
                                                                                        <td class="text-center">
                                                                                            {{ data.user.name }}
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            {{
                                                                                                data.status == 1 ? 'Belum ditindaklanjuti' :
                                                                                                    data.status == 2 ? 'Dalam proses' :
                                                                                                        data.status == 3 ? 'Selesai' :
                                                                                                            data.status == 4 ? 'Telah didisposisi ke ' +
                                                                                                                data.user_names :
                                                                                            'Tidak ditindak lanjuti'
                                                                                            }}
                                                                                        </td>

                                                                                        <td v-if="data.status != '1'" class="text-center">
                                                                                            <button class="btn btn-sm btn-primary"
                                                                                                @click="viewDetail(data)">
                                                                                                <i class="fa fa-eye"></i>
                                                                                            </button>

                                                                                            <!-- Modal -->
                                                                                            <div class="modal fade" id="detailModal"
                                                                                                tabindex="-1"
                                                                                                aria-labelledby="detailModalLabel"
                                                                                                aria-hidden="true">
                                                                                                <div
                                                                                                    class="modal-dialog modal-dialog-centered">
                                                                                                    <div class="modal-content">
                                                                                                        <div class="modal-header">
                                                                                                            <h5 class="modal-title"
                                                                                                                id="detailModalLabel">Keterangan</h5>
                                                                                                            <button type="button"
                                                                                                                class="btn-close"
                                                                                                                data-bs-dismiss="modal"
                                                                                                                aria-label="Close"></button>
                                                                                                        </div>
                                                                                                        <div class="modal-body">
                                                                                                            <p>{{ selectedData &&
                                                                                                                selectedData.detail ?
                                                                                                                selectedData.detail : 'tidak diberikan keterangan' }}</p>
                                                                                                        </div>
                                                                                                        <div class="modal-footer">
                                                                                                            <button type="button"
                                                                                                                class="btn btn-secondary"
                                                                                                                data-bs-dismiss="modal">Close</button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>

                                                                </div>

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
                    import { reactive, ref, computed } from 'vue';

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
                            refs: Array
                        },

                        //inisialisasi composition API
                        setup(props) {

                            //define form with reactive
                            const form = reactive({
                                archive_id: props.archive.id,
                                user_id: [],
                                allSelected: false,
                                isi: [], // Initialize isi as an empty array
                            });


                            const searchQuery = ref('');
                            const filteredRefs = computed(() => {
                                return props.refs.filter(ref => ref.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
                            });
                            const selectAll = () => {
                                form.user_id = form.allSelected ? props.users.map(user => user.id) : [];
                            };
                            const addUser = (user) => {
                                if (!form.user_id.includes(user.id)) {
                                    form.user_id.push(user.id);
                                }
                                searchQuery.value = '';
                            };
                            const removeUser = (id) => {
                                form.user_id = form.user_id.filter(userId => userId !== id);
                            };
                            const getUserById = (id) => {
                                return props.refs.find(ref => ref.id === id) || { name: 'Unknown' };
                            };


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
                                                                router.post(`/admin/archives/disposition/${id}`, {
                                                                    user_id: form.user_id,
                                                                    isi: form.isi
                                                                });

                                                                Swal.fire({
                                                                    title: 'Success!',
                                                                    text: 'Status Sent!.',
                                                                    icon: 'success',
                                                                    timer: 2000,
                                                                    showConfirmButton: false,
                                                                });
                                                            }
                                                        })
                                                }

                            const selectedData = ref(null);

                            const viewDetail = (data) => {
                                selectedData.value = data;
                                const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                                modal.show();
                            };

                            // Method to get the URL of the document
                            const getDocumentUrl = (documentName) => {
                                return `/storage/${documentName}`;
                            }

                            // Method to update the isi disposisi
                            const updateIsi = (event) => {
                                const checkbox = event.target;
                                if (checkbox.checked) {
                                    form.isi.push(checkbox.value);
                                } else {
                                    form.isi = form.isi.filter(value => value !== checkbox.value);
                                }
                            }


                            //return
                            return {
                                form,
                                submitDisposisi,
                                selectAll,
                                searchQuery,
                                filteredRefs,
                                addUser,
                                removeUser,
                                getUserById,
                                viewDetail,
                                selectedData,
                                getDocumentUrl,
                                updateIsi,
                            }

                        }



                    }


                    </script>

                    <style></style>
