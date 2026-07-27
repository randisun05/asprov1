                                        <template>

                                            <!-- Our Blogs -->
                                            <section id="dashboard" class="container-fluid padding px-5">
                                                <div class="card shadow py-5">
                                                    <h3 class="text-center">Management Website</h3>

                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <ul class="nav nav-pills flex-column" id="item-list">
                                                                <li class="nav-item" v-for="tab in tabs" :key="tab.value">
                                                                    <a class="nav-link" @click="setActiveTab(tab.value)"
                                                                        style="cursor: pointer;">
                                                                        {{ tab.label }}
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <div class="col-md-8 mt-4">
                                                            <!-- <button type="button" class="btn btn-primary" @click="ModalCreate()"
                                                                v-show="activeTab === 'popup' || activeTab === 'peraturan' || activeTab === 'dataanggota' || activeTab === 'strukturorganisasi' || activeTab === 'proker' || activeTab === 'faq'">
                                                                <i class="fa fa-plus-circle"></i>Tambah
                                                            </button> -->

                                                            <button type="button" class="btn btn-primary"
                                                                @click="ModalCreate()">
                                                                <i class="fa fa-plus-circle"></i>Tambah
                                                            </button>

                                                            <span>
                                                                <h3 class="text-center">Daftar {{ Active }}</h3>
                                                            </span>
                                                            <!-- ACTIVETAB = {{ activeTab }}
                                                            {{ filteredData }} -->
                                                            <table
                                                                class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                                                <thead class="thead-dark">
                                                                    <tr class="border-0 text-center">
                                                                        <th class="border-0 rounded-start" style="width:5%">
                                                                            No.</th>
                                                                        <th class="border-0">Title</th>
                                                                        <th class="border-0" style="width:12%">Status</th>
                                                                        <th class="border-0 rounded-end" style="width:15%">
                                                                            Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <div class="mt-2"></div>
                                                                <tbody>
                                                                    <tr v-for="(data, index) in datas.data" :key="index">
                                                                        <td class="fw-bold text-center">{{ ++index +
                                                                            (datas.current_page - 1) *
                                                                            datas.per_page
                                                                            }}</td>
                                                                        <td>{{ data.sub }}</td>
                                                                        <td class="text-center">
                                                                            <button type="button" class="btn btn-sm"
                                                                                :class="data.status == 1 ? 'btn-success' : 'btn-danger'"
                                                                                @click="data.status == 1 ? nonactive(data) : active(data)">
                                                                                {{ data.status == 1 ? 'Active' : 'Nonactive'
                                                                                }}
                                                                            </button>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <a class="btn btn-sm btn-primary border-0 ms-2"
                                                                                data-fancybox=""
                                                                                :href="showImage(data.image)"><i
                                                                                    class="fa fa-eye"></i></a>
                                                                            <a v-show="data.document !== '' && data.document !== '-'"
                                                                                :href="showDoc(data.document)"
                                                                                target="_blank"
                                                                                class="btn btn-sm btn-warning border-0 ms-2"><i
                                                                                    class="fa fa-file-pdf-o"></i></a>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-success border-0 ms-2"
                                                                                @click="ModalEdit(data)">
                                                                                <i class="fa fa-pencil"></i>

                                                                            </button>
                                                                            <button @click.prevent="destroy(data.id)"
                                                                                class="btn btn-sm btn-danger border-0 ms-2"
                                                                                v-show="activeTab === 'popup' || activeTab === 'peraturan' || activeTab === 'dataanggota' || activeTab === 'strukturorganisasi' || activeTab === 'proker' || activeTab === 'faq'"><i
                                                                                    class="fa fa-trash"
                                                                                    title="hapus"></i></button>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <Pagination :links="datas.links" align="end" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ModalCreate -->
                                                <div class="modal fade" id="ModalCreate" tabindex="-1"
                                                    aria-labelledby="popupModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="popupModalLabel">Create</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="program-description" id="program-description-1">

                                                                    <div class="row py-4 ms-5">
                                                                        <!-- <div class="col-md-11">
                                                                                <span class="text-black">Item</span>
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <select class="form-select" v-model="form.item">
                                                                                        <option value="" disabled selected>Pilih salah satu opsi</option>
                                                                                        <option value="popup">Pop Up</option>
                                                                                        <option value="siapakita">Siapa Kita ?</option>
                                                                                        <option value="ketuaumum">Ketua Umum</option>
                                                                                        <option value="strukturorganisasi">Struktur Organisasi</option>
                                                                                        <option value="visimisi">Visi Misi</option>
                                                                                        <option value="sejarah">Sejarah</option>
                                                                                        <option value="peraturan">Peraturan</option>
                                                                                        <option value="proker">Program Kerja</option>
                                                                                        <option value="dataanggota">Data Anggota</option>
                                                                                        <option value="faq">FAQ</option>
                                                                                        <option value="kontak">Kontak Kami</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div v-if="errors.item" class="alert alert-danger mt-2">{{ errors.item }}</div>
                                                                            </div> -->
                                                                        <div class="col-md-11">
                                                                            <span class="text-black">Item</span>
                                                                            <div class="form-group mt-1 mb-4">
                                                                                <input type="text" class="form-control"
                                                                                    v-model="tab" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-11">
                                                                            <span class="text-black">Sub</span>
                                                                            <div class="form-group mt-1 mb-4">
                                                                                <input type="text" class="form-control"
                                                                                    placeholder="Masukan Sub"
                                                                                    v-model="form.sub">
                                                                            </div>
                                                                            <div v-if="errors.sub"
                                                                                class="alert alert-danger mt-2">{{
                                                                                    errors.sub
                                                                                }}</div>
                                                                        </div>

                                                                        <div class="col-md-11" v-show="tab == 'proker'">
                                                                            <span class="text-black">Sub</span>
                                                                            <div class="form-group mt-1 mb-4">
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <select class="form-select"
                                                                                        v-model="form.item">
                                                                                        <option value="" disabled selected>
                                                                                            Pilih salah satu opsi
                                                                                        </option>
                                                                                        <option value="humas">Bidang
                                                                                            Hubungan Masyarakat
                                                                                        </option>
                                                                                        <option value="hukum">Bidang Hukum
                                                                                            dan Advokasi</option>
                                                                                        <option value="anggota">Bidang
                                                                                            Keanggotaan</option>
                                                                                        <option value="pengembangan">Bidang
                                                                                            Pengembangan
                                                                                            Kapasitas Insani
                                                                                        </option>
                                                                                        <option value="pendanaan">Bidang
                                                                                            Sumber Pendanaan
                                                                                            Organisasi</option>

                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div v-if="errors.sub"
                                                                                class="alert alert-danger mt-2">{{
                                                                                    errors.sub
                                                                                }}</div>
                                                                        </div>

                                                                        <div class="col-md-11"
                                                                            v-show="activeTab == 'proker' || tab == 'faq'">
                                                                            <span class="text-black">Subitem</span>
                                                                            <div class="form-group mt-1 mb-4">
                                                                                <input type="text" class="form-control"
                                                                                    placeholder="Masukan Sub Item"
                                                                                    v-model="form.subitem">
                                                                            </div>
                                                                            <div v-if="errors.subitem"
                                                                                class="alert alert-danger mt-2">{{
                                                                                    errors.subitem }}
                                                                            </div>
                                                                        </div>


                                                                        <div class="col-md-11" v-show="tab != 'popup'">
                                                                            <span class="text-black">Body</span>

                                                                            <div class="form-group mt-1 mb-4">
                                                                                <Editor
                                                                                    api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                                                                    v-model="form.body"
                                                                                    style="height: 500px;" :init="{
                                                                                        menubar: false,
                                                                                        plugins: 'lists link image emoticons media',
                                                                                        toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons | media'
                                                                                    }" />
                                                                            </div>
                                                                            <div v-if="errors.body"
                                                                                class="alert alert-danger mt-2">{{
                                                                                    errors.body }}</div>
                                                                        </div>


                                                                        <div class="col-md-11"
                                                                            v-show="tab == 'popup' || tab == 'peraturan' || tab == 'dataanggota'">
                                                                            <span class="text-black">Link</span>
                                                                            <div class="form-group mt-1 mb-4">
                                                                                <input type="text" class="form-control"
                                                                                    placeholder="Masukan Link"
                                                                                    v-model="form.link">
                                                                            </div>
                                                                            <div v-if="errors.link"
                                                                                class="alert alert-danger mt-2">{{
                                                                                    errors.link }}</div>
                                                                        </div>

                                                                        <div class="col-md-5"
                                                                            v-show="tab == 'proker' || tab == 'faq'">
                                                                            <span class="text-black">Posisi</span>
                                                                            <div class="form-group mt-1 mb-4">
                                                                                <select class="form-select"
                                                                                    v-model="form.button">
                                                                                    <option value="" disabled selected>Pilih
                                                                                        salah satu opsi
                                                                                    </option>
                                                                                    <option value="1">1</option>
                                                                                    <option value="2">2</option>
                                                                                    <option value="3">3</option>
                                                                                </select>
                                                                            </div>
                                                                            <div v-if="errors.position"
                                                                                class="alert alert-danger mt-2">{{
                                                                                    errors.position }}
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-5" v-show="tab == 'popup'">
                                                                            <span class="text-black">Button</span>
                                                                            <div class="form-group mt-1 mb-4">
                                                                                <select class="form-select"
                                                                                    v-model="form.button">
                                                                                    <option value="" disabled selected>Pilih
                                                                                        salah satu opsi
                                                                                    </option>
                                                                                    <option value="1">Yes</option>
                                                                                    <option value="0">No</option>
                                                                                </select>
                                                                            </div>
                                                                            <div v-if="errors.button"
                                                                                class="alert alert-danger mt-2">{{
                                                                                    errors.button }}</div>
                                                                        </div>

                                                                        <div class="col-md-5" v-show="tab === ''">
                                                                            <span class="text-black">Keterangan</span>
                                                                            <div class="form-group mt-1 mb-4">
                                                                                <Editor
                                                                                    api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                                                                    v-model="form.desc"
                                                                                    style="height: 500px;" :init="{
                                                                                        menubar: false,
                                                                                        plugins: 'lists link image emoticons media',
                                                                                        toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons | media'
                                                                                    }" />
                                                                            </div>
                                                                            <div v-if="errors.desc"
                                                                                class="alert alert-danger mt-2">{{
                                                                                    errors.desc }}</div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <span class="text-black">Gambar/Foto</span>
                                                                                <div class="form-group mt-1">
                                                                                    <input type="file" class="form-control"
                                                                                        placeholder="Masukan Gambar/Foto"
                                                                                        @change="updateImage"
                                                                                        accept=".jpg, .jpeg, .png, .svg, .gif">
                                                                                </div>
                                                                                <div v-if="errors.picture"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.picture
                                                                                    }}</div>
                                                                                <div v-if="errors[0]"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors[0] }}</div>
                                                                            </div>

                                                                            <div class="col-md-5"
                                                                                v-show="tab === 'peraturan' || tab === 'dataanggota'">
                                                                                <span class="text-black">Dokumen</span>
                                                                                <div class="form-group mt-1">
                                                                                    <input type="file" class="form-control"
                                                                                        placeholder="Masukan Dokumen"
                                                                                        @change="updateDocument">
                                                                                </div>
                                                                                <div v-if="errors.document"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.document }}</div>
                                                                                <div v-if="errors[0]"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors[0] }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row d-flex justify-content-center">
                                                                        <div class="col-md-4">
                                                                            <button type="submit"
                                                                                class="btn btn-md btn-primary border-0 shadow me-2"
                                                                                @click="store"
                                                                                data-bs-dismiss="modal">Simpan</button>
                                                                            <button type="reset"
                                                                                class="btn btn-md btn-warning border-0 shadow">Reset</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- ModalEdit -->
                                                <div class="modal fade" id="ModalEdit" tabindex="-1"
                                                    aria-labelledby="popupModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="popupModalLabel">Edit</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="program-description" id="program-description-1">
                                                                    <div class="tab-pane fade show active" id="modaledit">
                                                                        <div class="row py-4 ms-5">
                                                                            <div class="col-md-11">
                                                                                <span class="text-black">Item</span>
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <input type="text" class="form-control"
                                                                                        placeholder="Masukan Item"
                                                                                        v-model="form.item" disabled>

                                                                                </div>
                                                                                <div v-if="errors.sub"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.item }}</div>
                                                                            </div>

                                                                            <div class="col-md-11"
                                                                                v-show="tab == 'proker' || tab == 'faq' || tab == 'popup'">
                                                                                <span class="text-black">Sub</span>
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <input type="text" class="form-control"
                                                                                        placeholder="Masukan Sub"
                                                                                        v-model="form.sub">
                                                                                </div>
                                                                                <div v-if="errors.sub"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.sub }}</div>
                                                                            </div>

                                                                            <div class="col-md-11"
                                                                                v-show="tab == 'proker' || tab == 'faq'">
                                                                                <span class="text-black">Subitem</span>
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <input type="text" class="form-control"
                                                                                        placeholder="Masukan Sub Item"
                                                                                        v-model="form.subitem">
                                                                                </div>
                                                                                <div v-if="errors.subitem"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.subitem }}
                                                                                </div>
                                                                            </div>


                                                                            <div class="col-md-11" v-show="tab != 'popup'">
                                                                                <span class="text-black">Body</span>
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <Editor
                                                                                        api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                                                                        v-model="form.body"
                                                                                        style="height: 500px;" :init="{
                                                                                            menubar: false,
                                                                                            plugins: 'lists link image emoticons media',
                                                                                            toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons | media'
                                                                                        }" />
                                                                                </div>
                                                                                <div v-if="errors.body"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.body }}</div>
                                                                            </div>


                                                                            <div class="col-md-11"
                                                                                v-show="tab == 'popup' || tab == 'peraturan' || tab == 'dataanggota'">
                                                                                <span class="text-black">Link</span>
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <input type="text" class="form-control"
                                                                                        placeholder="Masukan Link"
                                                                                        v-model="form.link">
                                                                                </div>
                                                                                <div v-if="errors.body"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.link }}</div>
                                                                            </div>

                                                                            <div class="col-md-5"
                                                                                v-show="tab == 'proker' || tab == 'faq'">
                                                                                <span class="text-black">Posisi</span>
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <select class="form-select"
                                                                                        v-model="form.button">
                                                                                        <option value="" disabled selected>
                                                                                            Pilih salah satu opsi
                                                                                        </option>
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div v-if="errors.position"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.position
                                                                                    }}</div>
                                                                            </div>

                                                                            <div class="col-md-5" v-show="tab === 'popup'">
                                                                                <span class="text-black">Button</span>
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <select class="form-select"
                                                                                        v-model="form.button">
                                                                                        <option value="" disabled selected>
                                                                                            Pilih salah satu opsi
                                                                                        </option>
                                                                                        <option value="1">Yes</option>
                                                                                        <option value="0">No</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div v-if="errors.button"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.button }}
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-5" v-show="tab === ''">
                                                                                <span class="text-black">Keterangan</span>
                                                                                <div class="form-group mt-1 mb-4">
                                                                                    <Editor
                                                                                        api-key="1r5zhfhbvfala2snldia4kj7eub4vbev5i6i4mnf9r8smbsb"
                                                                                        v-model="form.desc"
                                                                                        style="height: 500px;" :init="{
                                                                                            menubar: false,
                                                                                            plugins: 'lists link image emoticons media',
                                                                                            toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons | media'
                                                                                        }" />
                                                                                </div>
                                                                                <div v-if="errors.desc"
                                                                                    class="alert alert-danger mt-2">{{
                                                                                        errors.desc }}</div>
                                                                            </div>



                                                                            <div class="row">
                                                                                <div class="col-md-5">
                                                                                    <span
                                                                                        class="text-black">Gambar/Foto</span>
                                                                                    <div class="form-group mt-1">
                                                                                        <input type="file"
                                                                                            class="form-control"
                                                                                            placeholder="Masukan Gambar/Foto"
                                                                                            @change="updateImage"
                                                                                            accept=".jpg, .jpeg, .png, .svg, .gif">
                                                                                    </div>
                                                                                    <div v-if="errors.image"
                                                                                        class="alert alert-danger mt-2">{{
                                                                                            errors.picture
                                                                                        }}</div>
                                                                                    <div v-if="errors[0]"
                                                                                        class="alert alert-danger mt-2">{{
                                                                                            errors[0] }}</div>
                                                                                </div>

                                                                                <div class="col-md-5"
                                                                                    v-show="tab === 'peraturan' || tab === 'dataanggota'">
                                                                                    <span class="text-black">Dokumen</span>
                                                                                    <div class="form-group mt-1">
                                                                                        <input type="file"
                                                                                            class="form-control"
                                                                                            placeholder="Masukan Dokumen"
                                                                                            @change="updateDocument">
                                                                                    </div>
                                                                                    <div v-if="errors.document"
                                                                                        class="alert alert-danger mt-2">
                                                                                        {{
                                                                                            errors.document }}</div>
                                                                                    <div v-if="errors[0]"
                                                                                        class="alert alert-danger mt-2">{{
                                                                                            errors[0] }}</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row d-flex justify-content-center">
                                                                            <div class="col-md-2">
                                                                                <button type="submit"
                                                                                    class="btn btn-md btn-primary border-0 shadow me-2"
                                                                                    data-bs-dismiss="modal"
                                                                                    @click="update">Simpan</button>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <!--Our Blogs Ends-->

                                        </template>

    <script>
    //import layout Admin
    import LayoutAdmin from '../../../Layouts/Admin.vue';

    //import component pagination
    import Pagination from '../../../Components/Pagination.vue';

    //import Heade from Inertia
    import {
        Head,
    } from '@inertiajs/vue3';

    //import sweet alert2
    import Swal from 'sweetalert2';

    //import ref from vue
    import {
        reactive, ref, watch
    } from 'vue';

    //import tinyMCE
    import Editor from '@tinymce/tinymce-vue';

    //import inertia adapter
    import { router } from '@inertiajs/vue3';

    export default {

        // data() {
        //     return {
        //         activeTab: 'popup', // Set the default active tab

        //     };
        // },

        // methods: {
        //     setActiveTab(tabName) {
        //         this.activeTab = tabName;
        //     },
        // },

        //layout
        layout: LayoutAdmin,

        //register components
        components: {
            Head,
            Pagination,
            Editor
        },

        //props
        props: {
            datas: Object,
            errors: Object,
        },

        //inisialisasi composition API
        setup() {
            const activeTab = ref('');

            // State untuk pencarian
            const search = ref(activeTab.value || new URL(document.location).searchParams.get('q'));



            // Daftar tab dalam bentuk array
            const tabs = ref([
                { value: 'popup', label: 'Pop Up' },
                { value: 'siapakita', label: 'Siapa Kita ?' },
                { value: 'ketuaumum', label: 'Ketua Umum' },
                { value: 'strukturorganisasi', label: 'Struktur Organisasi' },
                { value: 'visimisi', label: 'Visi Misi' },
                { value: 'sejarah', label: 'Sejarah' },
                { value: 'peraturan', label: 'Peraturan' },
                { value: 'proker', label: 'Program Kerja' },
                { value: 'dataanggota', label: 'Data Anggota' },
                { value: 'faq', label: 'FAQ' },
                { value: 'kontak', label: 'Kontak Kami' },
            ]);

            // Fungsi untuk mengubah tab dan otomatis melakukan pencarian
            const setActiveTab = (tab) => {
                activeTab.value = tab;
                handleSearch();
            };

            // Fungsi untuk pencarian otomatis
            const handleSearch = () => {
                router.get('/admin/managementfilter', {
                    q: activeTab.value, // Menggunakan activeTab langsung sebagai parameter pencarian
                });
            };

            //define form state
            const form = reactive({
                item: '',
                sub: '',
                subitem: '',
                status: '',
                position: '',
                body: '',
                image: '',
                link: '',
                document: '',
                button: '',
                desc: '',
            });

            const store = () => {
                // Kirim data ke server
                router.post('/admin/management/store', {
                    item: tab,
                    sub: form.sub,
                    subitem: form.subitem,
                    status: form.status,
                    position: form.position,
                    body: form.body,
                    image: form.image,
                    link: form.link,
                    document: form.document,
                    button: form.button,
                    desc: form.desc,
                }, {
                    onSuccess: () => {
                        // Tampilkan alert sukses
                        Swal.fire({
                            title: 'Success!',
                            text: 'Data Anda Berhasil Disimpan.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            // Ambil instance modal
                            const modalElement = document.getElementById('ModalCreate');
                            if (modalElement) {
                                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                                if (modalInstance) {
                                    modalInstance.hide(); // Tutup modal jika instance valid
                                }
                            }
                            router.reload({ preserveState: true });
                        });
                    },
                    onError: (errors) => {
                        console.error("Terjadi kesalahan:", errors);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            };

            //update method
            const update = () => {
                //send data to server
                router.post(`/admin/management/update`, {
                    //data
                    id: form.id,
                    item: form.item,
                    sub: form.sub,
                    subitem: form.subitem,
                    status: form.status,
                    position: form.position,
                    body: form.body,
                    image: form.image,
                    link: form.link,
                    document: form.document,
                    button: form.button,
                    desc: form.desc,
                }, {
                    onSuccess: () => {
                        //show success alert
                        Swal.fire({
                            title: 'Success!',
                            text: 'Data Anda Berhasil Disimpan.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    },
                });
            };

            const active = (data) => {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Mengaktifkan Status!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Approve it!'
                })
                    .then((result) => {
                        if (result.isConfirmed) {
                            router.post(`/admin/management/update/status`, {
                                id: data.id,
                                status: 1,
                                item: data.item,
                            });
                            Swal.fire({
                                title: 'Success!',
                                text: 'Status Active!.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    })
            }

            const nonactive = (data) => {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Menonaktifkan Status!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Approve it!'
                })
                    .then((result) => {
                        if (result.isConfirmed) {
                            router.post(`/admin/management/update/status`, {
                                id: data.id,
                                status: 0,
                                item: data.item,
                            });
                            Swal.fire({
                                title: 'Success!',
                                text: 'Status NonActive!.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    })
            }

            const ModalEdit = (data) => {
                Object.assign(form, {
                    id: data.id,
                    item: data.item,
                    sub: data.sub,
                    subitem: data.subitem,
                    status: data.status,
                    position: data.position,
                    body: data.body,
                    image: data.image,
                    link: data.link,
                    document: data.document,
                    button: data.button,
                    desc: data.desc,
                }); // Isi form dengan data yang dipilih
                const modal = new bootstrap.Modal(document.getElementById('ModalEdit'));
                modal.show();
            };

            const ModalCreate = () => {
                Object.assign(form, {
                    item: '',
                    sub: '',
                    subitem: '',
                    status: '',
                    position: '',
                    body: '',
                    image: '',
                    link: '',
                    document: '',
                    button: '',
                    desc: '',
                }); // Isi form dengan data yang dipilih
                const modal = new bootstrap.Modal(document.getElementById('ModalCreate'));
                modal.show();
            };


            //define method destroy
            const destroy = (id) => {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                })
                    .then((result) => {
                        if (result.isConfirmed) {

                            router.delete(`/admin/management/${id}`);

                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Media Berhasil Dihapus!.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    })
            }

            // Method to update the document file
            const updateDocument = (event) => {
                form.document = event.target.files[0];
            };

            const updateImage = (event) => {
                form.image = event.target.files[0];
            };


            const showImage = (imageName) => {
                return `/storage/${imageName}`;
            };

            const showDoc = (documentName) => {
                return `/storage/${documentName}`;
            };


            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('q'); // Mengambil nilai setelah q=
            //return
            return {
                form,
                store,
                update,
                updateDocument,
                updateImage,
                ModalEdit,
                ModalCreate,
                active,
                nonactive,
                setActiveTab,
                activeTab,
                destroy,
                showImage,
                showDoc,
                search,
                handleSearch,
                tabs,
                urlParams,
                tab

            };
        }



    }

    </script>

    <style></style>
