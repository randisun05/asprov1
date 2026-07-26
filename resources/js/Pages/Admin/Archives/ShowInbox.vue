                                                    <template>

                                                        <Head>
                                                            <title>Hubungi Aspro</title>
                                                        </Head>

                                                        <div class="container-fluid padding px-5">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <Link href="/admin/archives/inbox"
                                                                        class="btn btn-md btn-primary border-0 shadow mb-3" type="button">
                                                                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                                                    Kembali</Link>
                                                                </div>
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
                                                                                        <td>{{ archive.archive.nip }}</td>

                                                                                        <th>Jabatan</th>
                                                                                        <td>{{ archive.archive.position }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th>Nama</th>
                                                                                        <td>{{ archive.archive.name }}</td>
                                                                                        <th>Instansi</th>
                                                                                        <td>{{ archive.archive.agency }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th>Email</th>
                                                                                        <td>{{ archive.archive.email }}</td>
                                                                                        <th>Status</th>
                                                                                        <td>
                                                                                            {{ archive.archive.status == 1 ? 'Belum ditindaklanjuti' :
                                                                                            archive.archive.status == 2 ? 'Dalam proses' :
                                                                                            archive.archive.status == 3 ? 'Sudah selesai' :
                                                                                            archive.archive.status == 4 ? 'Telah didisposisi oleh administrator' : 'Tidak ditindak lanjuti' }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th>Kontak</th>
                                                                                        <td>{{ archive.archive.contact }}</td>

                                                                                        <th>Title</th>
                                                                                        <td>{{ archive.archive.title }}</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <th>Detail</th>
                                                                                        <td>{{ archive.archive.detail }}</td>
                                                                                        <th>Document</th>
                                                                                        <td><a class="btn btn-sm btn-primary border-0 me-1"
                                                                                                data-fancybox=""
                                                                                                :href="getDocumentUrl(archive.document)"><i
                                                                                                    class="fa fa-eye"></i>View</a></td>
                                                                                    </tr>

                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        <div class="row py-5">
                                                                            <h5><i class="fa fa-bookmark"></i>Tindaklanjuti</h5>
                                                                            <hr>
                                                                            <div class="col-md-3">
                                                                                <label for="status">Status</label>
                                                                                <select v-model="form.status" class="form-control" id="status">
                                                                                    <option selected disabled>Pilih Status</option>
                                                                                    <option value="1" disabled>Belum Ditindak lanjuti</option>
                                                                                    <option value="3">Selesai</option>
                                                                                    <option value="5">Tidak DItindaklanjuti</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-4 col-sm-4">
                                                                                <label for="detail">Isi Disposisi</label>
                                                                                <div>
                                                                                    <div>
                                                                                        <span v-for="(item, index) in archive.isi.split(',')" :key="index" class="badge bg-primary me-1">
                                                                                            {{ item.trim() }}
                                                                                        </span>                                                    </div>
                                                                                </div>                                                   </div>
                                                                            <div class="col-md-5 col-sm-5">
                                                                                <label for="detail">Catatan dan Bukti Dukung</label>
                                                                                <textarea v-model="form.detail" class="form-control" id="detail"
                                                                                    rows="2"></textarea>
                                                                            </div>
                                                                            <div class="text-center mt-2">
                                                                                <button class="button btn-primary" style="width: 300px;"
                                                                                    @click="submitForm(archive.id)">Simpan</button>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">

                                                                            <!-- //kolom disposisi -->
                                                                            <!-- <div class="col-md-4">
                                                                                <h5><i class="fa fa-bookmark"></i> Disposisi</h5>
                                                                                <hr>
                                                                                Disposisi ke
                                                                                <div>
                                                                                    <table class="table table-borderless" style="width: 50%;">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <th class="border-0 rounded-start"
                                                                                                    style="width:5%">
                                                                                                    <input type="checkbox"
                                                                                                        v-model="form.allSelected"
                                                                                                        @change="selectAll" />
                                                                                                </th>
                                                                                                <th class="border-0 rounded-start">
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
                                                                            </div> -->
                                                                            <div class="col-md-8">
                                                                                <h5><i class="fa fa-bookmark"></i> Histori Disposisi</h5>
                                                                                <hr>
                                                                                <table class="table table-borderless">
                                                                                    <thead class="thead-dark">
                                                                                        <tr class="border-0 text-center">
                                                                                            <th class="border-0 rounded-start" style="width:5%">
                                                                                                No.</th>
                                                                                            <th class="border-0">Disposisi</th>
                                                                                            <th class="border-0">Status</th>
                                                                                            <th class="border-0">Update</th>
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
                                                                                                {{ data.status == 1 ? 'Belum ditindak lanjuti' :
                                                                                                'telah didisposisi ke '
                                                                                                }}
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                {{ new
                                                                                                    Date(data.updated_at).toLocaleString('id-ID', {
                                                                                                        day: '2-digit',
                                                                                                        month: '2-digit', year: '2-digit', hour:
                                                                                                            '2-digit', minute:
                                                                                                '2-digit'
                                                                                                })
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
                                                                                                                    id="detailModalLabel">Detail
                                                                                                                    Disposisi</h5>
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
                        } from '@inertiajs/inertia-vue3';

                        //import reactive from vue
                        import { reactive, computed, ref } from 'vue';

                        //import inertia adapter
                        import { Inertia } from '@inertiajs/inertia';

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
                                    status: props.archive.status,
                                    detail: props.archive.detail
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

                                const submitForm = (id) => {
                                    Swal.fire({
                                        title: 'Apakah Anda yakin?',
                                        text: "Anda akan mengupdate disposisi ini!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Yes, Approve it!'
                                    })
                                        .then((result) => {
                                            if (result.isConfirmed) {

                                                Inertia.post(`/admin/archives/inbox/${id}/update`,
                                                    {
                                                        'detail': form.detail,
                                                        'status': form.status
                                                    });

                                                Swal.fire({
                                                    title: 'Success!',
                                                    text: 'Status Approved!.',
                                                    icon: 'success',
                                                    timer: 2000,
                                                    showConfirmButton: false,
                                                });
                                            }
                                        })
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
                                                Inertia.post(`/admin/archives/disposition/inbox/${id}`, { user_id: form.user_id });

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

                                //return
                                return {
                                    form,
                                    submitForm,
                                    submitDisposisi,
                                    selectAll,
                                    searchQuery,
                                    filteredRefs,
                                    addUser,
                                    removeUser,
                                    getUserById,
                                    selectedData,
                                    viewDetail,
                                    getDocumentUrl
                                }

                            }



                        }


                        </script>

                        <style></style>
